<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Plan — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .sidebar { min-height: 100vh; background: #1a1a2e; color: #fff; width: 240px; position: fixed; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); border-radius: 8px; }
        .main-content { margin-left: 240px; padding: 2rem; }
    </style>
</head>
<body>
<div class="sidebar p-3">
    <div class="text-center mb-4 pt-2">
        <i class="bi bi-shield-check fs-2 text-warning"></i>
        <div class="fw-bold mt-1">GestiónAula</div>
    </div>
    <nav class="nav flex-column gap-1">
        <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="{{ route('admin.planes.index') }}" class="nav-link active"><i class="bi bi-box me-2"></i>Planes</a>
        <a href="{{ route('admin.pagos.index') }}" class="nav-link"><i class="bi bi-cash-stack me-2"></i>Pagos</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Nuevo plan</h4>
        <a href="{{ route('admin.planes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width:500px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.planes.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control"
                           value="{{ old('nombre') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="precio" class="form-control"
                               value="{{ old('precio') }}" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                    <select name="periodicidad" class="form-select" required>
                        @foreach(\App\Models\Plan::PERIODICIDADES as $val => $label)
                            <option value="{{ $val }}" {{ old('periodicidad') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle me-1"></i>Guardar plan
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>