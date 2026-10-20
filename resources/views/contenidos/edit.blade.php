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
                    <select name="materia_id" id="selectMateriaEdit"
                            class="form-select @error('materia_id') is-invalid @enderror" required>
                        <option value="">— Seleccioná una materia —</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ old('materia_id', $contenido->materia_id) == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}{{ $materia->anio ? ' '.$materia->anio : '' }}
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

            {{-- Tema + botón currículum --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-semibold mb-0">Tema <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAbrirCurriculumEdit">
                        <i class="bi bi-mortarboard me-1"></i>Sugerir del currículum
                    </button>
                </div>

                {{-- Panel curricular inline --}}
                <div id="panelCurriculumEdit" class="card border border-primary rounded mb-2" style="display:none">
                    <div class="card-body py-2 px-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold mb-1">Eje</label>
                                <select id="editSelectEje" class="form-select form-select-sm" disabled>
                                    <option value="">Cargando...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold mb-1">Núcleo</label>
                                <select id="editSelectNucleo" class="form-select form-select-sm" disabled>
                                    <option value="">— Seleccioná un eje —</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold mb-1">Tema</label>
                                <select id="editSelectTema" class="form-select form-select-sm" disabled>
                                    <option value="">— Seleccioná un núcleo —</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary" id="btnAplicarCurriculumEdit" disabled>
                                <i class="bi bi-check2 me-1"></i>Aplicar al tema
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCerrarCurriculumEdit">
                                Cancelar
                            </button>
                        </div>
                        <div id="editSinDatos" class="text-muted small mt-1" style="display:none">
                            <i class="bi bi-info-circle me-1"></i>Sin diseño curricular para esta materia.
                        </div>
                    </div>
                </div>

                <input type="text" name="tema" id="inputTema"
                       class="form-control @error('tema') is-invalid @enderror"
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
// ── Subtemas ──────────────────────────────────────────────────
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

// ── Panel curricular (edit) ───────────────────────────────────
const rutas = {
    ejes:    '{{ route("curriculum.ejes") }}',
    nucleos: '{{ route("curriculum.nucleos") }}',
    temas:   '{{ route("curriculum.temas") }}',
};

let temasEdit = [];

document.getElementById('btnAbrirCurriculumEdit').addEventListener('click', async function() {
    document.getElementById('panelCurriculumEdit').style.display = '';
    this.style.display = 'none';
    const materiaId = document.getElementById('selectMateriaEdit').value;
    if (materiaId) await cargarEjesEdit(materiaId);
});

document.getElementById('btnCerrarCurriculumEdit').addEventListener('click', function() {
    document.getElementById('panelCurriculumEdit').style.display = 'none';
    document.getElementById('btnAbrirCurriculumEdit').style.display = '';
});

document.getElementById('editSelectEje').addEventListener('change', async function() {
    resetearDesdoNucleoEdit();
    if (this.value) await cargarNucleosEdit(this.value);
});

document.getElementById('editSelectNucleo').addEventListener('change', async function() {
    resetearDesdeTemasEdit();
    if (this.value) await cargarTemasEdit(this.value);
});

document.getElementById('editSelectTema').addEventListener('change', function() {
    document.getElementById('btnAplicarCurriculumEdit').disabled = !this.value;
});

document.getElementById('btnAplicarCurriculumEdit').addEventListener('click', function() {
    const tema = temasEdit.find(t => t.id == document.getElementById('editSelectTema').value);
    if (!tema) return;

    // Rellenar campo tema
    document.getElementById('inputTema').value = tema.nombre;

    // Reemplazar subtemas
    const cont = document.getElementById('contenedorSubtemas');
    cont.innerHTML = '';
    const lista = tema.subtemas && tema.subtemas.length ? tema.subtemas : [''];
    lista.forEach((s, i) => {
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm mb-2 fila-subtema';
        div.innerHTML = `
            <span class="input-group-text bg-light text-muted">${i + 1}</span>
            <input type="text" name="subtemas[${i}]" class="form-control"
                   placeholder="Subtema..." value="${escHtml(s)}">
            <button type="button" class="btn btn-outline-danger btn-quitar-subtema">
                <i class="bi bi-x"></i>
            </button>`;
        cont.appendChild(div);
    });
    indiceSubtema = lista.length;

    // Cerrar panel
    document.getElementById('panelCurriculumEdit').style.display = 'none';
    document.getElementById('btnAbrirCurriculumEdit').style.display = '';
});

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function resetearDesdoNucleoEdit() {
    resetearDesdeTemasEdit();
    const sel = document.getElementById('editSelectNucleo');
    sel.innerHTML = '<option value="">— Seleccioná un eje —</option>';
    sel.disabled = true;
}

function resetearDesdeTemasEdit() {
    temasEdit = [];
    document.getElementById('btnAplicarCurriculumEdit').disabled = true;
    const sel = document.getElementById('editSelectTema');
    sel.innerHTML = '<option value="">— Seleccioná un núcleo —</option>';
    sel.disabled = true;
}

const fetchJson = (url) => fetch(url, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
});

async function cargarEjesEdit(materiaId) {
    const sel = document.getElementById('editSelectEje');
    sel.innerHTML = '<option value="">Cargando...</option>';
    sel.disabled = true;
    document.getElementById('editSinDatos').style.display = 'none';
    try {
        const res  = await fetchJson(`${rutas.ejes}?materia_id=${materiaId}`);
        const data = await res.json();
        if (!Array.isArray(data) || !data.length) {
            sel.innerHTML = '<option value="">Sin datos</option>';
            document.getElementById('editSinDatos').style.display = '';
            return;
        }
        sel.innerHTML = '<option value="">— Seleccioná un eje —</option>';
        data.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.id; opt.textContent = e.nombre;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    } catch (err) {
        sel.innerHTML = '<option value="">Error</option>';
        console.error('ejesEdit:', err);
    }
}

async function cargarNucleosEdit(ejeId) {
    const sel = document.getElementById('editSelectNucleo');
    sel.innerHTML = '<option value="">Cargando...</option>';
    sel.disabled = true;
    try {
        const res  = await fetchJson(`${rutas.nucleos}?eje_id=${ejeId}`);
        const data = await res.json();
        sel.innerHTML = '<option value="">— Seleccioná un núcleo —</option>';
        data.forEach(n => {
            const opt = document.createElement('option');
            opt.value = n.id; opt.textContent = n.nombre;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    } catch (err) {
        sel.innerHTML = '<option value="">Error</option>';
        console.error('nucleosEdit:', err);
    }
}

async function cargarTemasEdit(nucleoId) {
    const sel = document.getElementById('editSelectTema');
    sel.innerHTML = '<option value="">Cargando...</option>';
    sel.disabled = true;
    try {
        const res  = await fetchJson(`${rutas.temas}?nucleo_id=${nucleoId}`);
        temasEdit = await res.json();
        sel.innerHTML = '<option value="">— Seleccioná un tema —</option>';
        temasEdit.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id; opt.textContent = t.nombre;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    } catch (err) {
        sel.innerHTML = '<option value="">Error</option>';
        console.error('temasEdit:', err);
    }
}
</script>
@endpush
