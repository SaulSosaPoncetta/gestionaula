<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Materia;

class AsistenciaService
{
    /**
     * Calcula y actualiza el porcentaje de asistencia de un alumno en una materia
     */
    public static function actualizarPorcentaje(int $alumnoId, int $materiaId): void
    {
        $alumno  = Alumno::find($alumnoId);
        $materia = Materia::find($materiaId);

        if (!$alumno || !$materia || !$materia->cantidadclasesanuales) return;

        // Total de clases dictadas (registros de asistencia para este alumno y materia)
        $totalClases = Asistencia::where('alumno_id', $alumnoId)
            ->where('materia_id', $materiaId)
            ->count();

        if ($totalClases === 0) {
            $alumno->update(['porcentajeasistencia' => 100.00]);
            return;
        }

        // Clases con presencia (presente, tarde, justificado)
        $clasesPresente = Asistencia::where('alumno_id', $alumnoId)
            ->where('materia_id', $materiaId)
            ->whereIn('estado', ['presente', 'tarde', 'justificado'])
            ->count();

        // Porcentaje sobre el total de clases dictadas hasta ahora
        $porcentaje = ($clasesPresente / $totalClases) * 100;

        $alumno->update(['porcentajeasistencia' => round($porcentaje, 2)]);
    }

    /**
     * Calcula el color de alerta según el porcentaje y el límite de la materia
     */
    public static function colorAlerta(float $porcentaje, float $limite, int $totalClases, int $cantidadClasesAnuales): string
    {
        if ($totalClases === 0 || $cantidadClasesAnuales === 0) return 'white';

        // Faltas actuales
        $faltasPorcentaje   = 100 - $porcentaje;
        $faltasLimite       = 100 - $limite;
        $diferencia         = $faltasPorcentaje - $faltasLimite;

        // Cuántas faltas equivalen a 1 clase
        $porcPorClase = 100 / $cantidadClasesAnuales;

        if ($diferencia >= 0) {
            // Ya superó el límite de faltas
            return 'danger';
        } elseif (abs($diferencia) <= $porcPorClase) {
            // Le queda 1 clase para pasarse
            return 'warning-naranja';
        } elseif (abs($diferencia) <= ($porcPorClase * 2)) {
            // Le quedan 2 clases para pasarse
            return 'warning';
        } else {
            return 'white';
        }
    }
}