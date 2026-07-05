<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    // Trae las asistencias ya registradas para un curso+materia+fecha
    // (para precargar el formulario si ya se tomó asistencia ese día)
    public function index(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
            'fecha'      => 'required|date',
        ]);

        $asistencias = Asistencia::where('curso_id', $request->curso_id)
            ->where('materia_id', $request->materia_id)
            ->where('fecha', $request->fecha)
            ->where('user_id', $request->user()->id)
            ->get()
            ->keyBy('alumno_id');

        return response()->json($asistencias);
    }

    // Guarda/actualiza la asistencia de todo el curso de una fecha
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
            $horallegada = ($estado === 'tarde' && !empty($datos['horallegada']))
                ? $datos['horallegada']
                : null;

            Asistencia::updateOrCreate(
                [
                    'alumno_id'  => $alumnoId,
                    'fecha'      => $request->fecha,
                    'materia_id' => $request->materia_id,
                ],
                [
                    'curso_id'    => $request->curso_id,
                    'user_id'     => $request->user()->id,
                    'estado'      => $estado,
                    'horallegada' => $horallegada,
                    'observacion' => $datos['observacion'] ?? null,
                ]
            );

            \App\Services\AsistenciaService::actualizarPorcentaje($alumnoId, $request->materia_id);
        }

        return response()->json(['message' => 'Asistencia guardada correctamente']);
    }
       // Resumen de asistencias por alumno para un curso+materia
    // (cuántas presentes, ausentes, tardes, justificadas, y % de asistencia)
    public function resumen(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso = \App\Models\Curso::where('user_id', $request->user()->id)
            ->with('alumnos')
            ->findOrFail($request->curso_id);

        $materia = \App\Models\Materia::where('user_id', $request->user()->id)
            ->findOrFail($request->materia_id);

        $registros = Asistencia::where('curso_id', $request->curso_id)
            ->where('materia_id', $request->materia_id)
            ->where('user_id', $request->user()->id)
            ->get();

        $resumen = $curso->alumnos->sortBy('apellido')->map(function ($alumno) use ($registros) {
            $regs = $registros->where('alumno_id', $alumno->id);
            $total = $regs->count();
            $presentes = $regs->whereIn('estado', ['presente', 'tarde', 'justificado'])->count();
            $porcentaje = $total > 0 ? round(($presentes / $total) * 100, 2) : 100;

            return [
                'alumno_id'   => $alumno->id,
                'nombre'      => $alumno->nombre,
                'apellido'    => $alumno->apellido,
                'presente'    => $regs->where('estado', 'presente')->count(),
                'ausente'     => $regs->where('estado', 'ausente')->count(),
                'tarde'       => $regs->where('estado', 'tarde')->count(),
                'justificado' => $regs->where('estado', 'justificado')->count(),
                'total'       => $total,
                'porcentaje'  => $porcentaje,
            ];
        })->values();

        return response()->json($resumen);
    }

    // Historial detallado de un alumno puntual en una materia
    public function historialAlumno(Request $request, $alumnoId)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $registros = Asistencia::where('alumno_id', $alumnoId)
            ->where('materia_id', $request->materia_id)
            ->where('user_id', $request->user()->id)
            ->orderBy('fecha', 'desc')
            ->get(['fecha', 'estado', 'horallegada', 'observacion']);

        return response()->json($registros);
    }
}