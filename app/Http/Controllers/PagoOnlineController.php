<?php
namespace App\Http\Controllers;

use App\Models\PagoOnline;
use App\Models\Suscripcion;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        // ─── MercadoPago (vía hub central MiGestión Panel) ─────────

    public function iniciarMP(Request $request)
    {
        try {
            $response = Http::withHeaders(['X-Api-Key' => config('migestion_hub.api_key')])
                ->timeout(8)
                ->post(rtrim(config('migestion_hub.url'), '/').'/api/generar-cobro', [
                    'referencia_externa' => (string) auth()->id(),
                ]);

            if ($response->failed()) {
                Log::error('PagoOnlineController@iniciarMP hub: '.$response->body());

                return redirect()->route('pagos.index')
                    ->with('error', 'No se pudo generar el link de pago. Contactá al administrador.');
            }

            return redirect($response->json('link_pago'));

        } catch (\Throwable $e) {
            Log::error('PagoOnlineController@iniciarMP: ' . $e->getMessage());
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