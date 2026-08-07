@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Calificar actividades</h4>
        <p class="text-muted">Seleccioná la materia y el curso para continuar.</p>
        @if($materiaActiva)
        <span class="badge bg-success mt-1">
            <i class="bi bi-play-circle me-1"></i>Clase activa detectada — materia y curso preseleccionados
        </span>
        @endif
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('calificaractividad.ver') }}">
            <div class="row g-3">

                {{-- Materia --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">1</span>Materia
                    </label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">Seleccioná una materia...</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}"
                                {{ $materiaId == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}{{ $materiaActiva == $m->id ? ' ⚡' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Curso filtrado por materia, activo primero --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">2</span>Curso
                    </label>
                    <select name="curso_id" id="curso_id" class="form-select" required
                            {{ $cursos->isEmpty() ? 'disabled' : '' }}>
                        <option value="">
                            {{ $cursos->isEmpty() ? 'Seleccioná primero una materia' : 'Seleccioná un curso...' }}
                        </option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id }}"
                                {{ $cursoActivo == $c->id ? 'selected' : '' }}>
                                {{ $c->nombre_completo }}{{ $cursoActivo == $c->id ? ' ⚡' : '' }}
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
    const cursoEl   = document.getElementById('curso_id');

    if (!materiaId) {
        cursoEl.innerHTML = '<option value="">Seleccioná primero una materia</option>';
        cursoEl.disabled  = true;
        return;
    }

    cursoEl.innerHTML = '<option value="">Cargando cursos...</option>';
    cursoEl.disabled  = true;

    fetch(`/api/materias/${materiaId}/cursos`)
        .then(r => r.json())
        .then(cursos => {
            cursoEl.innerHTML = '<option value="">Seleccioná un curso...</option>';
            cursos.forEach(c => {
                const sel   = c.activo ? ' selected' : '';
                const label = c.activo ? `${c.nombre} ⚡` : c.nombre;
                cursoEl.innerHTML += `<option value="${c.id}"${sel}>${label}</option>`;
            });
            cursoEl.disabled = cursos.length === 0;
            if (cursos.length === 1) cursoEl.value = cursos[0].id;
        })
        .catch(() => {
            cursoEl.innerHTML = '<option value="">Error al cargar</option>';
            cursoEl.disabled  = false;
        });
});
</script>
@endpush
