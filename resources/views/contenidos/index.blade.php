@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-richtext me-2"></i>Contenidos</h4>
        <p class="text-muted">Registro de unidades, temas y subtemas por materia.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('contenidos.create', ['materia_id' => request('materia_id')]) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva unidad
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('contenidos.index') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Filtrar por materia</label>
                <select name="materia_id" id="materia_id" class="form-select" onchange="this.form.submit()">
                    <option value="">— Seleccioná una materia —</option>
                    @foreach($materias as $materia)
                        <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                            {{ $materia->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('materia_id'))
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('contenidos.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@if(!request('materia_id'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Seleccioná una materia para ver sus contenidos.
    </div>
@elseif($porUnidad->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay contenidos registrados para esta materia.
    </div>
@else

{{-- Encabezado de columnas --}}
<div class="card border-0 shadow-sm mb-2">
    <div class="card-body py-2 px-3">
        <div class="row fw-semibold small text-muted">
            <div class="col-2"><i class="bi bi-bookmark me-1"></i>Unidad</div>
            <div class="col-4"><i class="bi bi-book me-1"></i>Materia</div>
            <div class="col-4"><i class="bi bi-file-text me-1"></i>Temas</div>
            <div class="col-2 text-end"><i class="bi bi-list me-1"></i>Subtemas</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-2">
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleTodosContenidos(true)">
        <i class="bi bi-arrows-expand me-1"></i>Expandir todo
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleTodosContenidos(false)">
        <i class="bi bi-arrows-collapse me-1"></i>Contraer todo
    </button>
</div>

<div class="accordion" id="acordeonContenidos">
    @foreach($porUnidad->sortKeys() as $unidad => $temas)
    @php
        $collapseId = 'unidad_' . ($unidad === 'sin_unidad' ? 'libre' : $unidad);
        $totalTemas    = $temas->count();
        $totalSubtemas = $temas->sum(fn($t) => $t->subtemas->count());
        $primeraMateria = $temas->first()->materia;
    @endphp
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white p-0">
            <button type="button"
                    class="btn w-100 text-start d-flex justify-content-between align-items-center py-3 px-3"
                    data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                    aria-expanded="false" aria-controls="{{ $collapseId }}">
                <div class="row w-100 align-items-center">
                    <div class="col-2">
                        <i class="bi bi-chevron-down acordeon-icono me-1"></i>
                        @if($unidad === 'sin_unidad')
                            <span class="badge bg-secondary">S/U</span>
                        @else
                            <span class="badge bg-primary">U{{ $unidad }}</span>
                        @endif
                    </div>
                    <div class="col-4 text-muted small fw-semibold">
                        {{ $primeraMateria?->nombre }}
                    </div>
                    <div class="col-4 small text-muted">
                        @foreach($temas->take(2) as $t)
                            <div class="text-truncate" style="max-width:220px">{{ $t->tema }}</div>
                        @endforeach
                        @if($temas->count() > 2)
                            <div class="text-primary small">+ {{ $temas->count() - 2 }} más...</div>
                        @endif
                    </div>
                    <div class="col-2 text-end">
                        <span class="badge bg-success">{{ $totalTemas }} T</span>
                        @if($totalSubtemas > 0)
                        <span class="badge bg-info">{{ $totalSubtemas }} S</span>
                        @endif
                    </div>
                </div>
            </button>
        </div>
        <div class="collapse" id="{{ $collapseId }}">
            <div class="card-body pt-0 pb-2">
                @foreach($temas as $contenido)
                <div class="border-start border-3 border-success ps-3 py-2 my-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                <i class="bi bi-file-text me-1 text-success"></i>
                                {{ $contenido->tema }}
                            </div>
                            @if($contenido->subtemas->isNotEmpty())
                            <ul class="mb-1 mt-1 ps-3">
                                @foreach($contenido->subtemas->sortBy('orden') as $sub)
                                <li class="small text-muted">{{ $sub->subtema }}</li>
                                @endforeach
                            </ul>
                            @endif
                            @if($contenido->observacion)
                            <div class="small text-muted mt-1">
                                <i class="bi bi-chat-text me-1"></i>{{ $contenido->observacion }}
                            </div>
                            @endif
                        </div>
                        <div class="d-flex gap-1 ms-3 flex-shrink-0">
                            <a href="{{ route('contenidos.edit', $contenido) }}"
                               class="btn btn-sm btn-secondary">
                                <i class="bi bi-pencil text-white"></i>
                            </a>
                            <form method="POST" action="{{ route('contenidos.destroy', $contenido) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Eliminar este tema?')">
                                    <i class="bi bi-trash text-white"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="ps-3 pt-1 pb-2">
                    <a href="{{ route('contenidos.create', ['materia_id' => request('materia_id'), 'numerounidad' => $unidad]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus me-1"></i>Agregar temas a esta unidad
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endif
@endsection

@push('scripts')
<script>
function toggleTodosContenidos(expandir) {
    document.querySelectorAll('#acordeonContenidos .collapse').forEach(function(el) {
        const inst = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
        expandir ? inst.show() : inst.hide();
    });
}
</script>
<style>
    .acordeon-icono { transition: transform 0.2s ease; }
    button[aria-expanded="true"] .acordeon-icono { transform: rotate(-180deg); }
</style>
@endpush