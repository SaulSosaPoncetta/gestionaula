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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LandingController extends Controller
{
    public function index()
    {
        try {
            $planes = Plan::where('activo', true)->orderBy('precio')->get();
            return view('landing.index', compact('planes'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function planes()
    {
        try {
            $planes = Plan::where('activo', true)->orderBy('precio')->get();
            return view('landing.planes', compact('planes'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@planes: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function contacto(Request $request)
    {
        try {
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@contacto: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function registroPlan(Request $request)
    {
        try {
            $plan = Plan::findOrFail($request->plan_id);
            return view('landing.registro', compact('plan'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@registroPlan: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function registrarDocente(Request $request)
    {
        try {
            DB::beginTransaction();
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@registrarDocente: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function activar(string $token)
    {
        try {
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

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('LandingController@activar BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@activar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}