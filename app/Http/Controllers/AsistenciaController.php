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
        // Muestra todos los cursos del docente para seleccionar
        $cursos = Curso::orderBy('anio')->orderBy('division')->get();

        return view('asistencia.index', compact('cursos'));
    }

    /**
     * Carga el formulario de registro dado curso + materia + fecha.
     * Los alumnos mostrados son los del curso seleccionado.
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'nullable|exists:materias,id',
            'fecha'      => 'required|date',
        ]);

        $curso   = Curso::with('alumnos')->findOrFail($request->curso_id);
        $materia = $request->materia_id ? Materia::find($request->materia_id) : null;
        $fecha   = $request->fecha;

        // Asistencias ya registradas para este curso/materia/fecha
        $asistencias = Asistencia::where('curso_id', $request->curso_id)
            ->where('fecha', $fecha)
            ->when($request->materia_id, fn($q) => $q->where('materia_id', $request->materia_id))
            ->get()
            ->keyBy('alumno_id');

        return view('asistencia.registrar', compact('curso', 'materia', 'fecha', 'asistencias'));
    }

    /**
     * Guarda los registros de asistencia.
     * Maneja foto de justificación y hora de llegada.
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'curso_id'    => 'required|exists:cursos,id',
            'materia_id'  => 'nullable|exists:materias,id',
            'fecha'       => 'required|date',
            'asistencias' => 'required|array',
        ]);

        foreach ($request->asistencias as $alumnoId => $datos) {
            $estado      = $datos['estado'] ?? 'presente';
            $horallegada = null;
            $fotoruta    = null;

            // Si llegó tarde, tomar hora del campo (que el JS llenó con hora del SO)
            if ($estado === 'tarde' && !empty($datos['horallegada'])) {
                $horallegada = $datos['horallegada'];
            }

            // Si está ausente y viene foto de justificación
            if ($estado === 'ausente' && $request->hasFile("fotos.{$alumnoId}")) {
                $file     = $request->file("fotos.{$alumnoId}");
                $fotoruta = $file->store("justificaciones/{$request->fecha}", 'public');
                // Si hay foto, se marca como justificado
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
     * Historial con filtros por curso, materia, fecha.
     */
    public function historial(Request $request)
    {
        $cursos     = Curso::orderBy('anio')->orderBy('division')->get();
        $asistencias = collect();
        $filtros     = [];

        if ($request->filled('curso_id')) {
            $filtros['curso_id'] = $request->curso_id;
            $query = Asistencia::with(['alumno', 'materia', 'docente'])
                ->where('curso_id', $request->curso_id);

            if ($request->filled('materia_id')) {
                $query->where('materia_id', $request->materia_id);
                $filtros['materia_id'] = $request->materia_id;
            }
            if ($request->filled('fecha')) {
                $query->where('fecha', $request->fecha);
                $filtros['fecha'] = $request->fecha;
            }

            $asistencias = $query->orderBy('fecha', 'desc')->paginate(30);
        }

        return view('asistencia.historial', compact('cursos', 'asistencias', 'filtros'));
    }

    /**
     * Buscador: ver estado de inasistencias de un alumno.
     */
    public function alumno(Request $request)
    {
        $alumnos  = collect();
        $alumno   = null;
        $resumen  = [];
        $detalle  = collect();

        // Búsqueda por nombre, apellido o DNI
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

        // Si se seleccionó un alumno específico
        if ($request->filled('alumno_id')) {
            $alumno = Alumno::with('curso')->findOrFail($request->alumno_id);

            $registros = Asistencia::with(['materia', 'curso'])
                ->where('alumno_id', $alumno->id)
                ->orderBy('fecha', 'desc')
                ->get();

            // Resumen de estados
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
