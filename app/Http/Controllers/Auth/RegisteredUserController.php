<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\CicloLectivo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'activo'   => true,
        ]);

        // Asignar rol docente automáticamente
        $user->assignRole('docente');

        // Buscar plan gratuito o de prueba si existe
        $planPrueba = Plan::where('activo', true)
            ->where('precio', 0)
            ->first();

        // Crear suscripción de prueba automáticamente
        // 30 días gratis, sin monto, estado activa
        Suscripcion::create([
            'user_id'       => $user->id,
            'plan_id'       => $planPrueba?->id,
            'montomensual'  => $planPrueba?->precio ?? 0,
            'estado'        => 'activa',
            'fechainicio'   => Carbon::now()->toDateString(),
            'proximopago'   => Carbon::now()->addDays(30)->toDateString(),
            'fechavencimiento' => Carbon::now()->addDays(30)->toDateString(),
            'observaciones' => 'Período de prueba gratuito — 30 días',
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Crear ciclo lectivo para el año actual automáticamente
        CicloLectivo::crearParaUsuario($user->id);

        return redirect(route('dashboard'));
    }
}