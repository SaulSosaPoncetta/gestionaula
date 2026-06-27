<?php

namespace App\Services;

use App\Models\CicloLectivo;

class CicloLectivoService
{
    /**
     * Devuelve el ciclo lectivo activo del usuario.
     * Si no tiene ninguno, lo crea para el año actual.
     */
    public static function obtenerActivo(?int $userId = null): ?CicloLectivo
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) return null;

        $ciclo = CicloLectivo::where('user_id', $userId)
            ->where('activo', true)
            ->first();

        if (!$ciclo) {
            $ciclo = CicloLectivo::crearParaUsuario($userId);
        }

        return $ciclo;
    }
}
