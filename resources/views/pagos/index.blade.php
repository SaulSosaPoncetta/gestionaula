@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-credit-card me-2"></i>Mis pagos</h4>
        <p class="text-muted">Gestioná tu suscripción y realizá pagos online.</p>
    </div>
</div>

{{-- Alertas --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Estado de la suscripción --}}
@if($suscripcion)
<div class="card border-0 shadow-sm mb-4 border-start border-4
     border-{{ $suscripcion->estado === 'activa' ? 'success' : 'danger' }}">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-patch-check me-2 text-success"></i>
                    Tu suscripción
                    <span class="badge bg-{{ $suscripcion->estadobadge }} ms-2">
                        {{ $suscripcion->estadolabel }}
                    </span>
                </h5>
                <div class="text-muted">
                    Plan: <strong>{{ $suscripcion->plan?->nombre ?? 'Personalizado' }}</strong>
                    &mdash;
                    Monto mensual: <strong>${{ number_format($suscripcion->montomensual, 2) }} ARS</strong>
                </div>
                @if($suscripcion->proximopago)
                <div class="mt-1
                    {{ $suscripcion->proximopago->isPast() ? 'text-danger fw-bold' : 'text-muted' }}">
                    <i class="bi bi-calendar me-1"></i>
                    Próximo pago:
                    {{ $suscripcion->proximopago->format('d/m/Y') }}
                    @if($suscripcion->proximopago->isPast())
                        <span class="badge bg-danger ms-1">Vencido</span>
                    @elseif($suscripcion->proximopago->diffInDays(now()) <= 5)
                        <span class="badge bg-warning text-dark ms-1">Próximo a vencer</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Opciones de pago --}}
<div class="row g-3 mb-4">

    {{-- MercadoPago --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <svg width="180" height="40" viewBox="0 0 180 40" fill="none">
                        <rect width="180" height="40" rx="8" fill="#009EE3"/>
                        <text x="90" y="26" text-anchor="middle" fill="white"
                              font-family="Arial" font-weight="bold" font-size="16">
                            MercadoPago
                        </text>
                    </svg>
                </div>
                <p class="text-muted small mb-3">
                    Pagá con tarjeta de crédito, débito, efectivo o transferencia.
                    <br>Redirige a MercadoPago de forma segura.
                </p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-light text-dark border">Visa</span>
                    <span class="badge bg-light text-dark border">Mastercard</span>
                    <span class="badge bg-light text-dark border">Amex</span>
                    <span class="badge bg-light text-dark border">Rapipago</span>
                    <span class="badge bg-light text-dark border">Pagofácil</span>
                </div>
                <div class="fw-bold fs-5 mb-3">
                    ${{ number_format($suscripcion->montomensual, 2) }} ARS
                </div>
                <form method="POST" action="{{ route('pagos.mp.iniciar') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-credit-card me-2"></i>Pagar con MercadoPago
                    </button>
                </form>
                @if(!env('MP_ACCESS_TOKEN'))
                    <div class="alert alert-warning mt-2 small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Configurá MP_ACCESS_TOKEN en .env para activar.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- PayPal --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <svg width="120" height="40" viewBox="0 0 120 40" fill="none">
                        <rect width="120" height="40" rx="8" fill="#003087"/>
                        <text x="60" y="26" text-anchor="middle" fill="white"
                              font-family="Arial" font-weight="bold" font-size="16">
                            PayPal
                        </text>
                    </svg>
                </div>
                <p class="text-muted small mb-3">
                    Pagá con tu cuenta PayPal o tarjeta de crédito internacional.
                    <br>Pago procesado en USD.
                </p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-light text-dark border">Visa</span>
                    <span class="badge bg-light text-dark border">Mastercard</span>
                    <span class="badge bg-light text-dark border">Amex</span>
                </div>
                <div class="fw-bold fs-5 mb-1">
                    USD {{ number_format($suscripcion->montomensual / 1000, 2) }}
                </div>
                <div class="text-muted small mb-3">
                    (equivalente aprox. ${{ number_format($suscripcion->montomensual, 2) }} ARS)
                </div>
                <form method="POST" action="{{ route('pagos.paypal.iniciar') }}">
                    @csrf
                    <button type="submit" class="btn btn-warning text-dark w-100">
                        <i class="bi bi-paypal me-2"></i>Pagar con PayPal
                    </button>
                </form>
                @if(!env('PAYPAL_CLIENT_ID'))
                    <div class="alert alert-warning mt-2 small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Configurá PAYPAL_CLIENT_ID en .env para activar.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@else
{{-- Sin suscripción --}}
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    No tenés una suscripción activa. Contactá al administrador para asignarte un plan.
</div>
@endif

{{-- Historial de pagos online --}}
@if($pagos->isNotEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-clock-history me-1"></i>Historial de pagos
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Plataforma</th>
                    <th>Período</th>
                    <th class="text-end">Monto</th>
                    <th class="text-center">Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                <tr>
                    <td class="ps-4">
                        <span class="badge bg-{{ $pago->plataforma === 'mercadopago' ? 'primary' : 'warning text-dark' }}">
                            {{ $pago->plataforma === 'mercadopago' ? 'MercadoPago' : 'PayPal' }}
                        </span>
                    </td>
                    <td class="small text-muted">
                        @if($pago->periododesde)
                            {{ $pago->periododesde->format('d/m/Y') }}
                            — {{ $pago->periodohasta?->format('d/m/Y') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-end fw-semibold">
                        {{ $pago->moneda }} {{ number_format($pago->monto, 2) }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $pago->estadobadge }}">
                            {{ $pago->estadolabel }}
                        </span>
                    </td>
                    <td class="small text-muted">
                        {{ $pago->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pagos->links() }}</div>
@endif

@endsection