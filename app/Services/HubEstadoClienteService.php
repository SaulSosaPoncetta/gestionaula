<?php

namespace App\Services;

use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sincroniza el estado de la suscripción del docente contra MiGestión Panel
 * (el hub). El hub es ahora quien administra el cobro con Mercado Pago;
 * acá solo reflejamos el estado que él nos manda.
 */
class HubEstadoClienteService
{
    public function sincronizar(User $user, bool $forzar = false): void
    {
        if (! config('migestion_hub.url') || ! config('migestion_hub.api_key')) {
            return; // integración no configurada todavía: no bloqueamos a nadie
        }

        $suscripcion = $user->suscripcion;
        $cacheVencida = ! $suscripcion || ! $suscripcion->updated_at
            || $suscripcion->updated_at->addHours((int) config('migestion_hub.cache_horas'))->isPast();

        if (! $forzar && ! $cacheVencida) {
            return;
        }

        try {
            $response = Http::withHeaders(['X-Api-Key' => config('migestion_hub.api_key')])
                ->timeout(5)
                ->get(rtrim(config('migestion_hub.url'), '/').'/api/estado-cliente', [
                    'referencia_externa' => (string) $user->id,
                ]);

            if ($response->status() === 404) {
                return; // todavía no está vinculado en el hub
            }

            $response->throw();
            $datos = $response->json();

            $this->aplicarEstado($user, $datos['estado'] ?? null);
        } catch (\Throwable $e) {
            Log::warning('No se pudo verificar el estado de pago contra el hub', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function aplicarEstado(User $user, ?string $estado): void
    {
        if (! in_array($estado, ['activa', 'vencida', 'suspendida', 'cancelada'], true)) {
            return;
        }

        $suscripcion = $user->suscripcion;

        if ($suscripcion) {
            $suscripcion->update(['estado' => $estado === 'vencida' ? 'suspendida' : $estado]);
        } else {
            $suscripcion = Suscripcion::create([
                'user_id' => $user->id,
                'estado' => $estado === 'vencida' ? 'suspendida' : $estado,
            ]);
        }

        $user->update(['activo' => $estado === 'activa']);
    }

    public function alDia(User $user): bool
    {
        $suscripcion = $user->suscripcion;

        // Sin suscripción registrada todavía: no lo bloqueamos, es lo mismo
        // que "sin_verificar" del lado de GestiónComercial.
        return ! $suscripcion || $suscripcion->estado === 'activa';
    }
}