<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadGrupo;
use App\Models\ActividadGrupoAlumno;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\TipoActividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::with(['materia', 'curso', 'tipoactividad'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('actividades.index', compact('actividades'));
    }

    /**
     * Paso 1: Seleccionar solo materia
     */
    public function seleccionar()
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        return view('actividades.seleccionar', compact('materias'));
    }

    /**
     * Paso 2: Crear la actividad
     */
    public function create(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $materia        = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $tiposactividad = TipoActividad::orderBy('denominacion')->get();

        // Cursos relacionados a esta materia del docente
        $cursos = Curso::where('user_id', auth()->id())
            ->whereHas('materias', fn($q) => $q->where('materias.id', $request->materia_id))
            ->with('alumnos')
            ->orderBy('anio')->orderBy('division')->get();

        if ($cursos->isEmpty()) {
            $cursos = Curso::where('user_id', auth()->id())
                ->with('alumnos')
                ->orderBy('anio')->orderBy('division')->get();
        }

        return view('actividades.create', compact('materia', 'tiposactividad', 'cursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id'          => 'required|exists:materias,id',
            'curso_id'            => 'nullable|exists:cursos,id',
            'tipoactividad_id'    => 'nullable|exists:tiposactividad,id',
            'titulo'              => 'required|string|max:300',
            'tema'                => 'nullable|string|max:300',
            'subtema'             => 'nullable|string|max:300',
            'descripcion'         => 'nullable|string',
            'fechainicio'         => 'required|date',
            'fechaentrega'        => 'required|date|after_or_equal:fechainicio',
            'esgrupal'            => 'nullable|boolean',
            'integrantesporgrupo' => 'nullable|integer|min:2',
            'modogrupo'           => 'nullable|in:aleatorio,manual',
        ]);

        $esgrupal  = $request->boolean('esgrupal');

        $actividad = Actividad::create([
            'user_id'             => auth()->id(),
            'materia_id'          => $request->materia_id,
            'curso_id'            => $request->curso_id,
            'tipoactividad_id'    => $request->tipoactividad_id,
            'titulo'              => $request->titulo,
            'tema'                => $request->tema,
            'subtema'             => $request->subtema,
            'descripcion'         => $request->descripcion,
            'fechainicio'         => $request->fechainicio,
            'fechaentrega'        => $request->fechaentrega,
            'esgrupal'            => $esgrupal,
            'integrantesporgrupo' => $esgrupal ? $request->integrantesporgrupo : null,
            'estado'              => 'activa',
        ]);

        // Procesar grupos solo si hay curso seleccionado
        if ($esgrupal && $request->filled('integrantesporgrupo') && $request->filled('curso_id')) {
            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $alumnos = $curso->alumnos->pluck('id')->toArray();

            if ($request->modogrupo === 'aleatorio') {
                shuffle($alumnos);
                $grupos = array_chunk($alumnos, $request->integrantesporgrupo);
                foreach ($grupos as $i => $miembros) {
                    $grupo = ActividadGrupo::create([
                        'actividad_id' => $actividad->id,
                        'nombre'       => 'Grupo ' . ($i + 1),
                        'numero'       => $i + 1,
                    ]);
                    foreach ($miembros as $alumnoId) {
                        ActividadGrupoAlumno::create([
                            'grupo_id'  => $grupo->id,
                            'alumno_id' => $alumnoId,
                        ]);
                    }
                }
            } elseif ($request->modogrupo === 'manual' && $request->grupos) {
                foreach ($request->grupos as $i => $grupoData) {
                    if (empty($grupoData['alumnos'])) continue;
                    $grupo = ActividadGrupo::create([
                        'actividad_id' => $actividad->id,
                        'nombre'       => 'Grupo ' . ($i + 1),
                        'numero'       => $i + 1,
                    ]);
                    foreach ($grupoData['alumnos'] as $alumnoId) {
                        ActividadGrupoAlumno::create([
                            'grupo_id'  => $grupo->id,
                            'alumno_id' => $alumnoId,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('actividades.show', $actividad)
                         ->with('success', 'Actividad creada correctamente.');
    }

    public function show(Actividad $actividad)
    {
        abort_if($actividad->user_id !== auth()->id(), 403);
        $actividad->load(['materia', 'curso', 'tipoactividad', 'docente', 'grupos.alumnos']);
        return view('actividades.show', compact('actividad'));
    }

    public function destroy(Actividad $actividad)
    {
        abort_if($actividad->user_id !== auth()->id(), 403);
        $actividad->delete();
        return redirect()->route('actividades.index')
                         ->with('success', 'Actividad eliminada correctamente.');
    }
}