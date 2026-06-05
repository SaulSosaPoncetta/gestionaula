<?php

namespace App\Http\Middleware; // <-- Debe ser exactamente este

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeguridadWeb // <-- Debe coincidir idéntico al nombre del archivo
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}