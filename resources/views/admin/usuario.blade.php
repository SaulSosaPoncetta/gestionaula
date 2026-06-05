<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docente — Admin GestiónAula</title>
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
        <a href="{{ route('admin.planes.index') }}" class="nav-link">
            <i class="bi bi-box me-2"></i>Planes
        </a>
        <a href="{{ route('admin.pagos.index') }}" class="nav-link">
            <i class="bi bi-cash-stack me-2"></i>Pagos
        </a>
    </nav>
    <div class="mt-auto pb-2">
        <hr class="border-secondary">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
            <p class="text-muted mb-0">{{ $user->email }}</p>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.toggle', $user) }}">
                @csrf
                <button type="submit"
                        class="btn {{ $user->activo ? 'btn-danger' : 'btn-success' }}"
                        onclick="return confirm('{{ $user->activo ? 'Suspender' : 'Activar' }} esta cuenta?')">
                    <i class="bi bi-{{ $user->activo ? 'slash-circle' : 'check-circle' }} me-1"></i>
                    {{ $user->activo ? 'Suspender cuenta' : 'Activar cuenta' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">

        {{-- Suscripción actual --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-credit-card me-1 text-primary"></i>Suscripción actual
                </div>
                <div class="card-body">
                    @if($user->suscripcion)
                    @php $sus = $user->suscripcion; @endphp
                    <table class="table table-sm mb-3">
                        <tr>
                            <td class="text-muted">Plan</td>
                            <td class="fw-semibold">{{ $sus->plan?->nombre ?? 'Personalizado' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Monto mensual</td>
                            <td class="fw-semibold">${{ number_format($sus->montomensual, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado</td>
                            <td><span class="badge bg-{{ $sus->estadobadge }}">{{ $sus->estadolabel }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Inicio</td>
                            <td>{{ $sus->fechainicio->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Próximo pago</td>
                            <td class="{{ $sus->proximopago?->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $sus->proximopago?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2">
                        @if($sus->estado === 'activa')
                            <form method="POST" action="{{ route('admin.suscripciones.suspender', $sus) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning"
                                        onclick="return confirm('Suspender suscripción?')">
                                    <i class="bi bi-pause-circle me-1"></i>Suspender
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.suscripciones.activar', $sus) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-play-circle me-1"></i>Activar
                                </button>
                            </form>
                        @endif
                    </div>
                    @else
                        <div class="text-muted text-center py-2">
                            <i class="bi bi-exclamation-circle d-block fs-3 mb-2"></i>
                            Sin suscripción activa.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Nueva suscripción --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-plus-circle me-1 text-success"></i>Asignar suscripción
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.suscripciones.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Plan</label>
                            <select name="plan_id" class="form-select form-select-sm">
                                <option value="">— Sin plan base —</option>
                                @foreach($planes as $plan)
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->nombre }} — ${{ number_format($plan->precio, 2) }}/{{ $plan->periodicidad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Monto mensual <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" name="montomensual" class="form-control"
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fecha inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fechainicio" class="form-control form-control-sm"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Próximo pago</label>
                            <input type="date" name="proximopago" class="form-control form-control-sm">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="form-control form-control-sm" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-check-circle me-1"></i>Guardar suscripción
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Historial de pagos --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cash-stack me-1 text-success"></i>Historial de pagos</span>
                    @if($user->suscripcion)
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal" data-bs-target="#modalGenerarPago">
                        <i class="bi bi-plus-circle me-1"></i>Generar pago
                    </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($user->pagos->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-cash fs-2 d-block mb-2"></i>Sin pagos registrados.
                        </div>
                    @else
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Período</th>
                                <th class="text-end">Monto</th>
                                <th>Vence</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->pagos as $pago)
                            <tr>
                                <td class="ps-3 small">
                                    {{ $pago->periododesde->format('d/m/Y') }}
                                    — {{ $pago->periodohasta->format('d/m/Y') }}
                                </td>
                                <td class="text-end fw-semibold">${{ number_format($pago->monto, 2) }}</td>
                                <td class="{{ $pago->estado === 'pendiente' && $pago->fechapago->isPast() ? 'text-danger fw-bold' : '' }} small">
                                    {{ $pago->fechapago->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $pago->estadobadge }}">{{ $pago->estadolabel }}</span>
                                </td>
                                <td class="text-center">
                                    @if($pago->estado === 'pendiente')
                                        <button type="button" class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalPago{{ $pago->id }}">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                        <form method="POST"
                                              action="{{ route('admin.pagos.vencido', $pago) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Marcar vencido">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>

                                        {{-- Modal registrar pago --}}
                                        <div class="modal fade" id="modalPago{{ $pago->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">
                                                            <i class="bi bi-cash me-1"></i>Registrar pago
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.pagos.registrar') }}"
                                                          enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="pago_id" value="{{ $pago->id }}">
                                                        <div class="modal-body">
                                                            <div class="alert alert-light border mb-3">
                                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                                <div class="text-muted small">
                                                                    Período: {{ $pago->periododesde->format('d/m/Y') }}
                                                                    — {{ $pago->periodohasta->format('d/m/Y') }}
                                                                </div>
                                                                <div class="fs-5 fw-bold text-success">
                                                                    ${{ number_format($pago->monto, 2) }}
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Método de pago</label>
                                                                <select name="metodopago" class="form-select" required>
                                                                    @foreach(\App\Models\Pago::METODOS as $val => $label)
                                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Fecha de pago</label>
                                                                <input type="date" name="fechapago" class="form-control"
                                                                       value="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Comprobante</label>
                                                                <input type="file" name="comprobante" class="form-control"
                                                                       accept="image/*,application/pdf">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Observaciones</label>
                                                                <textarea name="observaciones" class="form-control" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                    data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check-circle me-1"></i>Confirmar pago
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($pago->comprobante)
                                        <a href="{{ asset('storage/' . $pago->comprobante) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary">
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal generar pago --}}
@if($user->suscripcion)
<div class="modal fade" id="modalGenerarPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-1"></i>Generar nuevo pago
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.pagos.generar') }}">
                @csrf
                <input type="hidden" name="suscripcion_id" value="{{ $user->suscripcion->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="monto" class="form-control"
                                       value="{{ $user->suscripcion->montomensual }}"
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Período desde</label>
                            <input type="date" name="periododesde" class="form-control"
                                   value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Período hasta</label>
                            <input type="date" name="periodohasta" class="form-control"
                                   value="{{ date('Y-m-t') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Fecha vencimiento</label>
                            <input type="date" name="fechapago" class="form-control"
                                   value="{{ $user->suscripcion->proximopago?->format('Y-m-d') ?? date('Y-m-d') }}"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Generar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>