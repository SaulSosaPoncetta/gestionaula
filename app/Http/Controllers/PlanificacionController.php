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
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar las planificaciones.');
        }
    }

    public function create()
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $ciclo    = date('Y');
            return view('planificaciones.create', compact('materias', 'ciclo'));
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'materia_id'  => 'required|exists:materias,id',
                'ciclo'       => 'required|string|max:20',
                'descripcion' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $planificacion = Planificacion::create([
                'user_id'     => auth()->id(),
                'materia_id'  => $request->materia_id,
                'ciclo'       => $request->ciclo,
                'descripcion' => $request->descripcion,
            ]);

            DB::commit();

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Planificación creada. Ahora agregá las unidades.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@store BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar la planificación.')->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
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
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@show: ' . $e->getMessage());
            return redirect()->route('planificaciones.index')->with('error', 'Planificación no encontrada.');
        }
    }

    public function edit(Planificacion $planificacion)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            return view('planificaciones.edit', compact('planificacion', 'materias'));
        } catch (\Throwable $e) {
            Log::error('PlanificacionController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la planificación.');
        }
    }

    public function update(Request $request, Planificacion $planificacion)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);

            $request->validate([
                'materia_id'  => 'required|exists:materias,id',
                'ciclo'       => 'required|string|max:20',
                'descripcion' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $planificacion->update([
                'materia_id'  => $request->materia_id,
                'ciclo'       => $request->ciclo,
                'descripcion' => $request->descripcion,
            ]);

            DB::commit();

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Planificación actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@update BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar.')->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function destroy(Planificacion $planificacion)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);

            DB::beginTransaction();

            foreach ($planificacion->unidades as $unidad) {
                foreach ($unidad->archivos as $archivo) {
                    Storage::disk('public')->delete($archivo->ruta);
                }
            }
            $planificacion->delete();

            DB::commit();

            return redirect()->route('planificaciones.index')
                             ->with('success', 'Planificación eliminada correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@destroy BD: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la planificación.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function storeUnidad(Request $request, Planificacion $planificacion)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'       => 'required|string|max:300',
                'numeroclases' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

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
                $ord = 1;
                foreach ($request->file('archivos') as $file) {
                    if ($ord > 3) break;
                    $ruta = $file->store("planificaciones/{$planificacion->id}", 'public');
                    UnidadArchivo::create([
                        'unidad_id' => $unidad->id,
                        'nombre'    => $file->getClientOriginalName(),
                        'ruta'      => $ruta,
                        'orden'     => $ord++,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Unidad agregada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@storeUnidad BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar la unidad.')->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@storeUnidad: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function destroyUnidad(Planificacion $planificacion, Unidad $unidad)
    {
        try {
            abort_if($planificacion->user_id !== auth()->id(), 403);

            DB::beginTransaction();

            foreach ($unidad->archivos as $archivo) {
                Storage::disk('public')->delete($archivo->ruta);
            }
            $unidad->delete();

            DB::commit();

            return redirect()->route('planificaciones.show', $planificacion)
                             ->with('success', 'Unidad eliminada correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('PlanificacionController@destroyUnidad BD: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la unidad.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PlanificacionController@destroyUnidad: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}
