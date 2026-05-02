@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Asistencia</h4>
        <p class="text-muted">Seleccioná el curso, materia y fecha para registrar la asistencia.</p>
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
        <form method="POST" action="{{ route('asistencia.registrar') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccioná un curso...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Materia <span class="text-muted fw-normal">(opcional)</span></label>
                    <select name="materia_id" id="materia_id" class="form-select">
                        <option value="">Todas / Sin materia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle me-1"></i>Cargar lista
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const materiasPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->materias]));

document.getElementById('curso_id').addEventListener('change', function () {
    const select = document.getElementById('materia_id');
    const materias = materiasPorCurso[this.value] || [];
    select.innerHTML = '<option value="">Todas / Sin materia</option>';
    materias.forEach(m => {
        select.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
    });
});
</script>
@endpush