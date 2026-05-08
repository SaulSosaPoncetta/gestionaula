<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Gestión Aula</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 440px;">

        {{-- Logo / Título --}}
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;">
                <i class="bi bi-mortarboard-fill fs-2"></i>
            </div>
            <h3 class="fw-bold">Gestión Aula</h3>
            <p class="text-muted">Sistema de gestión para docentes</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h5 class="fw-semibold mb-4">Iniciar sesión</h5>

                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label text-muted" for="remember">
                            Recordarme
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-muted small me-3">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="text-primary small">
                    Crear cuenta
                </a>
            @endif
        </div>

    </div>
</div>

</body>
</html>