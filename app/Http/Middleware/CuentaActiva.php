<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CuentaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->activo && !auth()->user()->hasRole('admin')) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está suspendida. Contactá al administrador.']);
        }
        return $next($request);
    }
}