<?php

namespace App\Http\Controllers;

use App\Models\Cese;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CeseController extends Controller
{
    public function index()
    {
        try {
            $ceses = Cese::with(['materia', 'establecimiento'])
                ->where('user_id', auth()->id())
                ->orderBy('fechacese', 'desc')
                ->paginate(15);

            return view('ceses.index', compact('ceses'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CeseController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $materias         = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
            $horarios         = Horario::with(['materia', 'curso', 'establecimiento'])
                ->where('user_id', auth()->id())
                ->orderByRaw("FIELD(dia, 'lunes','martes','miercoles','jueves','viernes','sabado','domingo')")
                ->orderBy('horainicio')
                ->get();

            return view('ceses.create', compact('materias', 'establecimientos', 'horarios'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CeseController@create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'materia_id'          => 'required|exists:materias,id',
                'establecimiento_id'  => 'required|exists:establecimientos,id',
                'horario_id'          => 'nullable|exists:horarios,id',
                'fechatomapossesion'  => 'required|date',
                'fechacese'           => 'required|date|after_or_equal:fechatomapossesion',
                'numerosecuencia'     => 'nullable|string|max:50',
            ]);

            // Guardar datos del horario antes de eliminarlo
            $dia        = null;
            $horainicio = null;
            $horafin    = null;

            if ($request->filled('horario_id')) {
                $horario = Horario::where('user_id', auth()->id())
                                  ->find($request->horario_id);

                if ($horario) {
                    $dia        = $horario->dia;
                    $horainicio = $horario->horainicio;
                    $horafin    = $horario->horafin;

                    // Eliminar el horario
                    $horario->delete();
                }
            }

            Cese::create([
                'user_id'            => auth()->id(),
                'materia_id'         => $request->materia_id,
                'establecimiento_id' => $request->establecimiento_id,
                'horario_id'         => null, // ya fue eliminado
                'fechatomapossesion' => $request->fechatomapossesion,
                'fechacese'          => $request->fechacese,
                'numerosecuencia'    => $request->numerosecuencia,
                'dia'                => $dia,
                'horainicio'         => $horainicio,
                'horafin'            => $horafin,
            ]);

            return redirect()->route('ceses.index')
                             ->with('success', 'Cese registrado y horario eliminado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('CeseController@store BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CeseController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function destroy(Cese $cese)
    {
        try {
            abort_if($cese->user_id !== auth()->id(), 403);
            $cese->delete();
            return redirect()->route('ceses.index')
                             ->with('success', 'Cese eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('CeseController@destroy BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CeseController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}