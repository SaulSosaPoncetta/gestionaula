<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadAlumnoEstado;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use Illuminate\Http\Request;

class CalificarActividadController extends Controller
{
    /**
     * Paso 1: Seleccionar materia y curso
     */
    public function index()
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos   = collect();

        if (request()->filled('materia_id')) {
            $cursos = Curso::where('user_id', auth()->id())
                ->whereHas('materias', fn($q) =>
                    $q->where('materias.id', request('materia_id'))
                )->orderBy('anio')->orderBy('division')->get();

            if ($cursos->isEmpty()) {
                $cursos = Curso::where('user_id', auth()->id())
                    ->orderBy('anio')->orderBy('division')->get();
            }
        }

        return view('calificaractividad.index', compact('materias', 'cursos'));
    }

    /**
     * Paso 2: Lista de alumnos con sus actividades
     */
    public function ver(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

        // Actividades de esta materia y curso
        $actividades = Actividad::where('user_id', auth()->id())
            ->where('materia_id', $request->materia_id)
            ->where('curso_id', $request->curso_id)
            ->where('estado', 'activa')
            ->orderBy('fechainicio')
            ->get();

        // Estados ya registrados
        $estadosRegistrados = ActividadAlumnoEstado::where('user_id', auth()->id())
            ->whereIn('actividad_id', $actividades->pluck('id'))
            ->whereIn('alumno_id', $curso->alumnos->pluck('id'))
            ->get()
            ->groupBy(fn($e) => $e->alumno_id . '_' . $e->actividad_id);

        $alumnos = $curso->alumnos->sortBy('apellido');

        return view('calificaractividad.ver', compact(
            'materia', 'curso', 'actividades', 'alumnos', 'estadosRegistrados'
        ));
    }

    /**
     * Guardar estados y notas
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'materia_id'  => 'required|exists:materias,id',
            'curso_id'    => 'required|exists:cursos,id',
            'registros'   => 'required|array',
        ]);

        foreach ($request->registros as $alumnoId => $actividades) {
            foreach ($actividades as $actividadId => $datos) {
                if (empty($datos['estado'])) continue;

                $fechaestado = null;
                if (in_array($datos['estado'], ['finalizado', 'vencida', 'incompleta'])) {
                    $fechaestado = $datos['fechaestado'] ?? now()->toDateString();
                }

                ActividadAlumnoEstado::updateOrCreate(
                    [
                        'alumno_id'    => $alumnoId,
                        'actividad_id' => $actividadId,
                    ],
                    [
                        'user_id'      => auth()->id(),
                        'estado'       => $datos['estado'],
                        'fechaestado'  => $fechaestado,
                        'nota'         => $datos['nota'] ?? null,
                        'observacion'  => $datos['observacion'] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('calificaractividad.ver', [
            'materia_id' => $request->materia_id,
            'curso_id'   => $request->curso_id,
        ])->with('success', 'Estados y notas guardados correctamente.');
    }

    /**
     * Historial por alumno y actividad
     */
    public function historial(Request $request)
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

        $registros = collect();
        $materia   = null;
        $curso     = null;

        if ($request->filled('materia_id') && $request->filled('curso_id')) {
            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $curso   = Curso::where('user_id', auth()->id())->findOrFail($request->curso_id);

            $registros = ActividadAlumnoEstado::with(['alumno', 'actividad'])
                ->where('user_id', auth()->id())
                ->whereHas('actividad', fn($q) =>
                    $q->where('materia_id', $request->materia_id)
                      ->where('curso_id', $request->curso_id)
                )
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        }

        return view('calificaractividad.historial', compact(
            'materias', 'cursos', 'registros', 'materia', 'curso'
        ));
    }
}