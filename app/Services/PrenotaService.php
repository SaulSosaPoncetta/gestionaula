<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\Calificacion;
use App\Models\ActividadNota;
use App\Models\ActividadAsignacion;
use App\Models\TipoValoracion;
use App\Models\Asistencia;

class PrenotaService
{
    /**
     * Calcula la prenota de un alumno en una materia y curso
     */
    public static function calcular(int $alumnoId, int $materiaId, int $cursoId): array
    {
        // 1. Promedio de calificaciones tradicionales
        $calificaciones = Calificacion::where('alumno_id', $alumnoId)
            ->where('materia_id', $materiaId)
            ->where('curso_id', $cursoId)
            ->whereNotNull('nota')
            ->pluck('nota');

        $promedioCalificaciones = $calificaciones->count() > 0
            ? round($calificaciones->avg(), 2)
            : null;

        // 2. Promedio de notas de actividades
        $asignaciones = ActividadAsignacion::where('materia_id', $materiaId)
            ->where('curso_id', $cursoId)
            ->pluck('id');

        $notasActividades = ActividadNota::where('alumno_id', $alumnoId)
            ->whereIn('asignacion_id', $asignaciones)
            ->whereNotNull('notaindividual')
            ->get();

        $todasLasNotas = collect();

        foreach ($notasActividades as $na) {
            $todasLasNotas->push($na->notaindividual);
            // Si tiene nota grupal también la incluimos
            if ($na->notagrupal !== null) {
                $todasLasNotas->push($na->notagrupal);
            }
        }

        $promedioActividades = $todasLasNotas->count() > 0
            ? round($todasLasNotas->avg(), 2)
            : null;

        // 3. Nota de asistencia
        $materia = Materia::find($materiaId);
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

        // 4. Calcular promedio final
        $componentes = collect();
        if ($promedioCalificaciones !== null) $componentes->push($promedioCalificaciones);
        if ($promedioActividades !== null)    $componentes->push($promedioActividades);
        if ($notaAsistencia !== null)         $componentes->push($notaAsistencia);

        $notaFinal = $componentes->count() > 0
            ? round($componentes->avg(), 2)
            : 0;

        // 5. Buscar valoraciones que corresponden a la nota
        $valoraciones = TipoValoracion::where('notainferior', '<=', $notaFinal)
            ->where('notasuperior', '>=', $notaFinal)
            ->orderBy('notainferior')
            ->get();

        return [
            'alumno_id'              => $alumnoId,
            'promedioCalificaciones' => $promedioCalificaciones,
            'promedioActividades'    => $promedioActividades,
            'notaAsistencia'         => $notaAsistencia,
            'porcentajeAsistencia'   => $porcentajeAsistencia,
            'notaFinal'              => $notaFinal,
            'valoraciones'           => $valoraciones,
            'cantCalificaciones'     => $calificaciones->count(),
            'cantActividades'        => $notasActividades->count(),
        ];
    }
}