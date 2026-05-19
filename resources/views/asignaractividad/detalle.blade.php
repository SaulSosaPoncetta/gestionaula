@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-check me-2"></i>Detalle de actividad asignada</h4>
        <p class="text-muted">
            <strong>{{ $asignacion->materia?->nombre }}</strong>
            &mdash;
            <strong>{{ $asignacion->curso?->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asignaractividad.ver', ['materia_id' => $asignacion->materia_id, 'curso_id' => $asignacion->curso_id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Datos de la asignación --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-info-circle me-1"></i>Datos de la asignación
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <div class="text-muted small">N° Unidad</div>
                <div class="fw-semibold">{{ $asignacion->actividad?->numerounidad ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">N° Actividad</div>
                <div class="fw-semibold">{{ $asignacion->actividad?->numeroactividad ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Tipo</div>
                <div class="fw-semibold">{{ $asignacion->actividad?->tipoactividad?->denominacion ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Materia</div>
                <div class="fw-semibold">{{ $asignacion->materia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Tema</div>
                <div class="fw-semibold fs-5">{{ $asignacion->actividad?->tema ?? '—' }}</div>
            </div>
            @if($asignacion->actividad?->subtema)
            <div class="col-md-6">
                <div class="text-muted small">Subtema</div>
                <div class="fw-semibold">{{ $asignacion->actividad->subtema }}</div>
            </div>
            @endif
            <div class="col-md-3">
                <div class="text-muted small">Fecha inicio</div>
                <div class="fw-semibold">{{ $asignacion->fechainicio->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha entrega</div>
                <div class="fw-semibold text-danger">{{ $asignacion->fechaentrega->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Modalidad</div>
                <div>
                    @if($asignacion->esgrupal)
                        <span class="badge bg-warning text-dark">
                            Grupal ({{ $asignacion->integrantesporgrupo }} por grupo)
                        </span>
                    @else
                        <span class="badge bg-light text-dark">Individual</span>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Estado</div>
                <span class="badge bg-{{ $asignacion->estado === 'activa' ? 'success' : 'secondary' }} fs-6">
                    {{ ucfirst($asignacion->estado) }}
                </span>
            </div>
            @if($asignacion->actividad?->descripcion)
            <div class="col-12">
                <div class="text-muted small">Observaciones</div>
                <div>{{ $asignacion->actividad->descripcion }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Consignas --}}
@if($asignacion->actividad?->items->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-list-ol me-1"></i>
        Consignas ({{ $asignacion->actividad->items->count() }})
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
                @foreach($asignacion->actividad->items as $item)
                <tr>
                    <td class="ps-4 text-center fw-bold fs-5">{{ $item->numeroitem }}</td>
                    <td class="py-3">{{ $item->texto }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Grupos si es grupal --}}
@if($asignacion->esgrupal && $asignacion->actividad?->grupos->isNotEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-people me-1"></i>Grupos ({{ $asignacion->actividad->grupos->count() }})
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($asignacion->actividad->grupos as $grupo)
            <div class="col-md-4">
                <div class="card border shadow-sm h-100">
                    <div class="card-header bg-primary text-white fw-semibold d-flex justify-content-between">
                        <span>{{ $grupo->nombre }}</span>
                        <span class="badge bg-white text-primary">
                            {{ $grupo->alumnos->count() }} integrantes
                        </span>
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
    </div>
</div>
@endif
@endsection