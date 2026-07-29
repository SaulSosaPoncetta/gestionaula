<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadItem;
use App\Models\ActividadGrupo;
use App\Models\ActividadGrupoAlumno;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\TipoActividad;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ActividadController extends Controller
{
    use DetectaHorarioActivo;

    public function index(Request $request)
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

            // Preseleccionar materia del horario activo si no hay filtro explícito
            if (!$request->filled('materia_id')) {
                $horario = $this->detectarHorarioActivo();
                if ($horario?->materia_id) {
                    $request->merge(['materia_id' => $horario->materia_id]);
                }
            }

            $query = Actividad::with(['materia', 'tipoactividad', 'items'])
                ->where('user_id', auth()->id())
                ->orderBy('numerounidad')
                ->orderBy('numeroactividad');

            if ($request->filled('materia_id')) {
                $query->where('materia_id', $request->materia_id);
            }

            $actividades     = $query->paginate(15);
            $materiaActiva   = $request->get('materia_id');

            return view('actividades.index', compact('actividades', 'materias', 'materiaActiva'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function seleccionar()
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

            $horario         = $this->detectarHorarioActivo();
            $materiaActiva   = $horario?->materia_id;

            return view('actividades.seleccionar', compact('materias', 'materiaActiva'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.seleccionar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'materia_id' => 'required|exists:materias,id',
            ]);

            $materia        = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $tiposactividad = TipoActividad::orderBy('denominacion')->get();

            $contenidos = \App\Models\Contenido::where('user_id', auth()->id())
                ->where('materia_id', $materia->id)
                ->with('subtemas')
                ->orderBy('numerounidad')
                ->orderBy('tema')
                ->get();

            return view('actividades.create', compact('materia', 'tiposactividad', 'contenidos'));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'materia_id'       => 'required|exists:materias,id',
                'tipoactividad_id' => 'required|exists:tiposactividad,id',
                'numerounidad'     => 'required|integer|min:1',
                'tema'             => 'required|string|max:300',
                'subtema'          => 'nullable|string|max:300',
                'numeroactividad'  => 'nullable|integer|min:1',
                'descripcion'      => 'nullable|string',
                'items'            => 'nullable|array',
                'items.*.numero'   => 'required|integer|min:1',
                'items.*.texto'    => 'required|string',
            ]);

            $actividad = Actividad::create([
                'user_id'          => auth()->id(),
                'materia_id'       => $request->materia_id,
                'tipoactividad_id' => $request->tipoactividad_id,
                'titulo'           => $request->tema,
                'numerounidad'     => $request->numerounidad,
                'numeroactividad'  => $request->numeroactividad,
                'tema'             => $request->tema,
                'subtema'          => $request->subtema,
                'descripcion'      => $request->descripcion,
                'estado'           => 'activa',
            ]);

            // Guardar items/consignas
            if ($request->items) {
                foreach ($request->items as $orden => $item) {
                    if (empty(trim($item['texto']))) continue;
                    ActividadItem::create([
                        'actividad_id' => $actividad->id,
                        'numeroitem'   => $item['numero'],
                        'texto'        => $item['texto'],
                        'orden'        => $orden + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('actividades.index', ['materia_id' => $actividad->materia_id])
                             ->with('success', 'Actividad creada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function show(Actividad $actividad)
    {
        try {
            abort_if($actividad->user_id !== auth()->id(), 403);
            $actividad->load(['materia', 'tipoactividad', 'items']);
            return view('actividades.show', compact('actividad'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

public function destroy(Actividad $actividad)
    {
        try {
            abort_if($actividad->user_id !== auth()->id(), 403);
            DB::beginTransaction();
            $materiaId = $actividad->materia_id;
            $actividad->delete();
            DB::commit();
            return redirect()->route('actividades.index', ['materia_id' => $materiaId])
                             ->with('success', 'Actividad eliminada correctamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('ActividadController@destroy BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar la actividad porque tiene registros asociados.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ActividadController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al eliminar.');
        }
    }
}