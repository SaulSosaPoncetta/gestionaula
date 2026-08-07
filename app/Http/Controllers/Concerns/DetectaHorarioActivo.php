<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Horario;
use Carbon\Carbon;

trait DetectaHorarioActivo
{
    /**
     * Devuelve el Horario activo del docente en este momento exacto,
     * o null si no hay ninguna clase en curso.
     *
     * Usa el mismo criterio que DashboardController: comparación de horas
     * en formato H:i:s sobre una coleccion PHP (evita problemas de
     * comparacion de tipos/formatos al hacerlo via SQL crudo).
     */
    protected function detectarHorarioActivo(): ?Horario
    {
        return self::detectarParaUsuario(auth()->id());
    }

    public static function detectarParaUsuario(int $userId): ?Horario
    {
        $ahora      = Carbon::now('America/Argentina/Buenos_Aires');
        $horaActual = $ahora->format('H:i:s');
        $mapaDias   = [1=>'lunes',2=>'martes',3=>'miercoles',4=>'jueves',5=>'viernes',6=>'sabado',7=>'domingo'];
        $diaActual  = $mapaDias[$ahora->isoWeekday()];
        $horarios   = Horario::with(['materia','curso','establecimiento'])
            ->where('user_id', $userId)->where('dia', $diaActual)->get();
        return $horarios->first(fn($h) => $h->horainicio <= $horaActual && $h->horafin >= $horaActual);
    }
}
