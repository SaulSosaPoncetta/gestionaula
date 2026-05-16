@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-check2-circle me-2"></i>Actividades calificadas</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaractividad.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('calificaractividad.calificadas') }}" class="row g-3">
            <div class="col-md-4">
                <select name="materia_id" class="form-select">
                    <option value="">— Selecciona materia —</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="curso_id" class="form-select">
                    <option value="">— Selecciona curso —</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id }}" {{ request('curso_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

@if(!request('materia_id') || !request('curso_id'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Seleccioná una materia y un curso para ver las calificadas.
    </div>
@elseif($registros->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay actividades calificadas para este filtro.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th>Actividad</th>
                    <th>Tema</th>
                    <th class="text-center">Estado</th>
                    <th>Fecha</th>
                    <th class="text-center">Nota</th>
                    <th>Observación</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $reg)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $reg->alumno->nombre_completo }}</td>
                    <td>{{ $reg->actividad?->titulo ?? '—' }}</td>
                    <td class="text-muted small">{{ $reg->actividad?->tema ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $reg->estadobadge }}">{{ $reg->estadolabel }}</span>
                    </td>
                    <td>{{ $reg->fechaestado?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-center">
                        @if($reg->nota !== null)
                            <span class="badge bg-{{ $reg->nota >= 7 ? 'success' : ($reg->nota >= 4 ? 'warning' : 'danger') }}">
                                {{ number_format($reg->nota, 2) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $reg->observacion ?? '—' }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('calificaractividad.show', $reg) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('calificaractividad.edit', $reg) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
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