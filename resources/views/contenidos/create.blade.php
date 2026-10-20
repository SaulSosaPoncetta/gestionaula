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
                    <select name="materia_id" id="selectMateria"
                            class="form-select @error('materia_id') is-invalid @enderror" required>
                        <option value="">— Seleccioná una materia —</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ (old('materia_id', request('materia_id')) == $materia->id) ? 'selected' : '' }}>
                                {{ $materia->nombre }}{{ $materia->anio ? ' '.$materia->anio : '' }}
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

    {{-- ───────────── PANEL DISEÑO CURRICULAR ───────────── --}}
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary" id="panelCurriculum" style="display:none">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold text-primary">
                <i class="bi bi-mortarboard me-1"></i>Cargar desde diseño curricular DGCyE
            </span>
            <button type="button" class="btn-close" id="btnCerrarCurriculum" aria-label="Cerrar"></button>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Eje curricular</label>
                    <select id="selectEje" class="form-select form-select-sm" disabled>
                        <option value="">— Seleccioná una materia primero —</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Núcleo</label>
                    <select id="selectNucleo" class="form-select form-select-sm" disabled>
                        <option value="">— Seleccioná un eje —</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Tema curricular</label>
                    <select id="selectTemaCurr" class="form-select form-select-sm" disabled>
                        <option value="">— Seleccioná un núcleo —</option>
                    </select>
                </div>
            </div>

            {{-- Preview del tema seleccionado --}}
            <div id="previewCurriculum" class="mt-3" style="display:none">
                <div class="alert alert-primary py-2 mb-2">
                    <div class="fw-semibold small mb-1"><i class="bi bi-file-text me-1"></i><span id="previewNombreTema"></span></div>
                    <div id="previewSubtemasWrap" style="display:none">
                        <div class="small text-muted mb-1">Subtemas:</div>
                        <ul id="previewSubtemasList" class="mb-0 ps-3 small"></ul>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" id="btnInsertarTema">
                    <i class="bi bi-plus-circle me-1"></i>Insertar como nuevo tema
                </button>
            </div>

            <div id="curriculumSinDatos" class="mt-2 text-muted small" style="display:none">
                <i class="bi bi-info-circle me-1"></i>No se encontró diseño curricular para esta materia.
            </div>
        </div>
    </div>
    {{-- ──────────────────────────────────────────────────── --}}

    {{-- Botón para abrir el panel curricular --}}
    <div class="mb-3">
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAbrirCurriculum">
            <i class="bi bi-mortarboard me-1"></i>Cargar desde diseño curricular
        </button>
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

