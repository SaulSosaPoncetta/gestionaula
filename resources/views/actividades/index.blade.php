@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-check me-2"></i>Actividades</h4>
        <p class="text-muted">Gestión de actividades pedagógicas.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('actividades.seleccionar', $materiaActiva ? ['materia_id' => $materiaActiva] : []) }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva actividad
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('actividades.index') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Filtrar por materia</label>
                @php $haySeparador = false; @endphp
                <select name="materia_id" id="materia_id" class="form-select">
                    <option value="">— Todas las materias —</option>
                    @foreach($materias as $m)
                        @if($m->id === $materiaActivaId)
                            <option value="{{ $m->id }}" {{ $materiaActiva == $m->id ? 'selected' : '' }}>
                                ⚡ {{ $m->nombre }} (activa ahora)
                            </option>
                        @elseif($materiasEnHorario->contains($m->id))
                            <option value="{{ $m->id }}" {{ $materiaActiva == $m->id ? 'selected' : '' }}>
                                📅 {{ $m->nombre }}
                            </option>
                        @else
                            @if(!$haySeparador) @php $haySeparador = true @endphp <option disabled>──────────────</option> @endif
                            <option value="{{ $m->id }}" {{ $materiaActiva == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @if($materiaActivaId)
                <div class="form-text">
                    <i class="bi bi-lightning-charge-fill text-success me-1"></i>Activa ahora &nbsp;
                    <span>📅 En tu horario</span>
                </div>
                @endif
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
            @if(request('materia_id'))
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@if($actividades->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay actividades registradas.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 text-center">Unidad</th>
                    <th class="text-center">N° Act.</th>
                    <th>Materia</th>
                    <th>Tema</th>
                    <th>Subtema</th>
                    <th>Tipo</th>
                    <th class="text-center">Items</th>
                    <th class="text-center">Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividades as $act)
                <tr>
                    <td class="ps-4 text-center">
                        @if($act->numerounidad)
                            <span class="badge bg-secondary">{{ $act->numerounidad }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center fw-bold">
                        {{ $act->numeroactividad ?? '—' }}
                    </td>
                    <td>{{ $act->materia?->nombre ?? '—' }}</td>
                    <td class="fw-semibold">{{ $act->tema }}</td>
                    <td class="text-muted small">{{ $act->subtema ?? '—' }}</td>
                    <td>{{ $act->tipoactividad?->denominacion ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $act->items->count() }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $act->estado === 'activa' ? 'success' : 'secondary' }}">
                            {{ ucfirst($act->estado) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('actividades.show', $act) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('actividades.destroy', $act) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta actividad?')">
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
<div class="mt-3">{{ $actividades->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('materia_id').addEventListener('change', function () {
    this.closest('form').submit();
});
</script>
@endpush