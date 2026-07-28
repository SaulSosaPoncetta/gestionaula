<?php

namespace App\Http\Controllers;

use App\Models\CicloLectivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CicloLectivoController extends Controller
{
    public function index()
    {
        try {
            $ciclos = CicloLectivo::where('user_id', auth()->id())
                ->orderBy('anio', 'desc')
                ->get();

            $cicloActivo = $ciclos->firstWhere('activo', true);

            return view('ciclos_lectivos.index', compact('ciclos', 'cicloActivo'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $anioSugerido = Carbon::now('America/Argentina/Buenos_Aires')->year;

            // Si ya existe uno para este año, sugerir el siguiente
            $existe = CicloLectivo::where('user_id', auth()->id())
                ->where('anio', $anioSugerido)
                ->exists();

            if ($existe) {
                $anioSugerido++;
            }

            return view('ciclos_lectivos.create', compact('anioSugerido'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'anio'        => 'required|digits:4|min:2000|max:2100',
                'fechainicio' => 'required|date',
                'fechafin'    => 'required|date|after:fechainicio',
            ]);

            // Solo puede haber un ciclo activo por usuario
            if ($request->boolean('activo')) {
                CicloLectivo::where('user_id', auth()->id())
                    ->where('activo', true)
                    ->update(['activo' => false]);
            }

            CicloLectivo::create([
                'user_id'     => auth()->id(),
                'anio'        => $request->anio,
                'fechainicio' => $request->fechainicio,
                'fechafin'    => $request->fechafin,
                'activo'      => $request->boolean('activo', true),
            ]);

            return redirect()->route('ciclos_lectivos.index')
                             ->with('success', "Ciclo lectivo {$request->anio} creado correctamente.");

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Controllers.store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(CicloLectivo $ciclosLectivo)
    {
        try {
            abort_if($ciclosLectivo->user_id !== auth()->id(), 403);
            return view('ciclos_lectivos.edit', ['ciclo' => $ciclosLectivo]);

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, CicloLectivo $ciclosLectivo)
    {
        try {
            DB::beginTransaction();
            abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

            $request->validate([
                'anio'        => 'required|digits:4|min:2000|max:2100',
                'fechainicio' => 'required|date',
                'fechafin'    => 'required|date|after:fechainicio',
            ]);

            if ($request->boolean('activo') && !$ciclosLectivo->activo) {
                CicloLectivo::where('user_id', auth()->id())
                    ->where('id', '!=', $ciclosLectivo->id)
                    ->where('activo', true)
                    ->update(['activo' => false]);
            }

            $ciclosLectivo->update([
                'anio'        => $request->anio,
                'fechainicio' => $request->fechainicio,
                'fechafin'    => $request->fechafin,
                'activo'      => $request->boolean('activo'),
            ]);

            return redirect()->route('ciclos_lectivos.index')
                             ->with('success', "Ciclo lectivo {$request->anio} actualizado correctamente.");

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Controllers.update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function destroy(CicloLectivo $ciclosLectivo)
    {
        try {
            DB::beginTransaction();
            abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

            if ($ciclosLectivo->activo) {
                return back()->with('error', 'No podés eliminar el ciclo lectivo activo. Activá otro primero.');
            }

            $ciclosLectivo->delete();

            return redirect()->route('ciclos_lectivos.index')
                             ->with('success', 'Ciclo lectivo eliminado correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Controllers.destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    /**
     * Genera el siguiente ciclo lectivo basado en el actual.
     */
    public function generarSiguiente(CicloLectivo $ciclosLectivo)
    {
        try {
            abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

            $anioSiguiente = (int) $ciclosLectivo->anio + 1;

            $existe = CicloLectivo::where('user_id', auth()->id())
                ->where('anio', $anioSiguiente)
                ->first();

            if ($existe) {
                return redirect()->route('ciclos_lectivos.index')
                    ->with('info', "Ya existe un ciclo lectivo para el año {$anioSiguiente}.");
            }

            return view('ciclos_lectivos.crear_siguiente', [
                'cicloActual'  => $ciclosLectivo,
                'anioSiguiente' => $anioSiguiente,
            ]);

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.generarSiguiente: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function activar(CicloLectivo $ciclosLectivo)
    {
        try {
            DB::beginTransaction();
            abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

            CicloLectivo::where('user_id', auth()->id())
                ->where('activo', true)
                ->update(['activo' => false]);

            $ciclosLectivo->update(['activo' => true]);

            return redirect()->route('ciclos_lectivos.index')
                             ->with('success', "Ciclo lectivo {$ciclosLectivo->anio} activado.");

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Controllers.activar - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.activar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }
}
