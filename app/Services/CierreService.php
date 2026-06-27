<?php

namespace App\Services;

use App\Models\Calificacion;
use App\Models\ActividadNota;
use App\Models\ActividadAsignacion;
use App\Models\TipoValoracion;
use App\Models\Asistencia;
use App\Models\Materia;

class CierreService
{
    /**
     * Calcula el cierre de notas del cuatrimestre para un alumno.
     * NO usa prenotas: solo calificaciones, actividades y asistencia.
     */
    public static function calcular(int $alumnoId, int $materiaId, int $cursoId): array
    {
        // ── 1. Calificaciones tradicionales ───────────────────────────────
        $calificaciones = Calificacion::where('alumno_id', $alumnoId)
            ->where('materia_id', $materiaId)
            ->where('curso_id', $cursoId)
            ->whereNotNull('nota')
            ->pluck('nota');

        $cantCalificaciones     = $calificaciones->count();
        $promedioCalificaciones = $cantCalificaciones > 0
            ? round($calificaciones->avg(), 2)
            : null;

        // ── 2. Notas de actividades ────────────────────────────────────────
        $asignacionIds = ActividadAsignacion::where('materia_id', $materiaId)
            ->where('curso_id', $cursoId)
            ->pluck('id');

        $notasActividades = ActividadNota::where('alumno_id', $alumnoId)
            ->whereIn('asignacion_id', $asignacionIds)
            ->whereNotNull('notaindividual')
            ->get();

        $todasLasNotas = collect();
        foreach ($notasActividades as $na) {
            $todasLasNotas->push($na->notaindividual);
            if ($na->notagrupal !== null) {
                $todasLasNotas->push($na->notagrupal);
            }
        }

        $cantActividades     = $notasActividades->count();
        $promedioActividades = $todasLasNotas->count() > 0
            ? round($todasLasNotas->avg(), 2)
            : null;

        // ── 3. Asistencia ─────────────────────────────────────────────────
        $materia              = Materia::find($materiaId);
        $porcentajeAsistencia = null;
        $notaAsistencia       = null;

        $totalClases = Asistencia::where('alumno_id', $alumnoId)
            ->where('materia_id', $materiaId)
            ->count();

        if ($totalClases > 0) {
            $clasesPresente = Asistencia::where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->whereIn('estado', ['presente', 'tarde', 'justificado'])
                ->count();

            $porcentajeAsistencia = round(($clasesPresente / $totalClases) * 100, 2);
            $limiteMinimo         = $materia?->porcentajelimite ?? 75;

            if ($porcentajeAsistencia >= 90) {
                $notaAsistencia = 10;
            } elseif ($porcentajeAsistencia >= $limiteMinimo) {
                $notaAsistencia = 7;
            } else {
                $notaAsistencia = 6;
            }
        }

        // ── 4. Promedio final ponderado por cantidad de registros ─────────
        //
        // Se pondera por la cantidad de registros de cada componente:
        //   - cada calificación cuenta como 1 registro
        //   - cada nota de actividad cuenta como 1 registro
        //   - la asistencia cuenta como 1 registro (si existe)
        //
        $sumaTotal    = 0;
        $registrosTotal = 0;

        if ($promedioCalificaciones !== null) {
            $sumaTotal    += $promedioCalificaciones * $cantCalificaciones;
            $registrosTotal += $cantCalificaciones;
        }

        if ($promedioActividades !== null) {
            $sumaTotal    += $promedioActividades * $cantActividades;
            $registrosTotal += $cantActividades;
        }

        if ($notaAsistencia !== null) {
            $sumaTotal    += $notaAsistencia;
            $registrosTotal += 1;
        }

        $notaFinal = $registrosTotal > 0
            ? round($sumaTotal / $registrosTotal, 2)
            : 0;

        // ── 5. Valoraciones que corresponden a la nota ────────────────────
        $valoraciones = TipoValoracion::where('notainferior', '<=', $notaFinal)
            ->where('notasuperior', '>=', $notaFinal)
            ->orderBy('notainferior')
            ->get();

        return [
            'alumno_id'              => $alumnoId,
            'promedioCalificaciones' => $promedioCalificaciones,
            'cantCalificaciones'     => $cantCalificaciones,
            'promedioActividades'    => $promedioActividades,
            'cantActividades'        => $cantActividades,
            'notaAsistencia'         => $notaAsistencia,
            'porcentajeAsistencia'   => $porcentajeAsistencia,
            'notaFinal'              => $notaFinal,
            'valoraciones'           => $valoraciones,
        ];
    }
}
