<?php

namespace App\Http\Controllers;

use App\Models\Planificacion;
use App\Models\Unidad;
use App\Models\UnidadObjetivoAprendizaje;
use App\Models\UnidadObjetivoEnsenianza;
use App\Models\UnidadActividad;
use App\Models\UnidadRecurso;
use App\Models\UnidadArchivo;
use App\Models\Materia;
use App\Models\Contenido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PlanificacionController extends Controller
{
    public function index()
    {
        try {
            $planificaciones = Planificacion::with(['materia', 'unidades'])
                ->where('user_id', auth()->id())
                ->withCount('unidades')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('planificaciones.index', compact('planificaciones'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $ciclo    = date('Y');
            return view('planificaciones.create', compact('materias', 'ciclo'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'materia_id'  => 'required|exists:materias,id',
                'ciclo'       => 'required|string|max:20',
                'descripcion' => 'nullable|string',
            ]);

            $planificacion = Planificacion::create([
                'user_id'     => auth()->id(),
                'materia_id'  => $request->materia_id,
                'ciclo'       => $request->ciclo,
                'descripcion' => $request->descripcion,
            ]);

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Planificación creada. Ahora agregá las unidades.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();            Log::error('PlanificacionController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function show(Planificacion $planificacion)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);

            $planificacion->load([
                'materia',
                'unidades.objetivosaprendizaje',
                'unidades.objetivosensenianza',
                'unidades.actividades',
                'unidades.recursos',
                'unidades.archivos',
                'unidades.contenidos',
            ]);

            $contenidos = Contenido::where('user_id', auth()->id())
                ->with('materia')
                ->orderBy('fecha', 'desc')
                ->get();

            return view('planificaciones.show', compact('planificacion', 'contenidos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function edit(Planificacion $planificacion)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            return view('planificaciones.edit', compact('planificacion', 'materias'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, Planificacion $planificacion)
    {
        try {
            DB::beginTransaction();
            abort_if($planificacion->user_id !== auth()->id(), 403);

            $request->validate([
                'materia_id'  => 'required|exists:materias,id',
                'ciclo'       => 'required|string|max:20',
                'descripcion' => 'nullable|string',
            ]);

            $planificacion->update([
                'materia_id'  => $request->materia_id,
                'ciclo'       => $request->ciclo,
                'descripcion' => $request->descripcion,
            ]);

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Planificación actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@update BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function destroy(Planificacion $planificacion)
    {
        try {
            DB::beginTransaction();
            abort_if($planificacion->user_id !== auth()->id(), 403);

            foreach ($planificacion->unidades as $unidad) {
                foreach ($unidad->archivos as $archivo) {
                    Storage::disk('public')->delete($archivo->ruta);
                }
            }
            $planificacion->delete();

            return redirect()->route('planificaciones.index')
                             ->with('success', 'Planificación eliminada correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@destroy BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function storeUnidad(Request $request, Planificacion $planificacion)
    {
        try {
            DB::beginTransaction();
            abort_if($planificacion->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'       => 'required|string|max:300',
                'numeroclases' => 'required|integer|min:1',
            ]);

            $orden  = $planificacion->unidades()->count() + 1;
            $unidad = Unidad::create([
                'planificacion_id' => $planificacion->id,
                'nombre'           => $request->nombre,
                'numeroclases'     => $request->numeroclases,
                'orden'            => $orden,
            ]);

            foreach ((array)$request->objetivosaprendizaje as $i => $obj) {
                if (!empty(trim($obj))) {
                    UnidadObjetivoAprendizaje::create([
                        'unidad_id' => $unidad->id,
                        'objetivo'  => $obj,
                        'orden'     => $i + 1,
                    ]);
                }
            }

            foreach ((array)$request->objetivosensenianza as $i => $obj) {
                if (!empty(trim($obj))) {
                    UnidadObjetivoEnsenianza::create([
                        'unidad_id' => $unidad->id,
                        'objetivo'  => $obj,
                        'orden'     => $i + 1,
                    ]);
                }
            }

            foreach ((array)$request->actividades as $i => $act) {
                if (!empty(trim($act))) {
                    UnidadActividad::create([
                        'unidad_id' => $unidad->id,
                        'actividad' => $act,
                        'orden'     => $i + 1,
                    ]);
                }
            }

            foreach ((array)$request->recursos as $i => $rec) {
                if (!empty(trim($rec))) {
                    UnidadRecurso::create([
                        'unidad_id' => $unidad->id,
                        'recurso'   => $rec,
                        'orden'     => $i + 1,
                    ]);
                }
            }

            if ($request->contenidos) {
                $unidad->contenidos()->sync($request->contenidos);
            }

            if ($request->hasFile('archivos')) {
                $orden = 1;
                foreach ($request->file('archivos') as $file) {
                    if ($orden > 3) break;
                    $ruta = $file->store("planificaciones/{$planificacion->id}", 'public');
                    UnidadArchivo::create([
                        'unidad_id' => $unidad->id,
                        'nombre'    => $file->getClientOriginalName(),
                        'ruta'      => $ruta,
                        'orden'     => $orden++,
                    ]);
                }
            }

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Unidad agregada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();            Log::error('PlanificacionController@storeUnidad: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function destroyUnidad(Planificacion $planificacion, Unidad $unidad)
    {
        try {
            DB::beginTransaction();
            abort_if($planificacion->user_id !== auth()->id(), 403);

            foreach ($unidad->archivos as $archivo) {
                Storage::disk('public')->delete($archivo->ruta);
            }
            $unidad->delete();

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Unidad eliminada correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();            Log::error('PlanificacionController@destroyUnidad BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();            Log::error('PlanificacionController@destroyUnidad: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}