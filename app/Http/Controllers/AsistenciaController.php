<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AsistenciaController extends Controller
{
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

        return view('asistencia.index', compact('materias', 'cursos'));
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

    foreach ($request->asistencias as $alumnoId => $datos) {
        $estado      = $datos['estado'] ?? 'presente';
        $horallegada = null;
        $fotoruta    = null;

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
                'observacion'       => $datos['observacion'] ?? null,
            ]
        );

        // Actualizar porcentaje de asistencia del alumno
        \App\Services\AsistenciaService::actualizarPorcentaje($alumnoId, $request->materia_id);
    }

    return redirect()->route('asistencia.registrar', [
    'curso_id'   => $request->curso_id,
    'materia_id' => $request->materia_id,
    'fecha'      => $request->fecha,
])->with('success', 'Asistencia guardada correctamente.');
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

        // Resumen
        $resumen = [
            'presente'    => $asistencias->where('estado', 'presente')->count(),
            'ausente'     => $asistencias->where('estado', 'ausente')->count(),
            'tarde'       => $asistencias->where('estado', 'tarde')->count(),
            'justificado' => $asistencias->where('estado', 'justificado')->count(),
            'total'       => $asistencias->count(),
        ];

        // Agrupar por alumno
        $asistencias = $asistencias->groupBy('alumno_id');

        $alumnos = Alumno::where('user_id', auth()->id())
            ->where('curso_id', $request->curso_id)
            ->orderBy('apellido')->get();
    }

    return view('asistencia.historial', compact(
        'materias', 'cursos', 'asistencias', 'filtros',
        'resumen', 'alumnos'
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

    // Recalcular porcentaje
    \App\Services\AsistenciaService::actualizarPorcentaje(
        $asistencia->alumno_id,
        $asistencia->materia_id
    );

    return redirect()->route('asistencia.alumno', [
        'alumno_id' => $asistencia->alumno_id,
        'buscar'    => $asistencia->alumno->apellido,
    ])->with('success', 'Asistencia actualizada correctamente.');
}

    public function alumno(Request $request)
    {
        $alumnos = collect();
        $alumno  = null;
        $resumen = [];
        $detalle = collect();

        if ($request->filled('buscar')) {
            $alumnos = Alumno::with('curso')
                ->where('user_id', auth()->id())
                ->where(function ($q) use ($request) {
                    $q->where('apellido', 'like', '%' . $request->buscar . '%')
                      ->orWhere('nombre',   'like', '%' . $request->buscar . '%')
                      ->orWhere('dni',      'like', '%' . $request->buscar . '%');
                })
                ->orderBy('apellido')
                ->get();
        }

        if ($request->filled('alumno_id')) {
            $alumno = Alumno::where('user_id', auth()->id())->with('curso')->findOrFail($request->alumno_id);

            $registros = Asistencia::with(['materia', 'curso'])
                ->where('alumno_id', $alumno->id)
                ->where('user_id', auth()->id())
                ->orderBy('fecha', 'desc')
                ->get();

            $resumen = [
                'presente'    => $registros->where('estado', 'presente')->count(),
                'ausente'     => $registros->where('estado', 'ausente')->count(),
                'tarde'       => $registros->where('estado', 'tarde')->count(),
                'justificado' => $registros->where('estado', 'justificado')->count(),
                'total'       => $registros->count(),
            ];

            $detalle = $registros;
        }

        return view('asistencia.alumno', compact('alumnos', 'alumno', 'resumen', 'detalle'));
    }
}