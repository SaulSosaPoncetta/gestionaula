@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold">
            <i class="bi bi-journal-bookmark me-2"></i>{{ $planificacion->materia?->nombre }}
        </h4>
        <p class="text-muted mb-0">
            Ciclo lectivo: <strong>{{ $planificacion->ciclo }}</strong>
            &mdash; Docente: <strong>{{ $planificacion->docente->name }}</strong>
        </p>
        @if($planificacion->descripcion)
            <p class="text-muted small mt-1">{{ $planificacion->descripcion }}</p>
        @endif
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('planificaciones.edit', $planificacion) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('planificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Unidades existentes --}}
@forelse($planificacion->unidades as $unidad)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <i class="bi bi-collection me-1"></i>
            Unidad {{ $unidad->orden }}: {{ $unidad->nombre }}
            <span class="badge bg-white text-primary ms-2">{{ $unidad->numeroclases }} clases</span>
        </span>
        <form method="POST"
              action="{{ route('planificaciones.unidades.destroy', [$planificacion, $unidad]) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-light"
                    onclick="return confirm('¿Eliminar esta unidad?')">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="row g-4">

            {{-- Contenidos --}}
            <div class="col-md-6">
                <h6 class="fw-semibold text-muted mb-2">
                    <i class="bi bi-journal-richtext me-1"></i>Contenidos
                </h6>
                @if($unidad->contenidos->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($unidad->contenidos as $cont)
                        <li class="list-group-item px-0 py-1 border-0">
                            <i class="bi bi-dot text-primary"></i>
                            {{ $cont->tema }}
                            <span class="text-muted small">({{ $cont->fecha->format('d/m/Y') }})</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted small">Sin contenidos asignados.</span>
                @endif
            </div>

            {{-- Objetivos aprendizaje --}}
            <div class="col-md-6">
                <h6 class="fw-semibold text-muted mb-2">
                    <i class="bi bi-bullseye me-1"></i>Objetivos de aprendizaje
                </h6>
                @if($unidad->objetivosaprendizaje->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($unidad->objetivosaprendizaje as $obj)
                        <li class="list-group-item px-0 py-1 border-0">
                            <i class="bi bi-dot text-success"></i>{{ $obj->objetivo }}
                        </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted small">Sin objetivos cargados.</span>
                @endif
            </div>

            {{-- Objetivos enseñanza --}}
            <div class="col-md-6">
                <h6 class="fw-semibold text-muted mb-2">
                    <i class="bi bi-mortarboard me-1"></i>Objetivos de enseñanza
                </h6>
                @if($unidad->objetivosensenianza->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($unidad->objetivosensenianza as $obj)
                        <li class="list-group-item px-0 py-1 border-0">
                            <i class="bi bi-dot text-warning"></i>{{ $obj->objetivo }}
                        </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted small">Sin objetivos cargados.</span>
                @endif
            </div>

            {{-- Actividades --}}
            <div class="col-md-3">
                <h6 class="fw-semibold text-muted mb-2">
                    <i class="bi bi-activity me-1"></i>Actividades
                </h6>
                @if($unidad->actividades->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($unidad->actividades as $act)
                        <li class="list-group-item px-0 py-1 border-0">
                            <i class="bi bi-dot text-info"></i>{{ $act->actividad }}
                        </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted small">Sin actividades.</span>
                @endif
            </div>

            {{-- Recursos --}}
            <div class="col-md-3">
                <h6 class="fw-semibold text-muted mb-2">
                    <i class="bi bi-tools me-1"></i>Recursos
                </h6>
                @if($unidad->recursos->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($unidad->recursos as $rec)
                        <li class="list-group-item px-0 py-1 border-0">
                            <i class="bi bi-dot text-secondary"></i>{{ $rec->recurso }}
                        </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted small">Sin recursos.</span>
                @endif
            </div>

            {{-- Archivos PDF --}}
            @if($unidad->archivos->isNotEmpty())
            <div class="col-12">
                <h6 class="fw-semibold text-muted mb-2">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Material teórico
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($unidad->archivos as $archivo)
                    <a href="{{ asset('storage/' . $archivo->ruta) }}"
                       target="_blank" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-file-pdf me-1"></i>{{ $archivo->nombre }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@empty
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>Esta planificación no tiene unidades. Agregá la primera abajo.
</div>
@endforelse

{{-- Formulario para agregar nueva unidad --}}
<div class="card border-0 shadow-sm border-start border-success border-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-plus-circle me-1 text-success"></i>Agregar nueva unidad
    </div>
    <div class="card-body">
        <form method="POST"
              action="{{ route('planificaciones.unidades.store', $planificacion) }}"
              enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                {{-- Nombre y clases --}}
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nombre de la unidad <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" required
                           placeholder="Ej: Unidad 1 - Introducción">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de clases <span class="text-danger">*</span></label>
                    <input type="number" name="numeroclases" class="form-control" min="1" value="1" required>
                </div>

                {{-- Contenidos --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Contenidos <span class="text-muted fw-normal">(seleccioná uno o varios)</span>
                    </label>
                    <div class="border rounded p-2" style="max-height:150px; overflow-y:auto;">
                        @forelse($contenidos as $cont)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="contenidos[]" value="{{ $cont->id }}"
                                   id="cont{{ $cont->id }}">
                            <label class="form-check-label small" for="cont{{ $cont->id }}">
                                <strong>{{ $cont->tema }}</strong>
                                <span class="text-muted">— {{ $cont->materia?->nombre }} ({{ $cont->fecha->format('d/m/Y') }})</span>
                            </label>
                        </div>
                        @empty
                        <span class="text-muted small">No hay contenidos cargados aún.</span>
                        @endforelse
                    </div>
                </div>

                {{-- Objetivos aprendizaje --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Objetivos de aprendizaje
                        <button type="button" class="btn btn-sm btn-outline-success ms-2"
                                onclick="agregarCampo('aprendizaje')">
                            <i class="bi bi-plus"></i>
                        </button>
                    </label>
                    <div id="campos-aprendizaje">
                        <input type="text" name="objetivosaprendizaje[]"
                               class="form-control form-control-sm mb-2"
                               placeholder="Objetivo de aprendizaje 1">
                    </div>
                </div>

                {{-- Objetivos enseñanza --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Objetivos de enseñanza
                        <button type="button" class="btn btn-sm btn-outline-warning ms-2"
                                onclick="agregarCampo('ensenianza')">
                            <i class="bi bi-plus"></i>
                        </button>
                    </label>
                    <div id="campos-ensenianza">
                        <input type="text" name="objetivosensenianza[]"
                               class="form-control form-control-sm mb-2"
                               placeholder="Objetivo de enseñanza 1">
                    </div>
                </div>

                {{-- Actividades --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Tipos de actividades
                        <button type="button" class="btn btn-sm btn-outline-info ms-2"
                                onclick="agregarCampo('actividades')">
                            <i class="bi bi-plus"></i>
                        </button>
                    </label>
                    <div id="campos-actividades">
                        <input type="text" name="actividades[]"
                               class="form-control form-control-sm mb-2"
                               placeholder="Tipo de actividad 1">
                    </div>
                </div>

                {{-- Recursos --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Recursos
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                onclick="agregarCampo('recursos')">
                            <i class="bi bi-plus"></i>
                        </button>
                    </label>
                    <div id="campos-recursos">
                        <input type="text" name="recursos[]"
                               class="form-control form-control-sm mb-2"
                               placeholder="Recurso 1">
                    </div>
                </div>

                {{-- Archivos PDF --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Material teórico en PDF
                        <span class="text-muted fw-normal">(máximo 3 archivos)</span>
                    </label>
                    <input type="file" name="archivos[]" class="form-control"
                           accept="application/pdf" multiple>
                    <div class="form-text">Podés seleccionar hasta 3 archivos PDF a la vez.</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Agregar unidad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function agregarCampo(tipo) {
    const contenedor = document.getElementById(`campos-${tipo}`);
    const cantidad   = contenedor.querySelectorAll('input').length + 1;
    const nombres    = {
        'aprendizaje': 'objetivosaprendizaje',
        'ensenianza':  'objetivosensenianza',
        'actividades': 'actividades',
        'recursos':    'recursos',
    };
    const placeholders = {
        'aprendizaje': 'Objetivo de aprendizaje',
        'ensenianza':  'Objetivo de enseñanza',
        'actividades': 'Tipo de actividad',
        'recursos':    'Recurso',
    };

    const input = document.createElement('input');
    input.type        = 'text';
    input.name        = `${nombres[tipo]}[]`;
    input.className   = 'form-control form-control-sm mb-2';
    input.placeholder = `${placeholders[tipo]} ${cantidad}`;
    contenedor.appendChild(input);
    input.focus();
}
</script>
@endpush