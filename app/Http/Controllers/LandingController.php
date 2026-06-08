<?php
namespace App\Http\Controllers;

use App\Mail\ContactoMail;
use App\Mail\BienvenidaMail;
use App\Mail\ActivacionCuentaMail;
use App\Models\Plan;
use App\Models\User;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        $planes = Plan::where('activo', true)->orderBy('precio')->get();
        return view('landing.index', compact('planes'));
    }

    public function planes()
    {
        $planes = Plan::where('activo', true)->orderBy('precio')->get();
        return view('landing.planes', compact('planes'));
    }

    public function contacto(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:200',
            'telefono' => 'nullable|string|max:30',
            'email'    => 'required|email',
            'mensaje'  => 'required|string',
        ]);

        // Enviar confirmación al cliente
        Mail::to($request->email)->send(new ContactoMail(
            $request->nombre,
            $request->email,
            $request->telefono ?? '',
            $request->mensaje
        ));

        return redirect()->route('landing.index')
            ->with('success', 'Mensaje enviado correctamente. Revisá tu correo.');
    }

    public function registroPlan(Request $request)
    {
        $plan = Plan::findOrFail($request->plan_id);
        return view('landing.registro', compact('plan'));
    }

    public function registrarDocente(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed|min:8',
            'plan_id'   => 'required|exists:planes,id',
        ]);

        $plan  = Plan::findOrFail($request->plan_id);
        $token = Str::random(64);

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'activo'           => false, // inactivo hasta activar
            'activation_token' => $token,
            'email_activado'   => false,
        ]);

        $user->assignRole('docente');

        // Crear suscripción pendiente
        Suscripcion::create([
            'user_id'      => $user->id,
            'plan_id'      => $plan->id,
            'montomensual' => $plan->precio,
            'estado'       => 'suspendida', // se activa al confirmar
            'fechainicio'  => now()->toDateString(),
            'proximopago'  => now()->addDays(30)->toDateString(),
            'observaciones'=> 'Registro desde landing — plan: ' . $plan->nombre,
        ]);

        $activationUrl = route('landing.activar', ['token' => $token]);

        // Enviar mail de bienvenida con link de activación
        Mail::to($user->email)->send(new BienvenidaMail($user, $plan, $activationUrl));

        return response()->json([
            'success' => true,
            'nombre'  => $user->name,
            'email'   => $user->email,
            'plan'    => $plan->nombre,
            'precio'  => $plan->precio,
        ]);
    }

    public function activar(string $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('landing.index')
                ->with('error', 'El link de activacion es invalido o ya fue usado.');
        }

        $user->update([
            'activo'           => true,
            'email_activado'   => true,
            'activation_token' => null,
            'email_verified_at'=> now(),
        ]);

        // Activar suscripción
        $suscripcion = $user->suscripcion;
        if ($suscripcion) {
            $suscripcion->update(['estado' => 'activa']);
        }

        // Enviar mail de confirmación
        Mail::to($user->email)->send(new ActivacionCuentaMail($user));

        return redirect()->route('login')
            ->with('success', 'Cuenta activada correctamente. Ya podes iniciar sesion.');
    }
}