@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-check me-2"></i>{{ $actividad->tema }}</h4>
        <p class="text-muted">
            {{ $actividad->materia?->nombre }}
            @if($actividad->numerounidad)
                &mdash; Unidad {{ $actividad->numerounidad }}
            @endif
            @if($actividad->numeroactividad)
                &mdash; Actividad N° {{ $actividad->numeroactividad }}
            @endif
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Datos generales --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-info-circle me-1"></i>Datos de la actividad
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <div class="text-muted small">N° Unidad</div>
                <div class="fw-semibold">{{ $actividad->numerounidad ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">N° Actividad</div>
                <div class="fw-semibold">{{ $actividad->numeroactividad ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Tipo</div>
                <div class="fw-semibold">{{ $actividad->tipoactividad?->denominacion ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Materia</div>
                <div class="fw-semibold">{{ $actividad->materia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Tema</div>
                <div class="fw-semibold">{{ $actividad->tema }}</div>
            </div>
            @if($actividad->subtema)
            <div class="col-md-6">
                <div class="text-muted small">Subtema</div>
                <div class="fw-semibold">{{ $actividad->subtema }}</div>
            </div>
            @endif
            @if($actividad->descripcion)
            <div class="col-12">
                <div class="text-muted small">Observaciones</div>
                <div>{{ $actividad->descripcion }}</div>
            </div>
            @endif
            <div class="col-md-3">
                <div class="text-muted small">Estado</div>
                <span class="badge bg-{{ $actividad->estado === 'activa' ? 'success' : 'secondary' }}">
                    {{ ucfirst($actividad->estado) }}
                </span>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha de creación</div>
                <div class="fw-semibold">{{ $actividad->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Items / Consignas --}}
@if($actividad->items->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-list-ol me-1"></i>Consignas ({{ $actividad->items->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 text-center" style="width:100px">N° Item</th>
                    <th>Consigna / Pregunta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividad->items as $item)
                <tr>
                    <td class="ps-4 text-center fw-bold fs-5">{{ $item->numeroitem }}</td>
                    <td>{{ $item->texto }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>Esta actividad no tiene consignas cargadas.
</div>
@endif
@endsection