<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        // Calcular intentos para esta IP (sin email aún, usamos solo IP)
        $intentosKey = 'login_attempts:' . sha1(request()->ip() . '|');
        $intentos    = \Illuminate\Support\Facades\Cache::get($intentosKey, 0);

        // Si llegó por validación fallida, recalcular con el email del formulario
        $emailViejo  = old('email', '');
        if ($emailViejo) {
            $intentosKey = 'login_attempts:' . sha1(request()->ip() . '|' . strtolower(trim($emailViejo)));
            $intentos    = \Illuminate\Support\Facades\Cache::get($intentosKey, 0);
        }

        $umbral        = (int) config('services.turnstile.captcha_after', 3);
        $captchaActivo = $intentos >= $umbral;
        $siteKey       = config('services.turnstile.site_key');

        return view('auth.login', compact('intentos', 'captchaActivo', 'siteKey', 'umbral'));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
