@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-check me-2"></i>{{ $actividad->titulo }}</h4>
        <p class="text-muted">
            {{ $actividad->materia?->nombre }} &mdash;
            {{ $actividad->curso?->nombre_completo }} &mdash;
            {{ $actividad->tipoactividad?->denominacion }}
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
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Fecha inicio</div>
                <div class="fw-semibold">{{ $actividad->fechainicio->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha entrega</div>
                <div class="fw-semibold">{{ $actividad->fechaentrega->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Modalidad</div>
                <div class="fw-semibold">
                    @if($actividad->esgrupal)
                        <span class="badge bg-info">Grupal ({{ $actividad->integrantesporgrupo }} por grupo)</span>
                    @else
                        <span class="badge bg-secondary">Individual</span>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Estado</div>
                <span class="badge bg-{{ $actividad->estado === 'activa' ? 'success' : 'secondary' }}">
                    {{ ucfirst($actividad->estado) }}
                </span>
            </div>
            @if($actividad->descripcion)
            <div class="col-12">
                <div class="text-muted small">Descripción</div>
                <div>{{ $actividad->descripcion }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Grupos --}}
@if($actividad->esgrupal && $actividad->grupos->isNotEmpty())
<h5 class="fw-bold mb-3"><i class="bi bi-people me-1"></i>Grupos</h5>
<div class="row g-3">
    @foreach($actividad->grupos as $grupo)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white fw-semibold">
                {{ $grupo->nombre }}
                <span class="badge bg-white text-primary ms-2">{{ $grupo->alumnos->count() }} integrantes</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($grupo->alumnos->sortBy('apellido') as $alumno)
                    <li class="list-group-item">
                        <i class="bi bi-person me-1 text-muted"></i>
                        {{ $alumno->nombre_completo }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>
@elseif(!$actividad->esgrupal)
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>Esta actividad es individual.
</div>
@endif
@endsection