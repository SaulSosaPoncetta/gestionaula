<?php
namespace App\Services;

use App\Models\PagoOnline;
use App\Models\Suscripcion;
use Carbon\Carbon;

class PayPalService
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId     = env('PAYPAL_CLIENT_ID', '');
        $this->clientSecret = env('PAYPAL_CLIENT_SECRET', '');
        $this->baseUrl      = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api.paypal.com'
            : 'https://api.sandbox.paypal.com';
    }

    private function getAccessToken(): ?string
    {
        if (empty($this->clientId)) return null;

        $response = \Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        return $response->json('access_token');
    }

    public function crearOrden(array $datos): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['error' => 'PayPal no configurado. Completá las credenciales en .env'];
        }

        $response = \Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value'         => number_format($datos['monto'], 2, '.', ''),
                    ],
                    'description'        => 'Suscripción GestiónAula — ' . $datos['descripcion'],
                    'custom_id'          => (string) $datos['pago_online_id'],
                ]],
                'application_context' => [
                    'return_url' => route('pagos.paypal.success'),
                    'cancel_url' => route('pagos.paypal.cancel'),
                    'brand_name' => 'GestiónAula',
                    'locale'     => 'es-AR',
                ],
            ]);

        $data     = $response->json();
        $approveUrl = collect($data['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        return [
            'order_id'    => $data['id'] ?? null,
            'approve_url' => $approveUrl,
        ];
    }

    public function capturarOrden(string $orderId): array
    {
        $token = $this->getAccessToken();
        if (!$token) return ['error' => 'Sin token'];

        $response = \Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        return $response->json();
    }

    public function activarSuscripcion(PagoOnline $pago): void
{
    $suscripcion = $pago->suscripcion;
    if (!$suscripcion) return;

    $periodicidad = $suscripcion->plan?->periodicidad ?? 'mensual';
    $diasAdicionales = match($periodicidad) {
        'trimestral' => 90,
        'anual'      => 365,
        default      => 30,
    };

    $proximoPago = \Carbon\Carbon::parse($pago->periodohasta)->addDays($diasAdicionales);

    $suscripcion->update([
        'estado'      => 'activa',
        'proximopago' => $proximoPago,
    ]);

    $pago->user->update(['activo' => true]);

    $pagoRegistro = \App\Models\Pago::create([
        'user_id'        => $pago->user_id,
        'suscripcion_id' => $pago->suscripcion_id,
        'monto'          => $pago->monto,
        'fechapago'      => now()->toDateString(),
        'periododesde'   => $pago->periododesde,
        'periodohasta'   => $pago->periodohasta,
        'estado'         => 'pagado',
        'metodopago'     => 'tarjeta',
        'observaciones'  => 'Pago online via PayPal — ID: ' . $pago->external_id,
    ]);

    // Enviar mail de confirmacion
    \Illuminate\Support\Facades\Mail::to($pago->user->email)
        ->send(new \App\Mail\ConfirmacionPagoMail($pagoRegistro->load(['user', 'suscripcion'])));

    // Enviar mail de renovacion automatica
    \Illuminate\Support\Facades\Mail::to($pago->user->email)
        ->send(new \App\Mail\RenovacionAutomaticaMail($pago->load(['user', 'suscripcion'])));
}
}