<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planes — Admin GestiónAula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .sidebar { min-height: 100vh; background: #1a1a2e; color: #fff; width: 240px; position: fixed; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-radius: 8px; }
        .main-content { margin-left: 240px; padding: 2rem; }
    </style>
</head>
<body>
<div class="sidebar p-3 d-flex flex-column">
    <div class="text-center mb-4 pt-2">
        <i class="bi bi-shield-check fs-2 text-warning"></i>
        <div class="fw-bold mt-1">GestiónAula</div>
        <small class="text-muted">Panel de administración</small>
    </div>
    <nav class="nav flex-column gap-1">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="{{ route('admin.planes.index') }}" class="nav-link active">
            <i class="bi bi-box me-2"></i>Planes
        </a>
        <a href="{{ route('admin.pagos.index') }}" class="nav-link">
            <i class="bi bi-cash-stack me-2"></i>Pagos
        </a>
    </nav>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-box me-2"></i>Planes de suscripción</h4>
        <a href="{{ route('admin.planes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo plan
        </a>
    </div>

    <div class="row g-3">
        @foreach($planes as $plan)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold">{{ $plan->nombre }}</h5>
                            <div class="text-muted small mb-2">{{ $plan->descripcion }}</div>
                        </div>
                        <span class="badge bg-{{ $plan->activo ? 'success' : 'secondary' }}">
                            {{ $plan->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="fs-3 fw-bold text-primary">
                        ${{ number_format($plan->precio, 2) }}
                        <span class="fs-6 text-muted fw-normal">/ {{ $plan->periodicidad }}</span>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-people me-1"></i>{{ $plan->suscripciones_count }} suscripciones
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.planes.edit', $plan) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        <form method="POST" action="{{ route('admin.planes.destroy', $plan) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este plan?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>