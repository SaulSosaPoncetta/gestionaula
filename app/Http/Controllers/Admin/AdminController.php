<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\Plan;
use Carbon\Carbon;

class AdminController extends Controller
{
    

    public function dashboard()
    {
        $totalUsuarios    = User::role('docente')->count();
        $usuariosActivos  = User::role('docente')->where('activo', true)->count();
        $usuariosSuspend  = User::role('docente')->where('activo', false)->count();

        $suscripcionesActivas = Suscripcion::where('estado', 'activa')->count();

        $pagosPendientes = Pago::where('estado', 'pendiente')->count();
        $pagosVencidos   = Pago::where('estado', 'vencido')->count();

        $recaudacionMes  = Pago::where('estado', 'pagado')
            ->whereMonth('fechapago', Carbon::now()->month)
            ->whereYear('fechapago', Carbon::now()->year)
            ->sum('monto');

        $recaudacionTotal = Pago::where('estado', 'pagado')->sum('monto');

        $proximosVencimientos = Suscripcion::with('user')
            ->where('estado', 'activa')
            ->where('proximopago', '<=', Carbon::now()->addDays(7))
            ->orderBy('proximopago')
            ->get();

        $ultimosPagos = Pago::with(['user', 'suscripcion'])
            ->where('estado', 'pagado')
            ->orderBy('fechapago', 'desc')
            ->take(10)
            ->get();

        $usuarios = User::role('docente')
            ->with(['suscripcion.plan'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.dashboard', compact(
            'totalUsuarios', 'usuariosActivos', 'usuariosSuspend',
            'suscripcionesActivas', 'pagosPendientes', 'pagosVencidos',
            'recaudacionMes', 'recaudacionTotal',
            'proximosVencimientos', 'ultimosPagos', 'usuarios'
        ));
    }

    public function toggleActivo(User $user)
    {
        abort_if($user->hasRole('admin'), 403);
        $user->update(['activo' => !$user->activo]);
        $msg = $user->activo ? 'Cuenta activada correctamente.' : 'Cuenta suspendida correctamente.';
        return redirect()->back()->with('success', $msg);
    }

    public function verUsuario(User $user)
    {
        $user->load(['suscripcion.plan', 'pagos' => fn($q) => $q->orderBy('fechapago', 'desc')]);
        $planes = Plan::where('activo', true)->get();
        return view('admin.usuario', compact('user', 'planes'));
    }
}