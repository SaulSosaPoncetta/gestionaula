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
        <form method="POST" action="{{ route('calificaciones.cargar') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Materia <span class="text-muted fw-normal">(opcional)</span></label>
                    <select name="materia_id" id="materia_id" class="form-select">
                        <option value="">Sin materia</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Período</label>
                    <select name="periodo" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo }}">{{ $periodo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de evaluación</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
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

@push('scripts')
<script>
const materiasPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->materias]));

document.getElementById('curso_id').addEventListener('change', function () {
    const select = document.getElementById('materia_id');
    const materias = materiasPorCurso[this.value] || [];
    select.innerHTML = '<option value="">Sin materia</option>';
    materias.forEach(m => {
        select.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
    });
});
</script>
@endpush
@endsection