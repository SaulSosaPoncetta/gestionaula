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

    public function ver(Request $request)
{
    $request->validate([
        'materia_id' => 'required|exists:materias,id',
        'curso_id'   => 'required|exists:cursos,id',
    ]);

    $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
    $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

    $actividades = Actividad::where('user_id', auth()->id())
        ->where('materia_id', $request->materia_id)
        ->where('curso_id', $request->curso_id)
        ->where('estado', 'activa')
        ->orderBy('fechainicio')
        ->get();

    $alumnos = $curso->alumnos->sortBy('apellido');

    $todosLosEstados = ActividadAlumnoEstado::where('user_id', auth()->id())
        ->whereIn('actividad_id', $actividades->pluck('id'))
        ->whereIn('alumno_id', $alumnos->pluck('id'))
        ->get()
        ->keyBy(fn($e) => $e->alumno_id . '_' . $e->actividad_id);

    // Solo mostrar combinaciones alumno+actividad que NO están finalizadas/vencidas/incompletas
    $estadosFinales = ['finalizado', 'vencida', 'incompleta'];

    // Filtrar: por alumno mostrar solo actividades pendientes o en proceso
    $estadosRegistrados = $todosLosEstados->filter(
        fn($e) => !in_array($e->estado, $estadosFinales)
    )->keyBy(fn($e) => $e->alumno_id . '_' . $e->actividad_id);

    // IDs de combinaciones ya finalizadas para ocultarlas
    $yaCalificadas = $todosLosEstados->filter(
        fn($e) => in_array($e->estado, $estadosFinales)
    )->map(fn($e) => $e->alumno_id . '_' . $e->actividad_id)->values()->toArray();

    return view('calificaractividad.ver', compact(
        'materia', 'curso', 'actividades', 'alumnos',
        'estadosRegistrados', 'yaCalificadas'
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

/**
 * Vista de entregas incompletas (solo permite cambiar a vencida)
 */
public function incompletas(Request $request)
{
    $request->validate([
        'materia_id' => 'required|exists:materias,id',
        'curso_id'   => 'required|exists:cursos,id',
    ]);

    $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
    $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

    $registros = ActividadAlumnoEstado::with(['alumno', 'actividad'])
        ->where('user_id', auth()->id())
        ->where('estado', 'incompleta')
        ->whereHas('actividad', fn($q) =>
            $q->where('materia_id', $request->materia_id)
              ->where('curso_id', $request->curso_id)
        )
        ->get();

    return view('calificaractividad.incompletas', compact('materia', 'curso', 'registros'));
}

/**
 * Cambiar estado de incompleta a vencida
 */
public function pasarAVencida(Request $request, ActividadAlumnoEstado $estado)
{
    abort_if($estado->user_id !== auth()->id(), 403);
    abort_if($estado->estado !== 'incompleta', 403);

    $estado->update([
        'estado'      => 'vencida',
        'fechaestado' => now()->toDateString(),
    ]);

    return redirect()->back()->with('success', 'Estado actualizado a entrega vencida.');
}

/**
 * Vista de actividades calificadas
 */
public function calificadas(Request $request)
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
            ->whereIn('estado', ['finalizado', 'vencida', 'incompleta'])
            ->whereHas('actividad', fn($q) =>
                $q->where('materia_id', $request->materia_id)
                  ->where('curso_id', $request->curso_id)
            )
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }

    return view('calificaractividad.calificadas', compact(
        'materias', 'cursos', 'registros', 'materia', 'curso'
    ));
}

/**
 * Ver detalle de una calificación
 */
public function showCalificada(ActividadAlumnoEstado $estado)
{
    abort_if($estado->user_id !== auth()->id(), 403);
    $estado->load(['alumno', 'actividad.materia', 'actividad.curso']);
    return view('calificaractividad.show', compact('estado'));
}

/**
 * Editar una calificación
 */
public function editCalificada(ActividadAlumnoEstado $estado)
{
    abort_if($estado->user_id !== auth()->id(), 403);
    $estado->load(['alumno', 'actividad.materia', 'actividad.curso']);
    return view('calificaractividad.edit', compact('estado'));
}

/**
 * Actualizar una calificación
 */
public function updateCalificada(Request $request, ActividadAlumnoEstado $estado)
{
    abort_if($estado->user_id !== auth()->id(), 403);

    $request->validate([
        'estado'      => 'required|in:finalizado,vencida,incompleta',
        'fechaestado' => 'nullable|date',
        'nota'        => 'nullable|numeric|min:0|max:10',
        'observacion' => 'nullable|string',
    ]);

    $estado->update([
        'estado'      => $request->estado,
        'fechaestado' => $request->fechaestado,
        'nota'        => $request->nota,
        'observacion' => $request->observacion,
    ]);

    return redirect()->route('calificaractividad.calificadas', [
        'materia_id' => $estado->actividad->materia_id,
        'curso_id'   => $estado->actividad->curso_id,
    ])->with('success', 'Calificación actualizada correctamente.');
}


}