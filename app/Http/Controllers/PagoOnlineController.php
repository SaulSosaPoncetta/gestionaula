<?php
namespace App\Http\Controllers;

use App\Models\PagoOnline;
use App\Models\Suscripcion;
use App\Services\MercadoPagoService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoOnlineController extends Controller
{
    public function index()
    {
        $suscripcion = Suscripcion::where('user_id', auth()->id())
            ->where('estado', 'activa')
            ->latest()
            ->first();

        $pagos = PagoOnline::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pagos.index', compact('suscripcion', 'pagos'));
    }

    // ─── MercadoPago ──────────────────────────────────────────

    public function iniciarMP(Request $request)
    {
        $suscripcion = Suscripcion::where('user_id', auth()->id())
            ->where('estado', 'activa')
            ->latest()->firstOrFail();

        $periododesde = $suscripcion->proximopago ?? now();
        $periodohasta = Carbon::parse($periododesde)->addMonth();

        // Crear registro pendiente
        $pagoOnline = PagoOnline::create([
            'user_id'        => auth()->id(),
            'suscripcion_id' => $suscripcion->id,
            'plataforma'     => 'mercadopago',
            'monto'          => $suscripcion->montomensual,
            'moneda'         => 'ARS',
            'estado'         => 'pendiente',
            'periododesde'   => $periododesde,
            'periodohasta'   => $periodohasta,
        ]);

        $service = new MercadoPagoService();
        $result  = $service->crearPreferencia([
            'suscripcion_id' => $suscripcion->id,
            'pago_online_id' => $pagoOnline->id,
            'monto'          => $suscripcion->montomensual,
            'descripcion'    => $periododesde->format('m/Y'),
            'nombre'         => auth()->user()->name,
            'email'          => auth()->user()->email,
        ]);

        if (isset($result['error'])) {
            return redirect()->route('pagos.index')
                ->with('error', $result['error']);
        }

        $pagoOnline->update(['preference_id' => $result['preference_id']]);

        // En sandbox usar sandbox_init_point, en producción usar init_point
        $url = env('MP_MODE', 'sandbox') === 'production'
            ? $result['init_point']
            : $result['sandbox_init_point'];

        return redirect($url);
    }

    public function mpSuccess(Request $request)
    {
        $pagoOnline = PagoOnline::where('preference_id', $request->preference_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($pagoOnline && $request->status === 'approved') {
            $pagoOnline->update([
                'external_id'      => $request->payment_id,
                'estado'           => 'aprobado',
                'fecha_aprobacion' => now(),
            ]);

            $service = new MercadoPagoService();
            $service->procesarWebhook([
                'type' => 'payment',
                'data' => ['id' => $request->payment_id],
            ]);
        }

        return redirect()->route('pagos.index')
            ->with('success', '¡Pago aprobado correctamente! Tu suscripción fue renovada.');
    }

    public function mpFailure(Request $request)
    {
        PagoOnline::where('preference_id', $request->preference_id)
            ->where('user_id', auth()->id())
            ->update(['estado' => 'rechazado']);

        return redirect()->route('pagos.index')
            ->with('error', 'El pago fue rechazado. Intentá nuevamente.');
    }

    public function mpPending(Request $request)
    {
        return redirect()->route('pagos.index')
            ->with('info', 'Tu pago está pendiente de acreditación. Te notificaremos cuando se confirme.');
    }

    // ─── PayPal ───────────────────────────────────────────────

    public function iniciarPaypal(Request $request)
    {
        $suscripcion = Suscripcion::where('user_id', auth()->id())
            ->where('estado', 'activa')
            ->latest()->firstOrFail();

        $periododesde = $suscripcion->proximopago ?? now();
        $periodohasta = Carbon::parse($periododesde)->addMonth();

        // Convertir ARS a USD aproximado (el docente ve el monto en USD)
        $montoUSD = round($suscripcion->montomensual / 1000, 2); // Ajustar cotización

        $pagoOnline = PagoOnline::create([
            'user_id'        => auth()->id(),
            'suscripcion_id' => $suscripcion->id,
            'plataforma'     => 'paypal',
            'monto'          => $montoUSD,
            'moneda'         => 'USD',
            'estado'         => 'pendiente',
            'periododesde'   => $periododesde,
            'periodohasta'   => $periodohasta,
        ]);

        $service = new PayPalService();
        $result  = $service->crearOrden([
            'pago_online_id' => $pagoOnline->id,
            'monto'          => $montoUSD,
            'descripcion'    => 'Suscripción ' . $periododesde->format('m/Y'),
        ]);

        if (isset($result['error'])) {
            $pagoOnline->delete();
            return redirect()->route('pagos.index')->with('error', $result['error']);
        }

        $pagoOnline->update(['external_id' => $result['order_id']]);
        session(['paypal_pago_id' => $pagoOnline->id]);

        return redirect($result['approve_url']);
    }

    public function paypalSuccess(Request $request)
    {
        $orderId    = $request->token;
        $pagoOnline = PagoOnline::find(session('paypal_pago_id'));

        if (!$pagoOnline) {
            return redirect()->route('pagos.index')->with('error', 'No se encontró el pago.');
        }

        $service = new PayPalService();
        $result  = $service->capturarOrden($orderId);

        if (($result['status'] ?? '') === 'COMPLETED') {
            $pagoOnline->update([
                'estado'           => 'aprobado',
                'fecha_aprobacion' => now(),
                'datos_extra'      => $result,
            ]);
            $service->activarSuscripcion($pagoOnline);

            return redirect()->route('pagos.index')
                ->with('success', '¡Pago con PayPal aprobado! Tu suscripción fue renovada.');
        }

        $pagoOnline->update(['estado' => 'rechazado']);
        return redirect()->route('pagos.index')
            ->with('error', 'El pago no fue completado.');
    }

    public function paypalCancel(Request $request)
    {
        if ($id = session('paypal_pago_id')) {
            PagoOnline::find($id)?->update(['estado' => 'cancelado']);
        }
        return redirect()->route('pagos.index')
            ->with('info', 'Cancelaste el pago con PayPal.');
    }

    // ─── Webhooks ─────────────────────────────────────────────

    public function webhookMP(Request $request)
    {
        // Verificar firma (cuando tengas el webhook secret)
        $secret = env('MP_WEBHOOK_SECRET');
        if ($secret) {
            $xSignature = $request->header('x-signature');
            $xRequestId = $request->header('x-request-id');
            $dataId     = $request->input('data.id');
            $manifest   = "id:{$dataId};request-id:{$xRequestId};ts:" . explode(',', $xSignature)[0] ?? '';
            $hash       = hash_hmac('sha256', $manifest, $secret);

            if (!str_contains($xSignature, $hash)) {
                return response()->json(['error' => 'Firma inválida'], 401);
            }
        }

        $service = new MercadoPagoService();
        $service->procesarWebhook($request->all());

        return response()->json(['status' => 'ok']);
    }

    public function webhookPaypal(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? '';

        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $customId   = $payload['resource']['custom_id'] ?? null;
            $pagoOnline = PagoOnline::find($customId);

            if ($pagoOnline && $pagoOnline->estado !== 'aprobado') {
                $pagoOnline->update([
                    'estado'           => 'aprobado',
                    'fecha_aprobacion' => now(),
                    'datos_extra'      => $payload,
                ]);

                $service = new PayPalService();
                $service->activarSuscripcion($pagoOnline);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}