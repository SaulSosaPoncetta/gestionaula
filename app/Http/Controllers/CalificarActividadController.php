<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadAlumnoEstado;
use App\Models\ActividadAsignacion;
use App\Models\ActividadNota;
use App\Models\ActividadGrupo;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\Horario;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CalificarActividadController extends Controller
{
    use DetectaHorarioActivo;

    public function index()
    {
        try {
            $horario       = $this->detectarHorarioActivo();
            $materiaActiva = $horario?->materia_id;
            $cursoActivo   = $horario?->curso_id;

            $materiaId = request('materia_id') ?: $materiaActiva;

            $materiasEnHorario = Horario::where('user_id', auth()->id())
                ->pluck('materia_id')->unique();

            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get()
                ->sortBy(function ($m) use ($materiaActiva, $materiasEnHorario) {
                    if ($m->id === $materiaActiva)              return '0_'.$m->nombre;
                    if ($materiasEnHorario->contains($m->id))   return '1_'.$m->nombre;
                    return '2_'.$m->nombre;
                })->values();

            $cursos = collect();
            if ($materiaId) {
                $cursos = Horario::with('curso')
                    ->where('user_id', auth()->id())
                    ->where('materia_id', $materiaId)
                    ->get()->pluck('curso')->filter()->unique('id')
                    ->sortBy(fn($c) => $cursoActivo === $c->id ? '0' : '1_'.$c->nombre_completo)
                    ->values();
                if ($cursos->isEmpty()) {
                    $cursos = Curso::where('user_id', auth()->id())
                        ->orderBy('anio')->orderBy('division')->get();
                }
            }

            return view('calificaractividad.index', compact(
                'materias', 'cursos', 'materiaActiva', 'cursoActivo',
                'materiaId', 'materiasEnHorario'
            ));
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el módulo.');
        }
    }

    /**
     * Paso 2: Lista de alumnos con sus actividades asignadas
     */
    public function ver(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

        // Asignaciones activas para esta materia y curso
        $asignaciones = ActividadAsignacion::with([
                'actividad.items',
                'actividad.tipoactividad',
                'actividad.grupos.alumnos',
            ])
            ->where('user_id', auth()->id())
            ->where('materia_id', $request->materia_id)
            ->where('curso_id', $request->curso_id)
            ->where('estado', 'activa')
            ->get();

        // Todos los alumnos del curso ordenados por apellido
        $todosAlumnos = $curso->alumnos->sortBy('apellido');

        // Filtrar por búsqueda de apellido si se especifica
        $buscar = $request->buscar;
        $alumnos = $buscar
            ? $todosAlumnos->filter(fn($a) =>
                str_contains(strtolower($a->apellido), strtolower($buscar)) ||
                str_contains(strtolower($a->nombre),   strtolower($buscar))
              )->values()
            : $todosAlumnos->values();

        // Notas ya registradas
        $notasRegistradas = ActividadNota::where('user_id', auth()->id())
            ->whereIn('asignacion_id', $asignaciones->pluck('id'))
            ->whereIn('alumno_id', $todosAlumnos->pluck('id'))
            ->get()
            ->keyBy(fn($n) => $n->alumno_id . '_' . $n->asignacion_id);

        $hoy = now()->toDateString();

        return view('calificaractividad.ver', compact(
            'materia', 'curso', 'asignaciones', 'alumnos',
            'todosAlumnos', 'notasRegistradas', 'hoy', 'buscar'
        ));
    }

    /**
     * Alias para compatibilidad con la ruta calificaractividad.guardar
     */
    public function guardar(Request $request)
    {
        return $this->calificar($request);
    }

    /**
     * Guardar nota individual o grupal
     */
    public function calificar(Request $request)
    {
        $request->validate([
            'asignacion_id'  => 'required|exists:actividadasignaciones,id',
            'alumno_id'      => 'required|exists:alumnos,id',
            'actividad_id'   => 'required|exists:actividades,id',
            'notaindividual' => 'nullable|numeric|min:1|max:10',
            'notagrupal'     => 'nullable|numeric|min:1|max:10',
            'estado'         => 'required|in:pendiente,enproceso,entregado,vencido',
            'observacion'    => 'nullable|string',
        ]);

        $fechaestado = null;
        if (in_array($request->estado, ['entregado', 'vencido'])) {
            $fechaestado = now()->toDateString();
        }

        ActividadNota::updateOrCreate(
            [
                'asignacion_id' => $request->asignacion_id,
                'alumno_id'     => $request->alumno_id,
                'actividad_id'  => $request->actividad_id,
            ],
            [
                'user_id'        => auth()->id(),
                'notaindividual' => $request->notaindividual,
                'notagrupal'     => $request->notagrupal,
                'estado'         => $request->estado,
                'fechaestado'    => $fechaestado,
                'observacion'    => $request->observacion,
            ]
        );

        return redirect()->back()->with('success', 'Calificación guardada correctamente.');
    }

    /**
     * Ver historial
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

            $registros = ActividadNota::with(['alumno', 'actividad', 'asignacion'])
                ->where('user_id', auth()->id())
                ->whereHas('asignacion', fn($q) =>
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
     * Incompletas
     */
    public function incompletas(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $curso   = Curso::where('user_id', auth()->id())->findOrFail($request->curso_id);

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

            $registros = ActividadNota::with(['alumno', 'actividad'])
                ->where('user_id', auth()->id())
                ->whereHas('asignacion', fn($q) =>
                    $q->where('materia_id', $request->materia_id)
                      ->where('curso_id', $request->curso_id)
                )
                ->whereNotNull('notaindividual')
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        }

        return view('calificaractividad.calificadas', compact(
            'materias', 'cursos', 'registros', 'materia', 'curso'
        ));
    }

    public function showCalificada(ActividadAlumnoEstado $estado)
    {
        abort_if($estado->user_id !== auth()->id(), 403);
        $estado->load(['alumno', 'actividad.materia', 'actividad.curso']);
        return view('calificaractividad.show', compact('estado'));
    }

    public function editCalificada(ActividadAlumnoEstado $estado)
    {
        abort_if($estado->user_id !== auth()->id(), 403);
        $estado->load(['alumno', 'actividad.materia', 'actividad.curso']);
        return view('calificaractividad.edit', compact('estado'));
    }

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