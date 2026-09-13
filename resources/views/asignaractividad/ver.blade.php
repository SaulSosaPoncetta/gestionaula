@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-arrow me-2"></i>Actividades asignadas</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('asignarnuevo.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>Asignar nueva actividad
        </a>
        <a href="{{ route('asignaractividad.seleccionar') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- Columna izquierda: Actividades asignadas --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clipboard2-check me-1"></i>
                Actividades asignadas ({{ $asignaciones->count() }})
            </div>
            <div class="card-body p-0">
                @if($asignaciones->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-clipboard2 fs-2 d-block mb-2"></i>
                        No hay actividades asignadas para esta materia y curso.
                    </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($asignaciones as $asig)
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">

                                {{-- Titulo y badges --}}
                                <div class="fw-semibold fs-6 mb-1">
                                    {{ $asig->actividad?->tema ?? '—' }}
                                </div>

                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @if($asig->actividad?->numerounidad)
                                        <span class="badge bg-secondary">
                                            Unidad {{ $asig->actividad->numerounidad }}
                                        </span>
                                    @endif
                                    @if($asig->actividad?->numeroactividad)
                                        <span class="badge bg-secondary">
                                            Act. N° {{ $asig->actividad->numeroactividad }}
                                        </span>
                                    @endif
                                    @if($asig->actividad?->tipoactividad)
                                        <span class="badge bg-info">
                                            {{ $asig->actividad->tipoactividad->denominacion }}
                                        </span>
                                    @endif
                                    @if($asig->esgrupal)
                                        <span class="badge bg-warning text-dark">
                                            Grupal ({{ $asig->integrantesporgrupo }} por grupo)
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">Individual</span>
                                    @endif

                                    {{-- Estado de la asignación --}}
                                    <span class="badge bg-{{ $asig->estado === 'activa' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($asig->estado) }}
                                    </span>

                                    {{-- Conteos por estado de alumnos --}}
                                    @if(($asig->conteo_enproceso ?? 0) > 0)
                                    <span class="badge bg-primary" title="Alumnos en proceso">
                                        <i class="bi bi-hourglass-split me-1"></i>En proceso: {{ $asig->conteo_enproceso ?? 0 }}
                                    </span>
                                    @endif

                                    @if(($asig->conteo_atiempo ?? 0) > 0)
                                    <span class="badge bg-success" title="Entregado a tiempo">
                                        <i class="bi bi-check-circle me-1"></i>A tiempo: {{ $asig->conteo_atiempo ?? 0 }}
                                    </span>
                                    @endif

                                    @if(($asig->conteo_vencido_entregado ?? 0) > 0)
                                    <span class="badge bg-warning text-dark" title="Entregado fuera de fecha">
                                        <i class="bi bi-clock-history me-1"></i>Entregado vencido: {{ $asig->conteo_vencido_entregado ?? 0 }}
                                    </span>
                                    @endif

                                    @if(($asig->conteo_vencida ?? 0) > 0)
                                    <span class="badge bg-danger" title="Sin entregar y vencida">
                                        <i class="bi bi-x-circle me-1"></i>Vencidos: {{ $asig->conteo_vencida ?? 0 }}
                                    </span>
                                    @endif

                                    @if(($asig->conteo_incompleta ?? 0) > 0)
                                    <span class="badge bg-secondary" title="Entrega incompleta">
                                        <i class="bi bi-dash-circle me-1"></i>Incompletos: {{ $asig->conteo_incompleta ?? 0 }}
                                    </span>
                                    @endif
                                </div>

                                {{-- Subtema --}}
                                @if($asig->actividad?->subtema)
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-bookmark me-1"></i>{{ $asig->actividad->subtema }}
                                    </div>
                                @endif

                                {{-- Fechas --}}
                                <div class="text-muted small mb-1">
                                    <i class="bi bi-calendar me-1"></i>
                                    Inicio: {{ $asig->fechainicio->format('d/m/Y') }}
                                    &mdash;
                                    Entrega: {{ $asig->fechaentrega->format('d/m/Y') }}
                                </div>

                                {{-- Items count --}}
                                <div class="text-muted small">
                                    <i class="bi bi-list-ol me-1"></i>
                                    {{ $asig->actividad?->items->count() ?? 0 }} consigna(s)
                                </div>

                            </div>

                            {{-- Botón ver detalle --}}
                            <a href="{{ route('asignaractividad.detalle', $asig) }}"
                               class="btn btn-sm btn-outline-primary ms-3">
                                <i class="bi bi-eye me-1"></i>Ver
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
                <span>
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    Material teórico ({{ $materialteoricoarchivos->count() }})
                </span>
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
                        <div class="fw-semibold mb-1">{{ $arch->titulo }}</div>
                        @if($arch->descripcion)
                            <div class="text-muted small mb-1">{{ $arch->descripcion }}</div>
                        @endif
                        @if($arch->tarea)
                            <div class="text-muted small mb-1">
                                <i class="bi bi-link me-1"></i>
                                Práctico: {{ $arch->tarea->titulo }}
                            </div>
                        @endif
                        <div class="text-muted small mb-2">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $arch->created_at->format('d/m/Y') }}
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ asset('storage/' . $arch->ruta) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-file-pdf me-1"></i>Ver PDF
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAsignar{{ $arch->id }}">
                                <i class="bi bi-send me-1"></i>Asignar
                            </button>
                        </div>

                        {{-- Modal asignar material --}}
                        <div class="modal fade" id="modalAsignar{{ $arch->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">
                                            <i class="bi bi-send me-1"></i>Asignar material a práctico
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted small mb-3">
                                            Seleccioná la actividad a la que querés asignar
                                            <strong>{{ $arch->titulo }}</strong>:
                                        </p>
                                        <form method="POST"
                                              action="{{ route('materialteoricoarchivos.asignar', $arch) }}">
                                            @csrf @method('PUT')
                                            <select name="tarea_id" class="form-select mb-3" required>
                                                <option value="">— Seleccioná una actividad —</option>
                                                @foreach($asignaciones as $asig)
                                                    <option value="{{ $asig->actividad_id }}">
                                                        {{ $asig->actividad?->tema ?? '—' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-check-circle me-1"></i>Asignar
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline-secondary btn-sm"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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