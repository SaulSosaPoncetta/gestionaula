<?php
namespace App\Http\Controllers;

use App\Models\PagoOnline;
use App\Models\Suscripcion;
use App\Services\MercadoPagoService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PagoOnlineController extends Controller
{
    public function index()
    {
        try {
            $suscripcion = Suscripcion::where('user_id', auth()->id())
                ->where('estado', 'activa')
                ->latest()
                ->first();

            $pagos = PagoOnline::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('pagos.index', compact('suscripcion', 'pagos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    // ─── MercadoPago ──────────────────────────────────────────

    public function iniciarMP(Request $request)
    {
        try {
            DB::beginTransaction();
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

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@iniciarMP BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@iniciarMP: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function mpSuccess(Request $request)
    {
        try {
            DB::beginTransaction();
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

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@mpSuccess BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@mpSuccess: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function mpFailure(Request $request)
    {
        try {
            DB::beginTransaction();
            PagoOnline::where('preference_id', $request->preference_id)
                ->where('user_id', auth()->id())
                ->update(['estado' => 'rechazado']);

            return redirect()->route('pagos.index')
                ->with('error', 'El pago fue rechazado. Intentá nuevamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@mpFailure BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@mpFailure: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function mpPending(Request $request)
    {
        try {
            return redirect()->route('pagos.index')
                ->with('info', 'Tu pago está pendiente de acreditación. Te notificaremos cuando se confirme.');

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@mpPending: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    // ─── PayPal ───────────────────────────────────────────────

    public function iniciarPaypal(Request $request)
    {
        try {
            DB::beginTransaction();
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

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@iniciarPaypal BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@iniciarPaypal: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function paypalSuccess(Request $request)
    {
        try {
            DB::beginTransaction();
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

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@paypalSuccess BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@paypalSuccess: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function paypalCancel(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($id = session('paypal_pago_id')) {
                PagoOnline::find($id)?->update(['estado' => 'cancelado']);
            }
            return redirect()->route('pagos.index')
                ->with('info', 'Cancelaste el pago con PayPal.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@paypalCancel BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@paypalCancel: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    // ─── Webhooks ─────────────────────────────────────────────

    public function webhookMP(Request $request)
    {
        try {
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

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@webhookMP: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function webhookPaypal(Request $request)
    {
        try {
            DB::beginTransaction();
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

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@webhookPaypal BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PagoOnlineController@webhookPaypal: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}