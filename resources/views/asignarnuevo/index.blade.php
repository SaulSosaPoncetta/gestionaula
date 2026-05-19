@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-arrow me-2"></i>Asignar actividad</h4>
        <p class="text-muted">Selecciona el curso y la materia para ver las actividades disponibles.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('asignarnuevo.ver') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">1</span>Curso
                    </label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Selecciona un curso...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">2</span>Materia
                    </label>
                    <select name="materia_id" id="materia_id" class="form-select" required
                            {{ $materias->isEmpty() ? 'disabled' : '' }}>
                        <option value="">
                            {{ $materias->isEmpty() ? 'Selecciona primero un curso' : 'Selecciona una materia...' }}
                        </option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"
                        {{ $materias->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-arrow-right-circle me-1"></i>Ver actividades
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('curso_id').addEventListener('change', function () {
    const cursoId = this.value;
    if (cursoId) {
        window.location.href = `/asignarnuevo?curso_id=${cursoId}`;
    } else {
        window.location.href = `/asignarnuevo`;
    }
});
</script>
@endpush