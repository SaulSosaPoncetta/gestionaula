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
    /**
     * Paso 1: Seleccionar materia y curso
     */
    public function index()
    {
        $materias = Materia::orderBy('nombre')->get();
        $cursos   = collect();

        if (request()->filled('materia_id')) {
            $cursos = Curso::whereHas('materias', fn($q) =>
                $q->where('materias.id', request('materia_id'))
            )->orderBy('anio')->orderBy('division')->get();

            if ($cursos->isEmpty()) {
                $cursos = Curso::orderBy('anio')->orderBy('division')->get();
            }
        }

        return view('asistencia.index', compact('materias', 'cursos'));
    }

    /**
     * Paso 2: Elegir accion (registrar, ver listado, editar alumno)
     */
    public function accion(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso   = Curso::with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::findOrFail($request->materia_id);

        return view('asistencia.accion', compact('curso', 'materia'));
    }

    /**
     * Paso 3a: Formulario de registro de nueva asistencia
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
            'fecha'      => 'required|date',
        ]);

        $curso   = Curso::with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::find($request->materia_id);
        $fecha   = $request->fecha;

        $asistencias = Asistencia::where('curso_id', $request->curso_id)
            ->where('fecha', $fecha)
            ->where('materia_id', $request->materia_id)
            ->get()
            ->keyBy('alumno_id');

        return view('asistencia.registrar', compact('curso', 'materia', 'fecha', 'asistencias'));
    }

    /**
     * Guardar asistencia
     */
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
        }

        return redirect()->route('asistencia.index')
                         ->with('success', 'Asistencia guardada correctamente.');
    }

    /**
     * Paso 3b: Ver listado de asistencias del curso/materia
     */
    public function listado(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso   = Curso::with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::findOrFail($request->materia_id);

        // Traer todos los registros de asistencia para este curso y materia
        $registros = Asistencia::with('alumno')
            ->where('curso_id', $request->curso_id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('fecha', 'desc')
            ->get();

        // Agrupar por alumno para mostrar resumen
        $resumenAlumnos = $curso->alumnos->sortBy('apellido')->map(function($alumno) use ($registros) {
            $regs = $registros->where('alumno_id', $alumno->id);
            return [
                'alumno'      => $alumno,
                'presente'    => $regs->where('estado', 'presente')->count(),
                'ausente'     => $regs->where('estado', 'ausente')->count(),
                'tarde'       => $regs->where('estado', 'tarde')->count(),
                'justificado' => $regs->where('estado', 'justificado')->count(),
                'total'       => $regs->count(),
            ];
        });

        return view('asistencia.listado', compact('curso', 'materia', 'resumenAlumnos'));
    }

    /**
     * Historial general con filtros
     */
    public function historial(Request $request)
    {
        $materias    = Materia::orderBy('nombre')->get();
        $cursos      = Curso::orderBy('anio')->orderBy('division')->get();
        $asistencias = collect();
        $filtros     = [];

        if ($request->filled('curso_id') || $request->filled('materia_id')) {
            $query = Asistencia::with(['alumno', 'materia', 'curso']);

            if ($request->filled('materia_id')) {
                $query->where('materia_id', $request->materia_id);
                $filtros['materia_id'] = $request->materia_id;
            }
            if ($request->filled('curso_id')) {
                $query->where('curso_id', $request->curso_id);
                $filtros['curso_id'] = $request->curso_id;
            }
            if ($request->filled('fecha')) {
                $query->where('fecha', $request->fecha);
                $filtros['fecha'] = $request->fecha;
            }

            $asistencias = $query->orderBy('fecha', 'desc')->paginate(30);
        }

        return view('asistencia.historial', compact('materias', 'cursos', 'asistencias', 'filtros'));
    }

    /**
 * Editar un registro de asistencia individual
 */
public function editarRegistro(Asistencia $asistencia)
{
    $asistencia->load(['alumno', 'materia', 'curso']);
    return view('asistencia.editar', compact('asistencia'));
}

/**
 * Actualizar un registro de asistencia individual
 */
public function actualizarRegistro(Request $request, Asistencia $asistencia)
{
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
        // Si tiene foto pasa a justificado
        $request->merge(['estado' => 'justificado']);
    }

    $asistencia->update([
        'estado'            => $request->estado,
        'horallegada'       => $horallegada,
        'fotojustificacion' => $fotoruta,
        'observacion'       => $request->observacion,
    ]);

    return redirect()->route('asistencia.alumno', [
        'alumno_id' => $asistencia->alumno_id,
        'buscar'    => $asistencia->alumno->apellido,
    ])->with('success', 'Asistencia actualizada correctamente.');
}

    /**
     * Buscador por alumno
     */
    public function alumno(Request $request)
    {
        $alumnos = collect();
        $alumno  = null;
        $resumen = [];
        $detalle = collect();

        if ($request->filled('buscar')) {
            $alumnos = Alumno::with('curso')
                ->where(function ($q) use ($request) {
                    $q->where('apellido', 'like', '%' . $request->buscar . '%')
                      ->orWhere('nombre',   'like', '%' . $request->buscar . '%')
                      ->orWhere('dni',      'like', '%' . $request->buscar . '%');
                })
                ->orderBy('apellido')
                ->get();
        }

        if ($request->filled('alumno_id')) {
            $alumno = Alumno::with('curso')->findOrFail($request->alumno_id);

            $registros = Asistencia::with(['materia', 'curso'])
                ->where('alumno_id', $alumno->id)
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