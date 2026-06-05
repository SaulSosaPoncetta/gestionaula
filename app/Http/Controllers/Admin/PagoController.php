<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Suscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{

    public function index(Request $request)
    {
        $query = Pago::with(['user', 'suscripcion'])
            ->orderBy('fechapago', 'desc');

        if ($request->filled('estado'))  $query->where('estado', $request->estado);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);

        $pagos = $query->paginate(20);

        return view('admin.pagos.index', compact('pagos'));
    }

    public function registrarPago(Request $request)
    {
        $request->validate([
            'pago_id'       => 'required|exists:pagos,id',
            'metodopago'    => 'required|in:efectivo,transferencia,tarjeta,otro',
            'fechapago'     => 'required|date',
            'comprobante'   => 'nullable|file|max:5120',
            'observaciones' => 'nullable|string',
        ]);

        $pago = Pago::findOrFail($request->pago_id);
        $comprobante = $pago->comprobante;

        if ($request->hasFile('comprobante')) {
            $comprobante = $request->file('comprobante')
                ->store('comprobantes', 'public');
        }

        $pago->update([
            'estado'        => 'pagado',
            'metodopago'    => $request->metodopago,
            'fechapago'     => $request->fechapago,
            'comprobante'   => $comprobante,
            'observaciones' => $request->observaciones,
        ]);

        // Generar próximo pago pendiente
        $suscripcion = $pago->suscripcion;
        $proximaFecha = Carbon::parse($pago->periodohasta)->addMonth();

        Pago::create([
            'user_id'        => $pago->user_id,
            'suscripcion_id' => $suscripcion->id,
            'monto'          => $suscripcion->montomensual,
            'fechapago'      => $proximaFecha,
            'periododesde'   => $pago->periodohasta,
            'periodohasta'   => $proximaFecha,
            'estado'         => 'pendiente',
        ]);

        // Actualizar próximo pago en suscripción
        $suscripcion->update(['proximopago' => $proximaFecha]);

        return redirect()->back()->with('success', 'Pago registrado. Se generó el próximo período automáticamente.');
    }

    public function marcarVencido(Pago $pago)
    {
        $pago->update(['estado' => 'vencido']);
        return redirect()->back()->with('success', 'Pago marcado como vencido.');
    }

    public function generarPago(Request $request)
    {
        $request->validate([
            'suscripcion_id' => 'required|exists:suscripciones,id',
            'monto'          => 'required|numeric|min:0',
            'periododesde'   => 'required|date',
            'periodohasta'   => 'required|date',
            'fechapago'      => 'required|date',
        ]);

        $suscripcion = Suscripcion::findOrFail($request->suscripcion_id);

        Pago::create([
            'user_id'        => $suscripcion->user_id,
            'suscripcion_id' => $suscripcion->id,
            'monto'          => $request->monto,
            'fechapago'      => $request->fechapago,
            'periododesde'   => $request->periododesde,
            'periodohasta'   => $request->periodohasta,
            'estado'         => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Pago generado correctamente.');
    }
}