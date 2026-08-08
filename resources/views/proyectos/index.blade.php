@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-folder2-open me-2"></i>Proyectos</h4>
        <p class="text-muted">Gestión de proyectos pedagógicos.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('proyectos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo proyecto
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('proyectos.index') }}" class="row g-3" id="form-filtros">
            {{-- Materia: activa primero, luego del horario, luego el resto --}}
            <div class="col-md-3">
                @php $haySep = false; @endphp
                <select name="materia_id" id="filtro-materia" class="form-select">
                    <option value="">— Todas las materias —</option>
                    @foreach($materias as $m)
                        @if($m->id === $materiaActivaId)
                            <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                                ⚡ {{ $m->nombre }} (activa ahora)
                            </option>
                        @elseif($materiasEnHorario->contains($m->id))
                            <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                                📅 {{ $m->nombre }}
                            </option>
                        @else
                            @if(!$haySep) @php $haySep = true @endphp <option disabled>──────────────</option> @endif
                            <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Curso: solo los asociados a la materia seleccionada --}}
            <div class="col-md-3">
                <select name="curso_id" id="filtro-curso" class="form-select">
                    <option value="">— Todos los cursos —</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id }}" {{ request('curso_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre_completo }}{{ $c->id === ($horario?->curso_id ?? null) ? ' ⚡' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">— Todos —</option>
                    @foreach(\App\Models\Proyecto::ESTADOS as $val => $label)
                        <option value="{{ $val }}" {{ request('estado') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
            @if(request()->hasAny(['materia_id','curso_id','estado']))
            <div class="col-md-2">
                <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@if($proyectos->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay proyectos registrados.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Título</th>
                    <th>Materia</th>
                    <th>Curso</th>
                    <th>Establecimiento</th>
                    <th class="text-center">Alumnos</th>
                    <th>Fecha</th>
                    <th>Presentación</th>
                    <th class="text-center">Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos as $p)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $p->titulo }}</td>
                    <td>{{ $p->materia?->nombre ?? '—' }}</td>
                    <td>{{ $p->curso?->nombre_completo ?? '—' }}</td>
                    <td>{{ $p->establecimiento?->nombre ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $p->alumnos->count() }}</span>
                    </td>
                    <td>{{ $p->fecha?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $p->fechapresentacion?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $p->estadobadge }}">{{ $p->estadolabel }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('proyectos.show', $p) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('proyectos.edit', $p) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('proyectos.destroy', $p) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este proyecto?')">
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
<div class="mt-3">{{ $proyectos->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('filtro-materia')?.addEventListener('change', function () {
    const materiaId = this.value;
    const cursoEl   = document.getElementById('filtro-curso');

    if (!materiaId) {
        cursoEl.innerHTML = '<option value="">— Todos los cursos —</option>';
        return;
    }

    cursoEl.innerHTML = '<option value="">Cargando...</option>';

    fetch(`/api/materias/${materiaId}/cursos`)
        .then(r => r.json())
        .then(cursos => {
            cursoEl.innerHTML = '<option value="">— Todos los cursos —</option>';
            cursos.forEach(c => {
                const activo = c.activo ? ' ⚡' : '';
                cursoEl.innerHTML += `<option value="${c.id}"${c.activo ? ' selected' : ''}>${c.nombre}${activo}</option>`;
            });
        })
        .catch(() => {
            cursoEl.innerHTML = '<option value="">— Todos los cursos —</option>';
        });
});
</script>
@endpush