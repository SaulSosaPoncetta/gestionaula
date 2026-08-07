@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-arrow me-2"></i>Asignar actividad</h4>
        <p class="text-muted">Seleccioná el curso y la materia para ver las actividades disponibles.</p>
        @if($cursoActivo)
        <span class="badge bg-success mt-1">
            <i class="bi bi-play-circle me-1"></i>Clase activa detectada — curso y materia preseleccionados
        </span>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('asignarnuevo.ver') }}">
            <div class="row g-3">

                {{-- Curso: activo primero, luego los del horario, luego el resto --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">1</span>Curso
                    </label>
                    @php $haySep = false; @endphp
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccioná un curso...</option>
                        @foreach($cursos as $c)
                            @if($c->id === $cursoActivo)
                                <option value="{{ $c->id }}" selected>
                                    ⚡ {{ $c->nombre_completo }} (activo ahora)
                                </option>
                            @elseif($cursosEnHorario->contains($c->id))
                                <option value="{{ $c->id }}" {{ $cursoId == $c->id ? 'selected' : '' }}>
                                    📅 {{ $c->nombre_completo }}
                                </option>
                            @else
                                @if(!$haySep) @php $haySep = true @endphp <option disabled>──────────────</option> @endif
                                <option value="{{ $c->id }}" {{ $cursoId == $c->id ? 'selected' : '' }}>
                                    {{ $c->nombre_completo }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @if($cursoActivo)
                    <div class="form-text">
                        <i class="bi bi-lightning-charge-fill text-success me-1"></i>Activo ahora &nbsp;
                        <span>📅 En tu horario</span>
                    </div>
                    @endif
                </div>

                {{-- Materia: activa primero, solo las asociadas al curso --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">2</span>Materia
                    </label>
                    <select name="materia_id" id="materia_id" class="form-select" required
                            {{ $materias->isEmpty() ? 'disabled' : '' }}>
                        <option value="">
                            {{ $materias->isEmpty() ? 'Seleccioná primero un curso' : 'Seleccioná una materia...' }}
                        </option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}"
                                {{ $materiaActiva == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}{{ $materiaActiva == $m->id ? ' ⚡' : '' }}
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
    const cursoId   = this.value;
    const materiaEl = document.getElementById('materia_id');

    if (!cursoId) {
        materiaEl.innerHTML = '<option value="">Seleccioná primero un curso</option>';
        materiaEl.disabled  = true;
        return;
    }

    // Obtener materias del curso vía API de horarios
    materiaEl.innerHTML = '<option value="">Cargando materias...</option>';
    materiaEl.disabled  = true;

    fetch(`/api/cursos/${cursoId}/materias`)
        .then(r => r.json())
        .then(materias => {
            materiaEl.innerHTML = '<option value="">Seleccioná una materia...</option>';
            materias.forEach(m => {
                const sel   = m.activa ? ' selected' : '';
                const label = m.activa ? `${m.nombre} ⚡` : m.nombre;
                materiaEl.innerHTML += `<option value="${m.id}"${sel}>${label}</option>`;
            });
            materiaEl.disabled = materias.length === 0;
        })
        .catch(() => {
            materiaEl.innerHTML = '<option value="">Error al cargar</option>';
            materiaEl.disabled  = false;
        });
});
</script>
@endpush
