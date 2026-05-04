@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Asistencia</h4>
        <p class="text-muted">Selecciona la materia y el curso para continuar.</p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('asistencia.alumno') }}" class="btn btn-outline-primary">
            <i class="bi bi-person-lines-fill me-1"></i>Buscar por alumno
        </a>
        <a href="{{ route('asistencia.historial') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Ver historial
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('asistencia.accion') }}">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">1</span>Materia
                    </label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">Selecciona una materia...</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">2</span>Curso
                    </label>
                    <select name="curso_id" id="curso_id" class="form-select" required
                            {{ $cursos->isEmpty() ? 'disabled' : '' }}>
                        <option value="">
                            {{ $cursos->isEmpty() ? 'Selecciona primero una materia' : 'Selecciona un curso...' }}
                        </option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"
                        {{ $cursos->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-arrow-right-circle me-1"></i>Siguiente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('materia_id').addEventListener('change', function () {
    const materiaId = this.value;
    if (materiaId) {
        window.location.href = `/asistencia?materia_id=${materiaId}`;
    } else {
        window.location.href = `/asistencia`;
    }
});
</script>
@endpush