// ── Etiquetas y helpers ───────────────────────────────────────
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
               class="form-control" placeholder="Subtema..." value="${escHtml(valor)}">
        <button type="button" class="btn btn-outline-danger btn-quitar-subtema">
            <i class="bi bi-x"></i>
        </button>`;
    return div;
}

function crearTarjetaTema(idx, nombreTema = '', subtemas = []) {
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
                       placeholder="Descripción del tema" value="${escHtml(nombreTema)}" required>
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
    const listaSubtemas = subtemas.length ? subtemas : [''];
    listaSubtemas.forEach(s => cont.appendChild(crearFilaSubtema(idx, s)));
    actualizarNumerosSubtemas(cont);
    return div;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// ── Eventos del formulario de temas ──────────────────────────
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

// ── Panel diseño curricular ───────────────────────────────────
const rutas = {
    ejes:    '{{ route("curriculum.ejes") }}',
    nucleos: '{{ route("curriculum.nucleos") }}',
    temas:   '{{ route("curriculum.temas") }}',
};

let temasDisponibles = []; // cache del último fetch de temas

document.getElementById('btnAbrirCurriculum').addEventListener('click', function() {
    const panel = document.getElementById('panelCurriculum');
    panel.style.display = '';
    this.style.display = 'none';
    // Disparar carga de ejes si ya hay materia seleccionada
    const materiaId = document.getElementById('selectMateria').value;
    if (materiaId) cargarEjes(materiaId);
});

document.getElementById('btnCerrarCurriculum').addEventListener('click', function() {
    document.getElementById('panelCurriculum').style.display = 'none';
    document.getElementById('btnAbrirCurriculum').style.display = '';
});

// Cuando cambia la materia en el encabezado, recargar ejes si el panel está abierto
document.getElementById('selectMateria').addEventListener('change', function() {
    const panel = document.getElementById('panelCurriculum');
    if (panel.style.display === 'none') return;
    resetearSelectCurriculum();
    if (this.value) cargarEjes(this.value);
});

document.getElementById('selectEje').addEventListener('change', function() {
    resetearDesdeNucleo();
    if (this.value) cargarNucleos(this.value);
});

document.getElementById('selectNucleo').addEventListener('change', function() {
    resetearDesdeTema();
    if (this.value) cargarTemas(this.value);
});

document.getElementById('selectTemaCurr').addEventListener('change', function() {
    document.getElementById('previewCurriculum').style.display = 'none';
    if (!this.value) return;
    const tema = temasDisponibles.find(t => t.id == this.value);
    if (!tema) return;
    document.getElementById('previewNombreTema').textContent = tema.nombre;
    const listEl = document.getElementById('previewSubtemasList');
    listEl.innerHTML = '';
    if (tema.subtemas && tema.subtemas.length) {
        tema.subtemas.forEach(s => {
            const li = document.createElement('li');
            li.textContent = s;
            listEl.appendChild(li);
        });
        document.getElementById('previewSubtemasWrap').style.display = '';
    } else {
        document.getElementById('previewSubtemasWrap').style.display = 'none';
    }
    document.getElementById('previewCurriculum').style.display = '';
});

document.getElementById('btnInsertarTema').addEventListener('click', function() {
    const tema = temasDisponibles.find(t => t.id == document.getElementById('selectTemaCurr').value);
    if (!tema) return;
    const contenedor = document.getElementById('contenedorTemas');
    contenedor.appendChild(crearTarjetaTema(indiceTema, tema.nombre, tema.subtemas || []));
    indiceTema++;
    actualizarEtiquetasTemas();
    // Reset preview para permitir insertar otro
    document.getElementById('previewCurriculum').style.display = 'none';
    document.getElementById('selectTemaCurr').value = '';
});

// ── Helpers AJAX currículum ───────────────────────────────────
function resetearSelectCurriculum() {
    resetearDesdeNucleo();
    const sel = document.getElementById('selectEje');
    sel.innerHTML = '<option value="">— Sin ejes —</option>';
    sel.disabled = true;
    document.getElementById('curriculumSinDatos').style.display = 'none';
}

function resetearDesdeNucleo() {
    resetearDesdeTema();
    const sel = document.getElementById('selectNucleo');
    sel.innerHTML = '<option value="">— Seleccioná un eje —</option>';
    sel.disabled = true;
}

function resetearDesdeTema() {
    document.getElementById('previewCurriculum').style.display = 'none';
    const sel = document.getElementById('selectTemaCurr');
    sel.innerHTML = '<option value="">— Seleccioná un núcleo —</option>';
    sel.disabled = true;
    temasDisponibles = [];
}

const fetchJson = (url) => fetch(url, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
});

async function cargarEjes(materiaId) {
    const sel = document.getElementById('selectEje');
    sel.innerHTML = '<option value="">Cargando...</option>';
    sel.disabled = true;
    document.getElementById('curriculumSinDatos').style.display = 'none';
    try {
        const res  = await fetchJson(`${rutas.ejes}?materia_id=${materiaId}`);
        const data = await res.json();
        if (!Array.isArray(data) || !data.length) {
            sel.innerHTML = '<option value="">Sin datos curriculares</option>';
            document.getElementById('curriculumSinDatos').style.display = '';
            return;
        }
        sel.innerHTML = '<option value="">— Seleccioná un eje —</option>';
        data.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.id;
            opt.textContent = e.nombre;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    } catch (err) {
        sel.innerHTML = '<option value="">Error al cargar</option>';
        console.error('ejes:', err);
    }
}

async function cargarNucleos(ejeId) {
    const sel = document.getElementById('selectNucleo');
    sel.innerHTML = '<option value="">Cargando...</option>';
    sel.disabled = true;
    try {
        const res  = await fetchJson(`${rutas.nucleos}?eje_id=${ejeId}`);
        const data = await res.json();
        sel.innerHTML = '<option value="">— Seleccioná un núcleo —</option>';
        data.forEach(n => {
            const opt = document.createElement('option');
            opt.value = n.id;
            opt.textContent = n.nombre;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    } catch (err) {
        sel.innerHTML = '<option value="">Error al cargar</option>';
        console.error('nucleos:', err);
    }
}

async function cargarTemas(nucleoId) {
    const sel = document.getElementById('selectTemaCurr');
    sel.innerHTML = '<option value="">Cargando...</option>';
    sel.disabled = true;
    try {
        const res  = await fetchJson(`${rutas.temas}?nucleo_id=${nucleoId}`);
        temasDisponibles = await res.json();
        sel.innerHTML = '<option value="">— Seleccioná un tema —</option>';
        temasDisponibles.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.nombre;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    } catch (err) {
        sel.innerHTML = '<option value="">Error al cargar</option>';
        console.error('temas:', err);
    }
}
</script>
@endpush
