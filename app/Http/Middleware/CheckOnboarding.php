<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOnboarding
{
    // Rutas siempre permitidas sin importar el paso
    const SIEMPRE_PERMITIDAS = [
        'dashboard', 'profile.*', 'logout',
        'ciclos_lectivos.*',   // paso 0 → 1
        'pwa.*', 'offline', 'ping', 'sync.*',
        'admin.*',
    ];

    // Rutas permitidas por paso
    const RUTAS_POR_PASO = [
        1 => ['designaciones.*'],          // paso 1: puede cargar designaciones
        2 => ['designaciones.*','horarios.*'],  // paso 2: puede cargar horarios
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || $user->hasRole('admin')) {
            return $next($request);
        }

        $paso = $user->onboardingStep();

        // Paso 3+ → todo desbloqueado
        if ($paso >= 3) {
            return $next($request);
        }

        // Verificar si la ruta actual está permitida
        $rutaActual = $request->route()?->getName() ?? '';

        // Siempre permitidas
        foreach (self::SIEMPRE_PERMITIDAS as $patron) {
            if (fnmatch($patron, $rutaActual)) {
                return $next($request);
            }
        }

        // Permitidas según paso
        for ($p = 1; $p <= $paso; $p++) {
            foreach (self::RUTAS_POR_PASO[$p] ?? [] as $patron) {
                if (fnmatch($patron, $rutaActual)) {
                    return $next($request);
                }
            }
        }

        // Ruta no permitida → redirigir al dashboard con mensaje
        $mensajes = [
            0 => 'Primero creá el ciclo lectivo del año en curso para continuar.',
            1 => 'Para acceder a esta sección, primero cargá tus designaciones.',
            2 => 'Para acceder a esta sección, primero cargá tus horarios.',
        ];

        return redirect()->route('dashboard')
            ->with('onboarding_bloqueado', $mensajes[$paso] ?? 'Completá el proceso inicial.');
    }
}
