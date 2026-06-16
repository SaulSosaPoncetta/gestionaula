<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos — Admin GestiónAula</title>
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
    </div>
    <nav class="nav flex-column gap-1">
        <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="{{ route('admin.planes.index') }}" class="nav-link"><i class="bi bi-box me-2"></i>Planes</a>
        <a href="{{ route('admin.pagos.index') }}" class="nav-link active"><i class="bi bi-cash-stack me-2"></i>Pagos</a>
    </nav>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2"></i>Historial de pagos</h4>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pagos.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select name="estado" class="form-select">
                        <option value="">— Todos los estados —</option>
                        @foreach(\App\Models\Pago::ESTADOS as $val => $label)
                            <option value="{{ $val }}" {{ request('estado') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Docente</th>
                        <th>Período</th>
                        <th class="text-end">Monto</th>
                        <th>Vencimiento</th>
                        <th>Método</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $pago)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('admin.usuario', $pago->user) }}" class="text-decoration-none fw-semibold">
                                {{ $pago->user->name }}
                            </a>
                            <div class="text-muted small">{{ $pago->user->email }}</div>
                        </td>
                        <td class="small">
                            {{ $pago->periododesde->format('d/m/Y') }}
                            — {{ $pago->periodohasta->format('d/m/Y') }}
                        </td>
                        <td class="text-end fw-bold">${{ number_format($pago->monto, 2) }}</td>
                        <td class="{{ $pago->estado === 'pendiente' && $pago->fechapago->isPast() ? 'text-danger fw-bold' : '' }} small">
                            {{ $pago->fechapago->format('d/m/Y') }}
                        </td>
                        <td class="small">{{ \App\Models\Pago::METODOS[$pago->metodopago] ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $pago->estadobadge }}">{{ $pago->estadolabel }}</span>
                        </td>
                        <td class="text-center">
                            @if($pago->estado === 'pendiente')
                                <form method="POST" action="{{ route('admin.pagos.vencido', $pago) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Marcar como vencido?')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            @elseif($pago->comprobante)
                                <a href="{{ asset('storage/' . $pago->comprobante) }}"
                                   target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pagos->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@include('partials.modal-confirm')
</body>
</html>