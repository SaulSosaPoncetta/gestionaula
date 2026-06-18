@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        @if($temasExistentes->isNotEmpty())
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Agregar temas a unidad existente</h4>
            <p class="text-muted">Se muestran los temas ya cargados. Agregá nuevos temas abajo y presioná Guardar.</p>
        @else
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo contenido</h4>
            <p class="text-muted">Cargá la materia, la unidad y todos los temas con sus subtemas.</p>
        @endif
    </div>
    <div class="col-auto">
        <a href="{{ route('contenidos.index', ['materia_id' => request('materia_id')]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Temas existentes (solo lectura) --}}
@if($temasExistentes->isNotEmpty())
<div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
    <div class="card-header bg-white fw-semibold text-info">
        <i class="bi bi-eye me-1"></i>Temas ya registrados en esta unidad
        <span class="badge bg-info ms-2">{{ $temasExistentes->count() }}</span>
    </div>
    <div class="card-body py-2">
        @foreach($temasExistentes as $i => $tema)
        <div class="border-start border-3 border-success ps-3 py-2 {{ !$loop->last ? 'mb-2' : '' }}">
            <div class="d-flex align-items-start gap-2">
                <span class="badge bg-success mt-1">{{ $i + 1 }}</span>
                <div>
                    <div class="fw-semibold">{{ $tema->tema }}</div>
                    @if($tema->subtemas->isNotEmpty())
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($tema->subtemas->sortBy('orden') as $sub)
                        <li class="small text-muted">{{ $sub->subtema }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<form method="POST" action="{{ route('contenidos.store') }}" id="formContenido">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Encabezado de unidad --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-bookmark me-1 text-primary"></i>Datos de la unidad
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" class="form-select @error('materia_id') is-invalid @enderror" required>
                        <option value="">— Seleccioná una materia —</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ (old('materia_id', request('materia_id')) == $materia->id) ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° de Unidad</label>
                    <input type="number" name="numerounidad" class="form-control"
                           value="{{ old('numerounidad', request('numerounidad')) }}"
                           min="1" placeholder="Ej: 1">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Observación general</label>
                    <input type="text" name="observacion" class="form-control"
                           value="{{ old('observacion') }}" placeholder="Opcional">
                </div>
            </div>
        </div>
    </div>

    {{-- Nuevos temas --}}
    <div class="mb-2 d-flex align-items-center gap-2">
        <h6 class="fw-bold mb-0">
            @if($temasExistentes->isNotEmpty())
                <i class="bi bi-plus-circle text-primary me-1"></i>Nuevos temas a agregar
            @else
                <i class="bi bi-list-ul text-primary me-1"></i>Temas de la unidad
            @endif
        </h6>
    </div>

    <div id="contenedorTemas">
        <div class="card border-0 shadow-sm mb-3 tarjeta-tema" data-tema-idx="0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-file-text me-1 text-success"></i>
                    <span class="etiqueta-tema">Tema {{ $temasExistentes->count() + 1 }}</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-tema">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tema <span class="text-danger">*</span></label>
                    <input type="text" name="temas[0][tema]" class="form-control"
                           value="{{ old('temas.0.tema') }}"
                           placeholder="Descripción del tema" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-list-ul me-1"></i>Subtemas
                    </label>
                    <div class="contenedor-subtemas" data-tema-idx="0">
                        <div class="input-group input-group-sm mb-2 fila-subtema">
                            <span class="input-group-text bg-light text-muted">1</span>
                            <input type="text" name="temas[0][subtemas][]"
                                   class="form-control" placeholder="Subtema...">
                            <button type="button" class="btn btn-outline-danger btn-quitar-subtema">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-agregar-subtema mt-1"
                            data-tema-idx="0">
                        <i class="bi bi-plus me-1"></i>Agregar subtema
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="button" class="btn btn-outline-primary" id="btnAgregarTema">
            <i class="bi bi-plus-circle me-1"></i>Agregar otro tema
        </button>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Guardar temas
        </button>
        <a href="{{ route('contenidos.index', ['materia_id' => request('materia_id')]) }}"
           class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
let indiceTema = 1;
const offsetTema = {{ $temasExistentes->count() }};

function actualizarEtiquetasTemas() {
    document.querySelectorAll('.tarjeta-tema').forEach((card, i) => {
        card.querySelector('.etiqueta-tema').textContent = 'Tema ' + (offsetTema + i + 1);
    });
}

function actualizarNumerosSubtemas(contenedor) {
    contenedor.querySelectorAll('.fila-subtema').forEach((fila, i) => {
        const span = fila.querySelector('.input-group-text');
        if (span) span.textContent = i + 1;
    });
}

function crearFilaSubtema(temaIdx, valor = '') {
    const div = document.createElement('div');
    div.className = 'input-group input-group-sm mb-2 fila-subtema';
    div.innerHTML = `
        <span class="input-group-text bg-light text-muted">?</span>
        <input type="text" name="temas[${temaIdx}][subtemas][]"
               class="form-control" placeholder="Subtema..." value="${valor}">
        <button type="button" class="btn btn-outline-danger btn-quitar-subtema">
            <i class="bi bi-x"></i>
        </button>`;
    return div;
}

function crearTarjetaTema(idx) {
    const div = document.createElement('div');
    div.className = 'card border-0 shadow-sm mb-3 tarjeta-tema';
    div.dataset.temaIdx = idx;
    div.innerHTML = `
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">
                <i class="bi bi-file-text me-1 text-success"></i>
                <span class="etiqueta-tema">Tema</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-tema">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tema <span class="text-danger">*</span></label>
                <input type="text" name="temas[${idx}][tema]" class="form-control"
                       placeholder="Descripción del tema" required>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-list-ul me-1"></i>Subtemas
                </label>
                <div class="contenedor-subtemas" data-tema-idx="${idx}"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-agregar-subtema mt-1"
                        data-tema-idx="${idx}">
                    <i class="bi bi-plus me-1"></i>Agregar subtema
                </button>
            </div>
        </div>`;
    const cont = div.querySelector('.contenedor-subtemas');
    const fila = crearFilaSubtema(idx);
    cont.appendChild(fila);
    actualizarNumerosSubtemas(cont);
    return div;
}

document.getElementById('btnAgregarTema').addEventListener('click', function() {
    const contenedor = document.getElementById('contenedorTemas');
    contenedor.appendChild(crearTarjetaTema(indiceTema));
    indiceTema++;
    actualizarEtiquetasTemas();
});

document.getElementById('contenedorTemas').addEventListener('click', function(e) {
    if (e.target.closest('.btn-quitar-tema')) {
        if (document.querySelectorAll('.tarjeta-tema').length > 1) {
            e.target.closest('.tarjeta-tema').remove();
            actualizarEtiquetasTemas();
        }
        return;
    }
    if (e.target.closest('.btn-agregar-subtema')) {
        const btn = e.target.closest('.btn-agregar-subtema');
        const tarjeta = btn.closest('.tarjeta-tema');
        const temaIdx = tarjeta.dataset.temaIdx;
        const cont = tarjeta.querySelector('.contenedor-subtemas');
        cont.appendChild(crearFilaSubtema(temaIdx));
        actualizarNumerosSubtemas(cont);
        return;
    }
    if (e.target.closest('.btn-quitar-subtema')) {
        const fila = e.target.closest('.fila-subtema');
        const cont = fila.closest('.contenedor-subtemas');
        if (cont.querySelectorAll('.fila-subtema').length > 1) {
            fila.remove();
            actualizarNumerosSubtemas(cont);
        } else {
            fila.querySelector('input').value = '';
        }
        return;
    }
});

actualizarEtiquetasTemas();
</script>
@endpush
