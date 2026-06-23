<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\CalendarioEscolar;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    use DetectaHorarioActivo;

    public function index()
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        $horarioActivo = $this->detectarHorarioActivo();

        $materiaIdDefault = request('materia_id', $horarioActivo?->materia_id);

        // El curso del horario activo solo aplica como default si la materia
        // seleccionada sigue siendo la del horario activo (o no se eligió ninguna).
        $usandoMateriaDelHorario = !request()->filled('materia_id')
            || (string) request('materia_id') === (string) $horarioActivo?->materia_id;

        $cursoIdDefault = request('curso_id', $usandoMateriaDelHorario ? $horarioActivo?->curso_id : null);

        // Los cursos siempre deben estar disponibles en el select,
        // haya o no una clase activa en este momento.
        $cursos = Curso::where('user_id', auth()->id())
            ->orderBy('anio')->orderBy('division')->get();

        return view('asistencia.index', compact(
            'materias', 'cursos', 'materiaIdDefault', 'cursoIdDefault', 'horarioActivo'
        ));
    }

    public function accion(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);

        return view('asistencia.accion', compact('curso', 'materia'));
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
            'fecha'      => 'required|date',
        ]);

        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $fecha   = $request->fecha;

        $asistencias = Asistencia::where('curso_id', $request->curso_id)
            ->where('fecha', $fecha)
            ->where('materia_id', $request->materia_id)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('alumno_id');

        return view('asistencia.registrar', compact('curso', 'materia', 'fecha', 'asistencias'));
    }

public function guardar(Request $request)
{
    $request->validate([
        'curso_id'    => 'required|exists:cursos,id',
        'materia_id'  => 'required|exists:materias,id',
        'fecha'       => 'required|date',
        'asistencias' => 'required|array',
    ]);

    // Verificar si la fecha es feriado en el calendario escolar
    $esFeriado = \App\Models\CalendarioEscolar::where('user_id', auth()->id())
        ->where('esferiado', true)
        ->where(function($q) use ($request) {
            $q->where('fecha', $request->fecha)
              ->orWhere(function($q2) use ($request) {
                  $q2->where('fechainicio', '<=', $request->fecha)
                     ->where('fechafin', '>=', $request->fecha);
              });
        })
        ->first();

    foreach ($request->asistencias as $alumnoId => $datos) {
        $estado      = $datos['estado'] ?? 'presente';
        $horallegada = null;
        $fotoruta    = null;
        $observacion = $datos['observacion'] ?? null;

        // Si es feriado, agregar nota a la observacion
        if ($esFeriado) {
            $notaFeriado = 'Feriado: ' . $esFeriado->denominacion;
            $observacion = $observacion
                ? $notaFeriado . ' — ' . $observacion
                : $notaFeriado;
        }

        if ($estado === 'tarde' && !empty($datos['horallegada'])) {
            $horallegada = $datos['horallegada'];
        }

        if ($estado === 'ausente' && $request->hasFile("fotos.{$alumnoId}")) {
            $file     = $request->file("fotos.{$alumnoId}");
            $fotoruta = $file->store("justificaciones/{$request->fecha}", 'public');
            $estado   = 'justificado';
        }

        Asistencia::updateOrCreate(
            [
                'alumno_id'  => $alumnoId,
                'fecha'      => $request->fecha,
                'materia_id' => $request->materia_id,
            ],
            [
                'curso_id'          => $request->curso_id,
                'user_id'           => auth()->id(),
                'estado'            => $estado,
                'horallegada'       => $horallegada,
                'fotojustificacion' => $fotoruta,
                'observacion'       => $observacion,
            ]
        );

        \App\Services\AsistenciaService::actualizarPorcentaje($alumnoId, $request->materia_id);
    }

    $msg = 'Asistencia guardada correctamente.';
    if ($esFeriado) {
        $msg .= ' (Fecha marcada como feriado: ' . $esFeriado->denominacion . ')';
    }

    return redirect()->route('asistencia.registrar', [
        'curso_id'   => $request->curso_id,
        'materia_id' => $request->materia_id,
        'fecha'      => $request->fecha,
    ])->with('success', $msg);
}

