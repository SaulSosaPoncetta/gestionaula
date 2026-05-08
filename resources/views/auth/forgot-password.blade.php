<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — Gestión Aula</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 440px;">

        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;">
                <i class="bi bi-mortarboard-fill fs-2"></i>
            </div>
            <h3 class="fw-bold">Gestión Aula</h3>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-2">Recuperar contraseña</h5>
                <p class="text-muted small mb-4">
                    Ingresá tu email y te enviaremos un enlace para restablecer tu contraseña.
                </p>

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

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>Enviar enlace
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
            </a>
        </div>

    </div>
</div>

</body>
</html>