@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-richtext me-2"></i>Detalle del contenido</h4>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('contenidos.edit', $contenido) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('contenidos.index', ['materia_id' => $contenido->materia_id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Materia</div>
                <div class="fw-semibold fs-5">{{ $contenido->materia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Número de unidad</div>
                <div class="fw-semibold fs-5">
                    @if($contenido->numerounidad)
                        <span class="badge bg-secondary fs-6">Unidad {{ $contenido->numerounidad }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha de registro</div>
                <div class="fw-semibold">{{ $contenido->fecha->format('d/m/Y') }}</div>
            </div>

            <div class="col-12"><hr></div>

            <div class="col-12">
                <div class="text-muted small">Tema</div>
                <div class="fw-semibold fs-5">{{ $contenido->tema }}</div>
            </div>

            @if($contenido->subtemas->isNotEmpty())
            <div class="col-12">
                <div class="text-muted small mb-1">Subtemas</div>
                <ul class="mb-0">
                    @foreach($contenido->subtemas as $sub)
                        <li>{{ $sub->subtema }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($contenido->observacion)
            <div class="col-12">
                <div class="text-muted small mb-1">Observación</div>
                <div>{{ $contenido->observacion }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection