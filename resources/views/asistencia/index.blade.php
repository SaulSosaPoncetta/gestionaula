@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Asistencia</h4>
        <p class="text-muted mb-0">Seleccioná la materia y el curso para continuar.</p>
        @if($horarioActivo)
        <div class="mt-1">
            <span class="badge bg-success">
                <i class="bi bi-play-circle me-1"></i>Clase activa detectada:
                {{ $horarioActivo->materia?->nombre }} — {{ $horarioActivo->curso?->nombre_completo }}
            </span>
        </div>
        @endif
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
        <form method="GET" action="{{ route('asistencia.accion') }}" id="formAsistencia">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">1</span>Materia
                    </label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">Seleccioná una materia...</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ $materiaIdDefault == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">2</span>Curso
                    </label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccioná un curso...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ $cursoIdDefault == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle me-1"></i>Siguiente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const todosLosCursos = @json($cursos->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre_completo]));
const cursoDefault   = {{ $cursoIdDefault ?? 'null' }};

// Al cambiar materia: recargar cursos via AJAX o por URL
document.getElementById('materia_id').addEventListener('change', function () {
    const materiaId = this.value;
    const cursoSel  = document.getElementById('curso_id');
    const urlActual = new URL(window.location.href);

    if (materiaId) {
        urlActual.searchParams.set('materia_id', materiaId);
    } else {
        urlActual.searchParams.delete('materia_id');
    }
    urlActual.searchParams.delete('curso_id');
    window.location.href = urlActual.toString();
});
</script>
@endpush
