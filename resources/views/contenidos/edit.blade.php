@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar unidad</h4>
        @if($contenido->numerounidad)
            <p class="text-muted mb-0">
                <span class="badge bg-primary">Unidad {{ $contenido->numerounidad }}</span>
                <span class="ms-2">{{ $contenido->materia?->nombre }}</span>
            </p>
        @else
            <p class="text-muted mb-0">{{ $contenido->materia?->nombre }}</p>
        @endif
    </div>
    <div class="col-auto">
        <a href="{{ route('contenidos.index', ['materia_id' => $contenido->materia_id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

{{-- Otros temas de la misma unidad (solo lectura) --}}
@php $otrosTemas = $temasUnidad->where('id', '!=', $contenido->id); @endphp

@if($otrosTemas->isNotEmpty())
<div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
    <div class="card-header bg-white fw-semibold text-info">
        <i class="bi bi-eye me-1"></i>Otros temas de esta unidad
        <span class="badge bg-info ms-1">{{ $otrosTemas->count() }}</span>
        <span class="text-muted fw-normal small ms-2">(solo lectura — usá el lápiz de cada uno para editar)</span>
    </div>
    <div class="card-body py-2">
        @foreach($otrosTemas as $i => $otrTema)
        <div class="border-start border-3 border-secondary ps-3 py-2 {{ !$loop->last ? 'mb-2' : '' }}">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary">{{ $temasUnidad->search(fn($t) => $t->id === $otrTema->id) + 1 }}</span>
                        <span class="fw-semibold">{{ $otrTema->tema }}</span>
                    </div>
                    @if($otrTema->subtemas->isNotEmpty())
                    <ul class="mb-0 mt-1 ps-4">
                        @foreach($otrTema->subtemas->sortBy('orden') as $sub)
                        <li class="small text-muted">{{ $sub->subtema }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <a href="{{ route('contenidos.edit', $otrTema) }}"
                   class="btn btn-sm btn-secondary flex-shrink-0">
                    <i class="bi bi-pencil text-white"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Tema que se está editando --}}
<div class="card border-0 shadow-sm border-start border-4 border-warning mb-4">
    <div class="card-header bg-white fw-semibold text-warning">
        <i class="bi bi-pencil me-1"></i>Editando tema
        <span class="badge bg-warning text-dark ms-1">
            {{ $temasUnidad->search(fn($t) => $t->id === $contenido->id) + 1 }}
        </span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('contenidos.update', $contenido) }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Campos de cabecera: materia, unidad, observación --}}
            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" class="form-select @error('materia_id') is-invalid @enderror" required>
                        <option value="">— Seleccioná una materia —</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ old('materia_id', $contenido->materia_id) == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° de Unidad</label>
                    <input type="number" name="numerounidad" class="form-control"
                           value="{{ old('numerounidad', $contenido->numerounidad) }}"
                           min="1" placeholder="Ej: 1">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Observación general</label>
                    <input type="text" name="observacion" class="form-control"
                           value="{{ old('observacion', $contenido->observacion) }}"
                           placeholder="Opcional">
                </div>
            </div>

            {{-- Tema --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Tema <span class="text-danger">*</span></label>
                <input type="text" name="tema" class="form-control @error('tema') is-invalid @enderror"
                       value="{{ old('tema', $contenido->tema) }}"
                       placeholder="Descripción del tema" required>
                @error('tema')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Subtemas --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-list-ul me-1"></i>Subtemas
                </label>
                @php
                    $subtemas = $contenido->subtemas->sortBy('orden')->pluck('subtema')->toArray();
                    if (empty($subtemas)) $subtemas = [''];
                @endphp
                <div id="contenedorSubtemas">
                    @foreach($subtemas as $i => $sub)
                    <div class="input-group input-group-sm mb-2 fila-subtema">
                        <span class="input-group-text bg-light text-muted">{{ $i + 1 }}</span>
                        <input type="text" name="subtemas[{{ $i }}]" class="form-control"
                               placeholder="Subtema..."
                               value="{{ old("subtemas.$i", $sub) }}">
                        <button type="button" class="btn btn-outline-danger btn-quitar-subtema">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="btnAgregarSubtema">
                    <i class="bi bi-plus me-1"></i>Agregar subtema
                </button>
            </div>

            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-warning text-dark fw-semibold">
                    <i class="bi bi-floppy me-1"></i>Actualizar tema
                </button>
                <a href="{{ route('contenidos.index', ['materia_id' => $contenido->materia_id]) }}"
                   class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let indiceSubtema = {{ count($contenido->subtemas) ?: 1 }};

function actualizarNumeros() {
    document.querySelectorAll('#contenedorSubtemas .fila-subtema').forEach((fila, i) => {
        const span = fila.querySelector('.input-group-text');
        if (span) span.textContent = i + 1;
    });
}

document.getElementById('btnAgregarSubtema').addEventListener('click', function() {
    const cont = document.getElementById('contenedorSubtemas');
    const div  = document.createElement('div');
    div.className = 'input-group input-group-sm mb-2 fila-subtema';
    div.innerHTML = `
        <span class="input-group-text bg-light text-muted">${indiceSubtema + 1}</span>
        <input type="text" name="subtemas[${indiceSubtema}]" class="form-control" placeholder="Subtema...">
        <button type="button" class="btn btn-outline-danger btn-quitar-subtema">
            <i class="bi bi-x"></i>
        </button>`;
    cont.appendChild(div);
    indiceSubtema++;
    actualizarNumeros();
});

document.getElementById('contenedorSubtemas').addEventListener('click', function(e) {
    if (e.target.closest('.btn-quitar-subtema')) {
        const filas = document.querySelectorAll('#contenedorSubtemas .fila-subtema');
        if (filas.length > 1) {
            e.target.closest('.fila-subtema').remove();
            actualizarNumeros();
        } else {
            e.target.closest('.fila-subtema').querySelector('input').value = '';
        }
    }
});
</script>
@endpush