public function listado(Request $request)
{
    $request->validate([
        'curso_id'   => 'required|exists:cursos,id',
        'materia_id' => 'required|exists:materias,id',
    ]);

    $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
    $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);

    $registros = Asistencia::with('alumno')
        ->where('curso_id', $request->curso_id)
        ->where('materia_id', $request->materia_id)
        ->where('user_id', auth()->id())
        ->orderBy('fecha', 'desc')
        ->get();

    $resumenAlumnos = $curso->alumnos->sortBy('apellido')->map(function($alumno) use ($registros, $materia) {
        $regs       = $registros->where('alumno_id', $alumno->id);
        $total      = $regs->count();
        $presentes  = $regs->whereIn('estado', ['presente', 'tarde', 'justificado'])->count();
        $porcentaje = $total > 0 ? round(($presentes / $total) * 100, 2) : 100;

        // Calcular color de alerta
        $color = \App\Services\AsistenciaService::colorAlerta(
            $porcentaje,
            $materia->porcentajelimite ?? 75,
            $total,
            $materia->cantidadclasesanuales ?? 0
        );

        return [
            'alumno'      => $alumno,
            'presente'    => $presentes,
            'ausente'     => $regs->where('estado', 'ausente')->count(),
            'tarde'       => $regs->where('estado', 'tarde')->count(),
            'justificado' => $regs->where('estado', 'justificado')->count(),
            'total'       => $total,
            'porcentaje'  => $porcentaje,
            'color'       => $color,
        ];
    });

    return view('asistencia.listado', compact('curso', 'materia', 'resumenAlumnos'));
}

    public function historial(Request $request)
{
    $materias    = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
    $cursos      = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
    $asistencias = collect();
    $filtros     = [];
    $resumen     = [];
    $alumnos     = collect();

    // Detectar clase activa para preseleccionar
    $horarioActivo = $this->detectarHorarioActivo();

    $cursoIdDefault   = $request->get('curso_id',   $horarioActivo?->curso_id);
    $materiaIdDefault = $request->get('materia_id', $horarioActivo?->materia_id);

    // Si vienen del horario y no hay filtros explícitos, precargar datos
    if (!$request->filled('curso_id') && $cursoIdDefault) {
        $request->merge([
            'curso_id'   => $cursoIdDefault,
            'materia_id' => $materiaIdDefault,
        ]);
    }

    if ($request->filled('curso_id')) {
        $filtros = $request->only(['curso_id', 'materia_id', 'fechainicio', 'fechafin', 'alumno_id']);

        $query = Asistencia::with(['alumno', 'materia', 'curso'])
            ->where('user_id', auth()->id())
            ->where('curso_id', $request->curso_id);

        if ($request->filled('materia_id'))   $query->where('materia_id', $request->materia_id);
        if ($request->filled('alumno_id'))    $query->where('alumno_id', $request->alumno_id);
        if ($request->filled('fechainicio'))  $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))     $query->where('fecha', '<=', $request->fechafin);

        $asistencias = $query->orderBy('alumno_id')->orderBy('fecha', 'desc')->get();

        $resumen = [
            'presente'    => $asistencias->where('estado', 'presente')->count(),
            'ausente'     => $asistencias->where('estado', 'ausente')->count(),
            'tarde'       => $asistencias->where('estado', 'tarde')->count(),
            'justificado' => $asistencias->where('estado', 'justificado')->count(),
            'total'       => $asistencias->count(),
        ];

        $asistencias = $asistencias->groupBy('alumno_id');

        $alumnos = Alumno::where('user_id', auth()->id())
            ->where('curso_id', $request->curso_id)
            ->orderBy('apellido')->get();
    }

    return view('asistencia.historial', compact(
        'materias', 'cursos', 'asistencias', 'filtros',
        'resumen', 'alumnos', 'cursoIdDefault', 'materiaIdDefault'
    ));
}

    public function editarRegistro(Asistencia $asistencia)
    {
        abort_if($asistencia->user_id !== auth()->id(), 403);
        $asistencia->load(['alumno', 'materia', 'curso']);
        return view('asistencia.editar', compact('asistencia'));
    }

