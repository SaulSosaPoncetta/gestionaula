<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Materia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('director')) {
            $cursos = Curso::with('materias')->orderBy('nombre')->get();
        } else {
            $cursos = Curso::whereHas('docentes', fn($q) => $q->where('users.id', $user->id))
                           ->with('materias')
                           ->orderBy('nombre')
                           ->get();
        }

        return view('asistencia.index', compact('cursos'));
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'nullable|exists:materias,id',
            'fecha'      => 'required|date',
        ]);

        $curso = Curso::with('alumnos')->findOrFail($request->curso_id);
        $materia = $request->materia_id ? Materia::find($request->materia_id) : null;
        $fecha = $request->fecha;

        $asistencias = Asistencia::where('curso_id', $request->curso_id)
            ->where('fecha', $fecha)
            ->when($request->materia_id, fn($q) => $q->where('materia_id', $request->materia_id))
            ->get()
            ->keyBy('alumno_id');

        return view('asistencia.registrar', compact('curso', 'materia', 'fecha', 'asistencias'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'curso_id'      => 'required|exists:cursos,id',
            'materia_id'    => 'nullable|exists:materias,id',
            'fecha'         => 'required|date',
            'asistencias'   => 'required|array',
        ]);

        foreach ($request->asistencias as $alumnoId => $datos) {
            Asistencia::updateOrCreate(
                [
                    'alumno_id'  => $alumnoId,
                    'fecha'      => $request->fecha,
                    'materia_id' => $request->materia_id,
                ],
                [
                    'curso_id'    => $request->curso_id,
                    'user_id'     => auth()->id(),
                    'estado'      => $datos['estado'] ?? 'presente',
                    'observacion' => $datos['observacion'] ?? null,
                ]
            );
        }

        return redirect()->route('asistencia.index')
                         ->with('success', 'Asistencia guardada correctamente.');
    }

    public function historial(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('director')) {
            $cursos = Curso::orderBy('nombre')->get();
        } else {
            $cursos = Curso::whereHas('docentes', fn($q) => $q->where('users.id', $user->id))
                           ->orderBy('nombre')
                           ->get();
        }

        $asistencias = collect();
        $filtros = [];

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
}