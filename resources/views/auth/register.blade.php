<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — Gestión Aula</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 480px;">

        {{-- Logo / Título --}}
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;">
                <i class="bi bi-mortarboard-fill fs-2"></i>
            </div>
            <h3 class="fw-bold">Gestión Aula</h3>
            <p class="text-muted">Creá tu cuenta de docente</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h5 class="fw-semibold mb-4">Nueva cuenta</h5>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nombre completo</label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               required autofocus autocomplete="name"
                               placeholder="Apellido y nombre">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               required autocomplete="username"
                               placeholder="tu@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Confirmar contraseña
                        </label>
                        <input type="password" id="password_confirmation"
                               name="password_confirmation"
                               class="form-control"
                               required autocomplete="new-password">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i>Crear cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted small">
                ¿Ya tenés cuenta? Iniciá sesión
            </a>
        </div>

    </div>
</div>

</body>
</html>