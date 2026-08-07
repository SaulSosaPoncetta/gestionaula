<?php

namespace App\Http\Controllers;

use App\Models\ActividadAsignacion;
use App\Models\ActividadGrupo;
use App\Models\ActividadGrupoAlumno;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Horario;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;

class AsignarActividadNuevoController extends Controller
{
    use DetectaHorarioActivo;

    public function index()
    {
        $horario       = $this->detectarHorarioActivo();
        $cursoActivo   = $horario?->curso_id;
        $materiaActiva = $horario?->materia_id;

        // Preseleccionar curso activo si no hay filtro
        $cursoId = request('curso_id') ?: $cursoActivo;

        // Cursos ordenados: activo primero, luego los del horario, luego el resto
        $cursosEnHorario = Horario::where('user_id', auth()->id())
            ->pluck('curso_id')->unique();

        $cursos = Curso::where('user_id', auth()->id())
            ->orderBy('anio')->orderBy('division')->get()
            ->sortBy(function ($c) use ($cursoActivo, $cursosEnHorario) {
                if ($c->id === $cursoActivo)              return '0_' . $c->nombre_completo;
                if ($cursosEnHorario->contains($c->id))   return '1_' . $c->nombre_completo;
                return '2_' . $c->nombre_completo;
            })->values();

        // Materias filtradas por curso vía horarios, activa primero
        $materias = collect();
        if ($cursoId) {
            $materias = Horario::with('materia')
                ->where('user_id', auth()->id())
                ->where('curso_id', $cursoId)
                ->get()
                ->pluck('materia')->filter()->unique('id')
                ->sortBy(fn($m) => $materiaActiva === $m->id ? '0' : '1_'.$m->nombre)
                ->values();

            if ($materias->isEmpty()) {
                $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            }
        }

        return view('asignarnuevo.index', compact(
            'cursos', 'materias', 'cursoActivo', 'materiaActiva', 'cursoId', 'cursosEnHorario'
        ));
    }

    /**
     * Paso 2: Ver actividades disponibles para asignar
     */
    public function ver(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);

        // Actividades de esta materia
        $actividades = Actividad::with(['tipoactividad', 'items'])
            ->where('user_id', auth()->id())
            ->where('materia_id', $request->materia_id)
            ->where('estado', 'activa')
            ->orderBy('numerounidad')
            ->orderBy('numeroactividad')
            ->get();

        // Asignaciones ya existentes para este curso y materia
        $yaAsignadas = ActividadAsignacion::where('user_id', auth()->id())
            ->where('curso_id', $request->curso_id)
            ->where('materia_id', $request->materia_id)
            ->pluck('actividad_id')
            ->toArray();

        $alumnos = $curso->alumnos->sortBy('apellido');

        return view('asignarnuevo.ver', compact(
            'curso', 'materia', 'actividades', 'yaAsignadas', 'alumnos'
        ));
    }

    /**
     * Guardar asignación
     */
    public function asignar(Request $request)
    {
        $request->validate([
            'actividad_id'        => 'required|exists:actividades,id',
            'curso_id'            => 'required|exists:cursos,id',
            'materia_id'          => 'required|exists:materias,id',
            'fechainicio'         => 'required|date',
            'fechaentrega'        => 'required|date|after_or_equal:fechainicio',
            'esgrupal'            => 'nullable|boolean',
            'integrantesporgrupo' => 'nullable|integer|min:2',
            'modogrupo'           => 'nullable|in:aleatorio,manual',
        ]);

        $esgrupal = $request->boolean('esgrupal');

        // Verificar si ya está asignada
        $existe = ActividadAsignacion::where('actividad_id', $request->actividad_id)
            ->where('curso_id', $request->curso_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Esta actividad ya está asignada a este curso.');
        }

        $asignacion = ActividadAsignacion::create([
            'actividad_id'        => $request->actividad_id,
            'curso_id'            => $request->curso_id,
            'materia_id'          => $request->materia_id,
            'user_id'             => auth()->id(),
            'fechainicio'         => $request->fechainicio,
            'fechaentrega'        => $request->fechaentrega,
            'esgrupal'            => $esgrupal,
            'integrantesporgrupo' => $esgrupal ? $request->integrantesporgrupo : null,
            'modogrupo'           => $esgrupal ? $request->modogrupo : null,
            'estado'              => 'activa',
        ]);

        // Procesar grupos si es grupal
        if ($esgrupal && $request->filled('integrantesporgrupo')) {
            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $alumnos = $curso->alumnos->pluck('id')->toArray();

            if ($request->modogrupo === 'aleatorio') {
                shuffle($alumnos);
                $grupos = array_chunk($alumnos, $request->integrantesporgrupo);
                foreach ($grupos as $i => $miembros) {
                    $grupo = ActividadGrupo::create([
                        'actividad_id' => $request->actividad_id,
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
                        'actividad_id' => $request->actividad_id,
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

        return redirect()->route('asignarnuevo.ver', [
            'curso_id'   => $request->curso_id,
            'materia_id' => $request->materia_id,
        ])->with('success', 'Actividad asignada correctamente.');
    }
}