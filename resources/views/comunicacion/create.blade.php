@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo mensaje</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('comunicacion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('comunicacion.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de mensaje</label>
                    <select name="tipo" id="tipomensaje" class="form-select" required>
                        <option value="general" {{ old('tipo') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="curso" {{ old('tipo') == 'curso' ? 'selected' : '' }}>Por curso</option>
                        <option value="alumno" {{ old('tipo') == 'alumno' ? 'selected' : '' }}>Por alumno</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Destinatario <span class="text-danger">*</span></label>
                    <input type="text" name="destinatario"
                           class="form-control @error('destinatario') is-invalid @enderror"
                           value="{{ old('destinatario') }}"
                           placeholder="Ej: Familias de 3ro A, Padre de García Juan..." required>
                    @error('destinatario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 campocurso" style="{{ old('tipo', 'general') == 'general' ? 'display:none' : '' }}">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" id="selectcurso" class="form-select">
                        <option value="">Seleccioná...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" {{ old('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 campoalumno" style="{{ old('tipo') == 'alumno' ? '' : 'display:none' }}">
                    <label class="form-label fw-semibold">Alumno</label>
                    <select name="alumno_id" id="selectalumno" class="form-select">
                        <option value="">Seleccioná primero un curso...</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
                    <input type="text" name="asunto"
                           class="form-control @error('asunto') is-invalid @enderror"
                           value="{{ old('asunto') }}" required>
                    @error('asunto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
                    <textarea name="cuerpo" class="form-control @error('cuerpo') is-invalid @enderror"
                              rows="6" required>{{ old('cuerpo') }}</textarea>
                    @error('cuerpo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Enviar mensaje
                </button>
                <a href="{{ route('comunicacion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const alumnosPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->alumnos]));

const tipo = document.getElementById('tipomensaje');
const campoCurso = document.querySelectorAll('.campocurso');
const campoAlumno = document.querySelectorAll('.campoalumno');
const selectCurso = document.getElementById('selectcurso');
const selectAlumno = document.getElementById('selectalumno');

tipo.addEventListener('change', function() {
    campoCurso.forEach(el => el.style.display = this.value !== 'general' ? '' : 'none');
    campoAlumno.forEach(el => el.style.display = this.value === 'alumno' ? '' : 'none');
});

selectCurso.addEventListener('change', function() {
    const alumnos = alumnosPorCurso[this.value] || [];
    selectAlumno.innerHTML = '<option value="">Seleccioná un alumno...</option>';
    alumnos.forEach(a => {
        selectAlumno.innerHTML += `<option value="${a.id}">${a.apellido}, ${a.nombre}</option>`;
    });
});
</script>
@endpush
@endsection