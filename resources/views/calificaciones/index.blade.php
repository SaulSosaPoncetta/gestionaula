@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Calificaciones</h4>
        <p class="text-muted">Seleccioná el curso, materia, período y tipo de evaluación.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaciones.historial') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Ver historial
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('calificaciones.cargar') }}">

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">— Seleccioná un curso primero —</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Período <span class="text-danger">*</span></label>
                    <select name="periodo_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}">{{ $periodo->denominacion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de evaluación <span class="text-danger">*</span></label>
                    <select name="tipoevaluacion_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->denominacion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control"
                           value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle me-1"></i>Cargar notas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const materiasPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->materias->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])]));
const todasLasMaterias = @json(\App\Models\Materia::orderBy('nombre')->get()->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre]));

document.getElementById('curso_id').addEventListener('change', function () {
    const select  = document.getElementById('materia_id');
    const cursoId = this.value;
    const materias = (cursoId && materiasPorCurso[cursoId]?.length)
                   ? materiasPorCurso[cursoId]
                   : todasLasMaterias;

    select.innerHTML = '<option value="">Seleccioná una materia...</option>';
    materias.forEach(m => {
        select.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
    });
});
</script>
@endpush