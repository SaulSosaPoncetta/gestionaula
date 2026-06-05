@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo proyecto</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('proyectos.store') }}">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Datos del proyecto --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-folder2 me-1"></i>Datos del proyecto
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control"
                           value="{{ old('titulo') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}" {{ old('materia_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id }}" {{ old('curso_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Establecimiento presentación</label>
                    <select name="establecimiento_id" class="form-select">
                        <option value="">— Sin establecimiento —</option>
                        @foreach($establecimientos as $e)
                            <option value="{{ $e->id }}" {{ old('establecimiento_id') == $e->id ? 'selected' : '' }}>
                                {{ $e->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="fecha" class="form-control"
                           value="{{ old('fecha') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hora</label>
                    <input type="time" name="hora" class="form-control"
                           value="{{ old('hora') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de presentación</label>
                    <input type="date" name="fechapresentacion" class="form-control"
                           value="{{ old('fechapresentacion') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Actividad automática --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-clipboard2-check me-1"></i>Actividad pedagógica
            <span class="badge bg-info ms-2">Se generará automáticamente</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Tipo de actividad <span class="text-danger">*</span>
                    </label>
                    <select name="tipoactividad_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($tiposactividad as $t)
                            <option value="{{ $t->id }}" {{ old('tipoactividad_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        N° de unidad <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="numerounidad" class="form-control"
                           value="{{ old('numerounidad') }}" min="1" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Alumnos --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-people me-1"></i>Alumnos del proyecto
            <span class="badge bg-warning text-dark ms-2">Seleccioná al menos uno</span>
        </div>
        <div class="card-body">
            <div class="alert alert-info small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Al seleccionar alumnos se creará automáticamente una carpeta de campo para cada uno.
            </div>
            <div id="contenedorAlumnos">
                <p class="text-muted">Seleccioná primero un curso para ver los alumnos.</p>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Crear proyecto
        </button>
        <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('curso_id').addEventListener('change', function () {
    const cursoId = this.value;
    const contenedor = document.getElementById('contenedorAlumnos');

    if (!cursoId) {
        contenedor.innerHTML = '<p class="text-muted">Seleccioná primero un curso para ver los alumnos.</p>';
        return;
    }

    fetch(`/api/cursos/${cursoId}/alumnos`)
        .then(r => r.json())
        .then(alumnos => {
            if (alumnos.length === 0) {
                contenedor.innerHTML = '<p class="text-muted">Este curso no tiene alumnos registrados.</p>';
                return;
            }

            let html = `
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="seleccionarTodos()">
                        <i class="bi bi-check-all me-1"></i>Seleccionar todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodos()">
                        <i class="bi bi-x me-1"></i>Deseleccionar todos
                    </button>
                </div>
                <div class="row g-2">`;

            alumnos.forEach(a => {
                html += `
                    <div class="col-md-4">
                        <div class="form-check border rounded p-2">
                            <input class="form-check-input alumno-check" type="checkbox"
                                   name="alumnos[]" value="${a.id}" id="alumno${a.id}">
                            <label class="form-check-label w-100" for="alumno${a.id}">
                                <span class="fw-semibold">${a.nombre_completo}</span>
                                <span class="badge bg-${a.tipocursadabadge} ms-1">${a.tipocursadalabel}</span>
                            </label>
                        </div>
                    </div>`;
            });

            html += '</div>';
            contenedor.innerHTML = html;
        });
});

function seleccionarTodos() {
    document.querySelectorAll('.alumno-check').forEach(c => c.checked = true);
}
function deseleccionarTodos() {
    document.querySelectorAll('.alumno-check').forEach(c => c.checked = false);
}
</script>
@endpush