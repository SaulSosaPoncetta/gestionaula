@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-eye me-2"></i>Detalle de calificación</h4>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('calificaractividad.edit', $estado) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('calificaractividad.calificadas', ['materia_id' => $estado->actividad->materia_id, 'curso_id' => $estado->actividad->curso_id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Alumno</div>
                <div class="fw-semibold">{{ $estado->alumno->nombre_completo }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Actividad</div>
                <div class="fw-semibold">{{ $estado->actividad?->titulo ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Materia</div>
                <div class="fw-semibold">{{ $estado->actividad?->materia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Curso</div>
                <div class="fw-semibold">{{ $estado->actividad?->curso?->nombre_completo ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Tema</div>
                <div class="fw-semibold">{{ $estado->actividad?->tema ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Estado</div>
                <span class="badge bg-{{ $estado->estadobadge }} fs-6">{{ $estado->estadolabel }}</span>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold">{{ $estado->fechaestado?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Nota</div>
                <div class="fw-semibold fs-5">
                    @if($estado->nota !== null)
                        <span class="badge bg-{{ $estado->nota >= 7 ? 'success' : ($estado->nota >= 4 ? 'warning' : 'danger') }} fs-5">
                            {{ number_format($estado->nota, 2) }}
                        </span>
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha inicio actividad</div>
                <div class="fw-semibold">{{ $estado->actividad?->fechainicio?->format('d/m/Y') ?? '—' }}</div>
            </div>
            @if($estado->observacion)
            <div class="col-12">
                <div class="text-muted small">Observación</div>
                <div>{{ $estado->observacion }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection