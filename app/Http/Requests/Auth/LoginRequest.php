<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        // Solo exigir captcha si ya superó el umbral de intentos
        if ($this->captchaRequerido()) {
            $rules['cf-turnstile-response'] = ['required'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Por favor completá la verificación de seguridad.',
            'email.required'                 => 'El email es obligatorio.',
            'password.required'              => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Determina si el captcha debe mostrarse/validarse.
     * Se activa después de N intentos fallidos para esta IP/email.
     */
    public function captchaRequerido(): bool
    {
        $umbral = (int) config('services.turnstile.captcha_after', 3);
        return $this->intentosFallidos() >= $umbral;
    }

    /**
     * Cuántos intentos fallidos lleva esta combinación IP+email.
     */
    public function intentosFallidos(): int
    {
        return Cache::get($this->claveIntentos(), 0);
    }

    private function claveIntentos(): string
    {
        $email = Str::lower(trim($this->input('email', '')));
        return 'login_attempts:' . sha1($this->ip() . '|' . $email);
    }

    private function incrementarIntentos(): void
    {
        $key     = $this->claveIntentos();
        $actuales = Cache::get($key, 0);
        Cache::put($key, $actuales + 1, now()->addMinutes(30));
    }

    private function resetearIntentos(): void
    {
        Cache::forget($this->claveIntentos());
    }

    /**
     * Autenticar al usuario con todos los chequeos de seguridad.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Verificar Turnstile si ya superó el umbral
        if ($this->captchaRequerido()) {
            $this->verificarTurnstile();
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            $this->incrementarIntentos();

            $intentos = $this->intentosFallidos();
            $umbral   = (int) config('services.turnstile.captcha_after', 3);

            // Mensaje personalizado según los intentos
            $extra = '';
            if ($intentos >= $umbral) {
                $extra = ' Se requiere verificación de seguridad.';
            } elseif ($intentos === $umbral - 1) {
                $extra = ' Próximo intento requerirá verificación.';
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed') . $extra,
            ]);
        }

        // Login exitoso: limpiar contadores
        RateLimiter::clear($this->throttleKey());
        $this->resetearIntentos();
    }

    /**
     * Verificar el token de Cloudflare Turnstile.
     */
    protected function verificarTurnstile(): void
    {
        $secretKey = config('services.turnstile.secret_key');
        $token     = $this->input('cf-turnstile-response');

        if (empty($secretKey)) {
            Log::warning('Turnstile: secret key no configurada');
            return;
        }

        if (empty($token)) {
            throw ValidationException::withMessages([
                'captcha' => 'Por favor completá la verificación de seguridad.',
            ]);
        }

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => $secretKey,
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

            $data = $response->json();

            if (!($data['success'] ?? false)) {
                $errores = implode(', ', $data['error-codes'] ?? ['unknown']);
                Log::warning("Turnstile falló para IP {$this->ip()}: {$errores}");

                throw ValidationException::withMessages([
                    'captcha' => 'La verificación de seguridad falló. Intentá de nuevo.',
                ]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Turnstile error de conexión: ' . $e->getMessage());
            // No bloquear si Cloudflare no responde
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 6)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Demasiados intentos fallidos. Esperá " . ceil($seconds / 60) . " minuto(s).",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
