@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-plus me-2"></i>Nueva actividad</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('actividades.seleccionar') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('actividades.store') }}" id="formActividad">
    @csrf
    <input type="hidden" name="materia_id" value="{{ $materia->id }}">

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Datos generales --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-info-circle me-1"></i>Datos de la actividad
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control"
                           value="{{ old('titulo') }}" required
                           placeholder="Ej: Trabajo práctico N°1">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Tipo de actividad <span class="text-muted fw-normal">(opcional)</span>
                    </label>
                    <select name="tipoactividad_id" class="form-select">
                        <option value="">— Sin especificar —</option>
                        @foreach($tiposactividad as $tipo)
                            <option value="{{ $tipo->id }}"
                                {{ old('tipoactividad_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Tema <span class="text-muted fw-normal">(opcional)</span>
                    </label>
                    <input type="text" name="tema" class="form-control"
                           value="{{ old('tema') }}"
                           placeholder="Tema de la actividad">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Subtema <span class="text-muted fw-normal">(opcional)</span>
                    </label>
                    <input type="text" name="subtema" class="form-control"
                           value="{{ old('subtema') }}"
                           placeholder="Subtema de la actividad">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fechainicio" class="form-control"
                           value="{{ old('fechainicio', date('Y-m-d')) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha entrega <span class="text-danger">*</span></label>
                    <input type="date" name="fechaentrega" class="form-control"
                           value="{{ old('fechaentrega') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Curso <span class="text-muted fw-normal">(opcional)</span>
                    </label>
                    <select name="curso_id" id="curso_id" class="form-select">
                        <option value="">— Sin curso asignado —</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ old('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"
                              placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Modalidad --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-people me-1"></i>Modalidad
        </div>
        <div class="card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="esgrupal"
                       id="esgrupal" value="1" {{ old('esgrupal') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="esgrupal">
                    Trabajo en grupo
                </label>
            </div>

            <div id="seccionGrupal" style="{{ old('esgrupal') ? '' : 'display:none' }}">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Integrantes por grupo <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="integrantesporgrupo" id="integrantesporgrupo"
                               class="form-control" min="2"
                               value="{{ old('integrantesporgrupo', 2) }}">
                        <div class="form-text" id="infoGrupos"></div>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Modo de asignación</label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modogrupo"
                                       id="modoAleatorio" value="aleatorio"
                                       {{ old('modogrupo', 'aleatorio') === 'aleatorio' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modoAleatorio">
                                    <i class="bi bi-shuffle me-1"></i>Aleatorio
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modogrupo"
                                       id="modoManual" value="manual"
                                       {{ old('modogrupo') === 'manual' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modoManual">
                                    <i class="bi bi-hand-index me-1"></i>Manual
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="seccionManual" style="display:none">
                    <div class="row g-3" id="contenedorGrupos"></div>
                    <div class="alert alert-info mt-3" id="alertaSinGrupo" style="display:none">
                        <i class="bi bi-info-circle me-2"></i>
                        Alumnos sin grupo: <span id="contadorSinGrupo" class="fw-bold">0</span>
                    </div>
                </div>

                <div id="seccionAleatorio">
                    <div class="alert alert-success">
                        <i class="bi bi-shuffle me-2"></i>
                        Los grupos se formarán automáticamente de manera aleatoria al guardar.
                        <span id="infoGruposAleatorio"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Guardar actividad
        </button>
        <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Alumnos del curso seleccionado
const alumnosPorCurso = @json($cursos->mapWithKeys(fn($c) => [
    $c->id => $c->alumnos->map(fn($a) => ['id' => $a->id, 'nombre' => $a->nombre_completo])->values()
]));

let totalAlumnos = 0;
let alumnos      = [];
let asignaciones = {};

document.getElementById('curso_id').addEventListener('change', function () {
    const cursoId = this.value;
    alumnos       = cursoId ? (alumnosPorCurso[cursoId] ?? []) : [];
    totalAlumnos  = alumnos.length;
    asignaciones  = {};
    actualizarInfo();
    if (document.getElementById('modoManual').checked) construirGrupos();
});

document.getElementById('esgrupal').addEventListener('change', function () {
    document.getElementById('seccionGrupal').style.display = this.checked ? '' : 'none';
    if (this.checked) actualizarInfo();
});

document.querySelectorAll('[name=modogrupo]').forEach(r => {
    r.addEventListener('change', function () {
        document.getElementById('seccionManual').style.display   = this.value === 'manual'    ? '' : 'none';
        document.getElementById('seccionAleatorio').style.display = this.value === 'aleatorio' ? '' : 'none';
        if (this.value === 'manual') construirGrupos();
    });
});

document.getElementById('integrantesporgrupo').addEventListener('input', function () {
    actualizarInfo();
    if (document.getElementById('modoManual').checked) construirGrupos();
});

function actualizarInfo() {
    const n      = parseInt(document.getElementById('integrantesporgrupo').value) || 0;
    const grupos = n > 0 && totalAlumnos > 0 ? Math.ceil(totalAlumnos / n) : 0;
    const info   = n > 0 && totalAlumnos > 0
        ? `Se formarán aprox. ${grupos} grupos de ${n} integrantes (${totalAlumnos} alumnos)`
        : totalAlumnos === 0 ? 'Seleccioná un curso para ver los alumnos' : '';
    document.getElementById('infoGrupos').textContent          = info;
    document.getElementById('infoGruposAleatorio').textContent = info ? ` — ${info}` : '';
}

function construirGrupos() {
    const n          = parseInt(document.getElementById('integrantesporgrupo').value) || 2;
    const cantGrupos = totalAlumnos > 0 ? Math.ceil(totalAlumnos / n) : 3;
    asignaciones     = {};

    const contenedor = document.getElementById('contenedorGrupos');
    contenedor.innerHTML = '';

    for (let g = 0; g < cantGrupos; g++) {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-3';
        col.innerHTML = `
            <div class="card border shadow-sm" id="tarjetaGrupo${g}">
                <div class="card-header bg-primary text-white fw-semibold d-flex justify-content-between">
                    <span>Grupo ${g + 1}</span>
                    <span class="badge bg-white text-primary" id="contGrupo${g}">0/${n}</span>
                </div>
                <div class="card-body p-2" id="listaGrupo${g}" style="min-height:80px">
                    <div class="text-muted small text-center mt-2">Sin integrantes</div>
                </div>
            </div>`;
        contenedor.appendChild(col);
    }

    renderizarDisponibles();
}

function renderizarDisponibles() {
    const n          = parseInt(document.getElementById('integrantesporgrupo').value) || 2;
    const cantGrupos = totalAlumnos > 0 ? Math.ceil(totalAlumnos / n) : 3;

    for (let g = 0; g < cantGrupos; g++) {
        const lista   = document.getElementById(`listaGrupo${g}`);
        const cont    = document.getElementById(`contGrupo${g}`);
        if (!lista) continue;

        const miembros = alumnos.filter(a => asignaciones[a.id] === g);
        cont.textContent = `${miembros.length}/${n}`;
        lista.innerHTML  = '';

        if (miembros.length === 0) {
            lista.innerHTML = '<div class="text-muted small text-center mt-2">Sin integrantes</div>';
        } else {
            miembros.forEach(a => {
                const div = document.createElement('div');
                div.className = 'd-flex justify-content-between align-items-center mb-1';
                div.innerHTML = `
                    <small class="fw-semibold">${a.nombre}</small>
                    <button type="button" class="btn btn-xs btn-outline-danger"
                            style="font-size:0.7rem;padding:1px 6px"
                            onclick="quitarAlumno(${a.id})">
                        <i class="bi bi-x"></i>
                    </button>
                    <input type="hidden" name="grupos[${g}][alumnos][]" value="${a.id}">`;
                lista.appendChild(div);
            });
        }

        const sinGrupo = alumnos.filter(a => asignaciones[a.id] === undefined);
        if (sinGrupo.length > 0 && miembros.length < n) {
            const sel = document.createElement('select');
            sel.className = 'form-select form-select-sm mt-2';
            sel.innerHTML = '<option value="">+ Agregar alumno...</option>';
            sinGrupo.forEach(a => {
                sel.innerHTML += `<option value="${a.id}">${a.nombre}</option>`;
            });
            sel.addEventListener('change', function () {
                if (this.value) {
                    asignaciones[parseInt(this.value)] = g;
                    renderizarDisponibles();
                }
            });
            lista.appendChild(sel);
        }
    }

    const sinGrupo = alumnos.filter(a => asignaciones[a.id] === undefined);
    const alerta   = document.getElementById('alertaSinGrupo');
    document.getElementById('contadorSinGrupo').textContent = sinGrupo.length;
    alerta.style.display = sinGrupo.length > 0 ? '' : 'none';
}

function quitarAlumno(alumnoId) {
    delete asignaciones[alumnoId];
    renderizarDisponibles();
}

actualizarInfo();
</script>
@endpush