<?php
namespace App\Services;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use App\Models\PagoOnline;
use App\Models\Suscripcion;
use Carbon\Carbon;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }

    public function crearPreferencia(array $datos): array
    {
        $client = new PreferenceClient();

        $preference = $client->create([
            'items' => [
                [
                    'id'          => 'suscripcion_' . $datos['suscripcion_id'],
                    'title'       => 'Suscripción GestiónAula — ' . $datos['descripcion'],
                    'quantity'    => 1,
                    'unit_price'  => (float) $datos['monto'],
                    'currency_id' => 'ARS',
                ]
            ],
            'payer' => [
                'name'  => $datos['nombre'],
                'email' => $datos['email'],
            ],
            'back_urls' => [
                'success' => route('pagos.mp.success'),
                'failure' => route('pagos.mp.failure'),
                'pending' => route('pagos.mp.pending'),
            ],
            'auto_return'          => 'approved',
            'notification_url'     => route('webhooks.mercadopago'),
            'external_reference'   => $datos['pago_online_id'],
            'statement_descriptor' => 'GestionAula',
        ]);

        return [
            'preference_id' => $preference->id,
            'init_point'    => $preference->init_point,
            'sandbox_init_point' => $preference->sandbox_init_point,
        ];
    }

    public function procesarWebhook(array $payload): bool
    {
        if (($payload['type'] ?? '') !== 'payment') return false;

        $paymentId = $payload['data']['id'] ?? null;
        if (!$paymentId) return false;

        // Buscar el pago en nuestra BD
        $client  = new \MercadoPago\Client\Payment\PaymentClient();
        $payment = $client->get($paymentId);

        $pagoOnline = PagoOnline::where('preference_id', $payment->preference_id)
            ->orWhere('external_id', $paymentId)
            ->first();

        if (!$pagoOnline) return false;

        $pagoOnline->update([
            'external_id'      => $paymentId,
            'estado'           => $this->mapearEstado($payment->status),
            'metodo_pago'      => $payment->payment_method_id ?? null,
            'fecha_aprobacion' => $payment->status === 'approved' ? now() : null,
            'datos_extra'      => [
                'status_detail' => $payment->status_detail,
                'payment_type'  => $payment->payment_type_id,
            ],
        ]);

        if ($payment->status === 'approved') {
            $this->activarSuscripcion($pagoOnline);
        }

        return true;
    }

    private function mapearEstado(string $estado): string
    {
        return match($estado) {
            'approved'     => 'aprobado',
            'rejected'     => 'rechazado',
            'cancelled'    => 'cancelado',
            'refunded'     => 'reembolsado',
            default        => 'pendiente',
        };
    }

    private function activarSuscripcion(PagoOnline $pago): void
    {
        $suscripcion = $pago->suscripcion;
        if (!$suscripcion) return;

        $proximoPago = Carbon::parse($pago->periodohasta)->addMonth();

        $suscripcion->update([
            'estado'      => 'activa',
            'proximopago' => $proximoPago,
        ]);

        $pago->user->update(['activo' => true]);

        // Registrar en tabla pagos también
        \App\Models\Pago::create([
            'user_id'        => $pago->user_id,
            'suscripcion_id' => $pago->suscripcion_id,
            'monto'          => $pago->monto,
            'fechapago'      => now()->toDateString(),
            'periododesde'   => $pago->periododesde,
            'periodohasta'   => $pago->periodohasta,
            'estado'         => 'pagado',
            'metodopago'     => 'transferencia',
            'observaciones'  => 'Pago online via MercadoPago — ID: ' . $pago->external_id,
        ]);
    }
}