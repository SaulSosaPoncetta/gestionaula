@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-bookmark-fill me-2"></i>Libro de temas</h4>
        <p class="text-muted">Registro cronológico de clases dictadas.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('librotemas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Registrar clase
        </a>
    </div>
</div>

{{-- Clase activa ahora --}}
@if($materiaActiva)
<div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-bold text-success fs-5">
                <i class="bi bi-lightning-fill me-1"></i>Clase activa ahora
            </div>
            <div class="text-muted">
                <strong>{{ $materiaActiva->nombre }}</strong>
                @if($cursoActivo) &mdash; {{ $cursoActivo->nombre_completo }} @endif
            </div>
        </div>
        <a href="{{ route('librotemas.create', ['materia_id' => $materiaActiva->id, 'curso_id' => $cursoActivo?->id]) }}"
           class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i>Registrar esta clase
        </a>
    </div>
</div>
@endif

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('librotemas.index') }}" class="row g-3" id="form-filtros">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Materia</label>
                <select name="materia_id" id="filtro-materia" class="form-select">
                    <option value="">Todas las materias</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}"
                            {{ $materiaIdFiltro == $m->id ? 'selected' : '' }}>
                            {{ $m->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Curso</label>
                <select name="curso_id" id="filtro-curso" class="form-select">
                    <option value="">Todos los cursos</option>
                    @foreach($cursosParaMateria as $c)
                        <option value="{{ $c->id }}"
                            {{ $cursoIdFiltro == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre_completo }}
                            @if($materiaActiva && $cursoActivo?->id == $c->id) ⚡ @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Al cambiar materia en el filtro, actualizar cursos dinámicamente
document.getElementById('filtro-materia').addEventListener('change', function() {
    const materiaId = this.value;
    const cursosEl  = document.getElementById('filtro-curso');
    cursosEl.innerHTML = '<option value="">Cargando...</option>';

    if (!materiaId) {
        cursosEl.innerHTML = '<option value="">Todos los cursos</option>';
        return;
    }

    fetch(`/api/materias/${materiaId}/cursos`)
        .then(r => r.json())
        .then(cursos => {
            cursosEl.innerHTML = '<option value="">Todos los cursos</option>';
            cursos.forEach(c => {
                const activo = c.activo ? ' ⚡' : '';
                cursosEl.innerHTML += `<option value="${c.id}"${c.activo?' selected':''}>${c.nombre}${activo}</option>`;
            });
            // Si hay exactamente 1 curso, seleccionarlo
            if (cursos.length === 1) cursosEl.value = cursos[0].id;
        })
        .catch(() => {
            cursosEl.innerHTML = '<option value="">Error al cargar cursos</option>';
        });
});
</script>
@endpush

@if($registros->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay registros en el libro de temas.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th class="text-center">Clase N°</th>
                    <th class="text-center">Unidad</th>
                    <th>Materia</th>
                    <th>Curso</th>
                    <th>Tipo</th>
                    <th>Contenido</th>
                    <th>Actividad</th>
                    <th>Observación</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $reg)
                <tr>
                    <td class="ps-4">{{ $reg->fecha->format('d/m/Y') }}</td>
                    <td class="text-center fw-bold">{{ $reg->numeroclase }}</td>
                    <td class="text-center">{{ $reg->numerounidad ?? '—' }}</td>
                    <td>{{ $reg->materia?->nombre ?? '—' }}</td>
                    <td>{{ $reg->curso?->nombre_completo ?? '—' }}</td>
                    <td>
                        <span class="badge bg-info">{{ $reg->tipoclase?->denominacion ?? '—' }}</span>
                    </td>
                    <td class="small">{{ $reg->contenido?->tema ?? '—' }}</td>
                    <td class="small">{{ $reg->actividad?->titulo ?? '—' }}</td>
                    <td class="text-muted small">{{ Str::limit($reg->observacion, 40) ?? '—' }}</td>
                    <td class="text-end pe-3">
                        <form method="POST" action="{{ route('librotemas.destroy', $reg) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este registro?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $registros->links() }}</div>
@endif
@endsection