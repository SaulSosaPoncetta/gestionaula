<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Gestión Aula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .sidebar { min-height: 100vh; background: #1a1a2e; color: #fff; width: 240px; position: fixed; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-radius: 8px; }
        .main-content { margin-left: 240px; padding: 2rem; }
        .card-stat { border-radius: 12px; }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar p-3 d-flex flex-column">
    <div class="text-center mb-4 pt-2">
        <i class="bi bi-shield-check fs-2 text-warning"></i>
        <div class="fw-bold mt-1">GestiónAula</div>
        <small class="text-muted">Panel de administración</small>
    </div>

    <nav class="nav flex-column gap-1">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="{{ route('admin.planes.index') }}"
           class="nav-link {{ request()->routeIs('admin.planes.*') ? 'active' : '' }}">
            <i class="bi bi-box me-2"></i>Planes
        </a>
        <a href="{{ route('admin.pagos.index') }}"
           class="nav-link {{ request()->routeIs('admin.pagos.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack me-2"></i>Pagos
        </a>
    </nav>

    <div class="mt-auto pb-2">
        <hr class="border-secondary">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="bi bi-arrow-left me-2"></i>Volver al sistema
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </button>
        </form>
    </div>
</div>

{{-- Contenido principal --}}
<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
        <span class="text-muted small">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Cards de resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $totalUsuarios }}</div>
                    <div class="text-muted small">Total docentes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-success">{{ $usuariosActivos }}</div>
                    <div class="text-muted small">Cuentas activas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $usuariosSuspend }}</div>
                    <div class="text-muted small">Cuentas suspendidas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $pagosPendientes }}</div>
                    <div class="text-muted small">Pagos pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-0 shadow-sm bg-success text-white text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold">${{ number_format($recaudacionMes, 2) }}</div>
                    <div class="small">Recaudado este mes</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-0 shadow-sm bg-primary text-white text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold">${{ number_format($recaudacionTotal, 2) }}</div>
                    <div class="small">Recaudación total</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-0 shadow-sm bg-danger text-white text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold">{{ $pagosVencidos }}</div>
                    <div class="small">Pagos vencidos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Próximos vencimientos --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-calendar-x me-1 text-danger"></i>Próximos vencimientos (7 días)
                </div>
                <div class="card-body p-0">
                    @if($proximosVencimientos->isEmpty())
                        <div class="p-3 text-muted text-center small">Sin vencimientos próximos.</div>
                    @else
                    <ul class="list-group list-group-flush">
                        @foreach($proximosVencimientos as $sus)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $sus->user->name }}</div>
                                <div class="text-muted small">{{ $sus->user->email }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger">${{ number_format($sus->montomensual, 2) }}</span>
                                <div class="text-muted small">{{ $sus->proximopago?->format('d/m/Y') }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Últimos pagos --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                    <span><i class="bi bi-cash me-1 text-success"></i>Últimos pagos</span>
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-sm btn-outline-secondary">
                        Ver todos
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Docente</th>
                                <th class="text-end">Monto</th>
                                <th>Fecha</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosPagos as $pago)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.usuario', $pago->user) }}" class="text-decoration-none">
                                        {{ $pago->user->name }}
                                    </a>
                                </td>
                                <td class="text-end fw-semibold">${{ number_format($pago->monto, 2) }}</td>
                                <td>{{ $pago->fechapago->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $pago->estadobadge }}">{{ $pago->estadolabel }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Listado de usuarios --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people me-1"></i>Docentes registrados</span>
            <span class="badge bg-primary">{{ $totalUsuarios }}</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nombre</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th class="text-end">Monto</th>
                        <th>Próximo pago</th>
                        <th class="text-center">Suscripción</th>
                        <th class="text-center">Cuenta</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $user->name }}</td>
                        <td class="text-muted small">{{ $user->email }}</td>
                        <td>{{ $user->suscripcion?->plan?->nombre ?? '—' }}</td>
                        <td class="text-end">
                            @if($user->suscripcion)
                                ${{ number_format($user->suscripcion->montomensual, 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->suscripcion?->proximopago)
                                <span class="{{ $user->suscripcion->proximopago->isPast() ? 'text-danger fw-bold' : '' }}">
                                    {{ $user->suscripcion->proximopago->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->suscripcion)
                                <span class="badge bg-{{ $user->suscripcion->estadobadge }}">
                                    {{ $user->suscripcion->estadolabel }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark">Sin suscripción</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->activo)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-danger">Suspendida</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.usuario', $user) }}"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.toggle', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm {{ $user->activo ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        onclick="return confirm('{{ $user->activo ? 'Suspender' : 'Activar' }} esta cuenta?')">
                                    <i class="bi bi-{{ $user->activo ? 'slash-circle' : 'check-circle' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $usuarios->links() }}</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>