<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suscripcion;
use App\Models\Plan;
use App\Models\User;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'plan_id'       => 'nullable|exists:planes,id',
            'montomensual'  => 'required|numeric|min:0',
            'fechainicio'   => 'required|date',
            'proximopago'   => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        // Cancelar suscripción anterior si existe
        Suscripcion::where('user_id', $request->user_id)
            ->where('estado', 'activa')
            ->update(['estado' => 'cancelada']);

        $suscripcion = Suscripcion::create([
            'user_id'       => $request->user_id,
            'plan_id'       => $request->plan_id,
            'montomensual'  => $request->montomensual,
            'estado'        => 'activa',
            'fechainicio'   => $request->fechainicio,
            'proximopago'   => $request->proximopago ?? Carbon::parse($request->fechainicio)->addMonth(),
            'observaciones' => $request->observaciones,
        ]);

        // Generar primer pago pendiente
        Pago::create([
            'user_id'        => $request->user_id,
            'suscripcion_id' => $suscripcion->id,
            'monto'          => $request->montomensual,
            'fechapago'      => $suscripcion->proximopago,
            'periododesde'   => $suscripcion->fechainicio,
            'periodohasta'   => $suscripcion->proximopago,
            'estado'         => 'pendiente',
        ]);

        return redirect()->route('admin.usuario', $request->user_id)
                         ->with('success', 'Suscripción creada correctamente.');
    }

    public function suspender(Suscripcion $suscripcion)
    {
        $suscripcion->update(['estado' => 'suspendida']);
        $suscripcion->user->update(['activo' => false]);
        return redirect()->back()->with('success', 'Suscripción suspendida.');
    }

    public function activar(Suscripcion $suscripcion)
    {
        $suscripcion->update(['estado' => 'activa']);
        $suscripcion->user->update(['activo' => true]);
        return redirect()->back()->with('success', 'Suscripción activada.');
    }
}