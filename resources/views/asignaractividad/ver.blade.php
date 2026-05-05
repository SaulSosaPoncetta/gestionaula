@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-arrow me-2"></i>Asignar actividad</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('actividades.seleccionar') }}"
           class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva actividad
        </a>
        <a href="{{ route('asignaractividad.seleccionar') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- Columna izquierda: Actividades --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-check me-1"></i>Actividades ({{ $actividades->count() }})</span>
                <a href="{{ route('actividades.seleccionar') }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle me-1"></i>Nueva
                </a>
            </div>
            <div class="card-body p-0">
                @if($actividades->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-clipboard2 fs-2 d-block mb-2"></i>
                        No hay actividades para esta materia y curso.
                    </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($actividades as $act)
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $act->titulo }}</div>
                                <div class="text-muted small mt-1">
                                    <span class="badge bg-secondary me-1">
                                        {{ $act->tipoactividad?->denominacion ?? '—' }}
                                    </span>
                                    @if($act->esgrupal)
                                        <span class="badge bg-info me-1">
                                            Grupal ({{ $act->integrantesporgrupo }} por grupo)
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark me-1">Individual</span>
                                    @endif
                                    <span class="badge bg-{{ $act->estado === 'activa' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($act->estado) }}
                                    </span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-calendar me-1"></i>
                                    Inicio: {{ $act->fechainicio->format('d/m/Y') }}
                                    &mdash;
                                    Entrega: {{ $act->fechaentrega->format('d/m/Y') }}
                                </div>
                                @if($act->descripcion)
                                    <div class="text-muted small mt-1">{{ $act->descripcion }}</div>
                                @endif

                                {{-- Grupos si es grupal --}}
                                @if($act->esgrupal && $act->grupos->isNotEmpty())
                                <div class="mt-2">
                                    <button class="btn btn-xs btn-outline-secondary"
                                            style="font-size:0.75rem;padding:2px 8px"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#grupos{{ $act->id }}">
                                        <i class="bi bi-people me-1"></i>Ver grupos ({{ $act->grupos->count() }})
                                    </button>
                                    <div class="collapse mt-2" id="grupos{{ $act->id }}">
                                        <div class="row g-2">
                                            @foreach($act->grupos as $grupo)
                                            <div class="col-md-6">
                                                <div class="card border shadow-sm">
                                                    <div class="card-header bg-light py-1 small fw-semibold">
                                                        {{ $grupo->nombre }}
                                                    </div>
                                                    <div class="card-body py-1 px-2">
                                                        @foreach($grupo->alumnos->sortBy('apellido') as $al)
                                                            <div class="small">
                                                                <i class="bi bi-person me-1 text-muted"></i>
                                                                {{ $al->nombre_completo }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <a href="{{ route('actividades.show', $act) }}"
                               class="btn btn-sm btn-outline-primary ms-3">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Columna derecha: Material teórico --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-pdf me-1"></i>Material teórico ({{ $materialteoricoarchivos->count() }})</span>
                <a href="{{ route('materialteoricoarchivos.create') }}"
                   class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-plus-circle me-1"></i>Subir
                </a>
            </div>
            <div class="card-body p-0">
                @if($materialteoricoarchivos->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-file-earmark-pdf fs-2 d-block mb-2"></i>
                        No hay material teórico cargado para este curso.
                    </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($materialteoricoarchivos as $arch)
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ $arch->titulo }}</div>
                                @if($arch->descripcion)
                                    <div class="text-muted small">{{ $arch->descripcion }}</div>
                                @endif
                                @if($arch->tarea)
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-link me-1"></i>
                                        Práctico: {{ $arch->tarea->titulo }}
                                    </div>
                                @endif
                                <div class="text-muted small">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $arch->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $arch->ruta) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-danger ms-3">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