public function actualizarRegistro(Request $request, Asistencia $asistencia)
{
    abort_if($asistencia->user_id !== auth()->id(), 403);

    $request->validate([
        'estado'      => 'required|in:presente,ausente,tarde,justificado',
        'horallegada' => 'nullable|date_format:H:i',
        'observacion' => 'nullable|string',
    ]);

    $horallegada = null;
    $fotoruta    = $asistencia->fotojustificacion;

    if ($request->estado === 'tarde' && $request->filled('horallegada')) {
        $horallegada = $request->horallegada;
    }

    if ($request->estado === 'ausente' && $request->hasFile('fotojustificacion')) {
        if ($asistencia->fotojustificacion) {
            Storage::disk('public')->delete($asistencia->fotojustificacion);
        }
        $fotoruta = $request->file('fotojustificacion')
            ->store('justificaciones/' . $asistencia->fecha->format('Y-m-d'), 'public');
        $request->merge(['estado' => 'justificado']);
    }

    $asistencia->update([
        'estado'            => $request->estado,
        'horallegada'       => $horallegada,
        'fotojustificacion' => $fotoruta,
        'observacion'       => $request->observacion,
    ]);

    \App\Services\AsistenciaService::actualizarPorcentaje(
        $asistencia->alumno_id,
        $asistencia->materia_id
    );

    return redirect()->route('asistencia.alumno', array_filter([
        'alumno_id'  => $asistencia->alumno_id,
        'materia_id' => $request->filtro_materia_id,
        'curso_id'   => $request->filtro_curso_id,
    ]))->with('success', 'Asistencia actualizada correctamente.');
}

    public function alumno(Request $request)
{
    $alumnos  = collect();
    $alumno   = null;
    $resumen  = [];
    $detalle  = collect();
    $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

    // Si viene alumno_id directo, cargarlo sin buscar
    if ($request->filled('alumno_id')) {
        $alumno = Alumno::where('user_id', auth()->id())
            ->with('curso')
            ->find($request->alumno_id);
    }

    // Si viene búsqueda por texto y no hay alumno_id
    if (!$alumno && $request->filled('buscar')) {
        $alumnos = Alumno::with('curso')
            ->where('user_id', auth()->id())
            ->where(function ($q) use ($request) {
                $q->where('apellido', 'like', '%' . $request->buscar . '%')
                  ->orWhere('nombre',   'like', '%' . $request->buscar . '%');
                 
            })
            ->orderBy('apellido')
            ->get();

        // Si hay un solo resultado, usarlo directamente
        if ($alumnos->count() === 1) {
            $alumno  = $alumnos->first();
            $alumnos = collect();
        }
    }

    if ($alumno) {
        $query = Asistencia::with(['materia', 'curso'])
            ->where('alumno_id', $alumno->id)
            ->where('user_id', auth()->id());

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        $registros = $query->orderBy('fecha', 'desc')->get();

        $resumen = [
            'presente'    => $registros->where('estado', 'presente')->count(),
            'ausente'     => $registros->where('estado', 'ausente')->count(),
            'tarde'       => $registros->where('estado', 'tarde')->count(),
            'justificado' => $registros->where('estado', 'justificado')->count(),
            'total'       => $registros->count(),
        ];

        $detalle = $registros;
    }

    return view('asistencia.alumno', compact(
        'alumnos', 'alumno', 'resumen', 'detalle', 'materias'
    ));
}
}