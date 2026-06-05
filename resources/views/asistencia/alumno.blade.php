@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Asistencia por alumno</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.accion', array_filter([
            'curso_id'   => request('curso_id'),
            'materia_id' => request('materia_id'),
        ])) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Buscador --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('asistencia.alumno') }}" class="row g-3">
            <input type="hidden" name="curso_id"   value="{{ request('curso_id') }}">
            <input type="hidden" name="materia_id" value="{{ request('materia_id') }}">

            <div class="col-md-5">
                <label class="form-label fw-semibold">Buscar alumno</label>
                <input type="text" name="buscar" class="form-control"
                       placeholder="Apellido, nombre o DNI..."
                       value="{{ request('buscar') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filtrar por materia</label>
                <select name="materia_id" class="form-select">
                    <option value="">— Todas las materias —</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}"
                            {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Lista de resultados si hay varios --}}
@if($alumnos->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-people me-1"></i>Resultados ({{ $alumnos->count() }})
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @foreach($alumnos as $a)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $a->nombre_completo }}</div>
                    <div class="text-muted small">
                        {{ $a->curso?->nombre_completo ?? '—' }}
                    </div>
                </div>
                <a href="{{ route('asistencia.alumno', array_filter([
                        'alumno_id'  => $a->id,
                        'materia_id' => request('materia_id'),
                        'curso_id'   => request('curso_id'),
                    ])) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i>Ver asistencias
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- Detalle del alumno --}}
@if($alumno)

{{-- Resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-success">{{ $resumen['presente'] }}</div>
                <div class="text-muted small">Presentes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-danger">{{ $resumen['ausente'] }}</div>
                <div class="text-muted small">Ausentes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-info">{{ $resumen['justificado'] }}</div>
                <div class="text-muted small">Justificados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-warning">{{ $resumen['tarde'] }}</div>
                <div class="text-muted small">Tardanzas</div>
            </div>
        </div>
    </div>
</div>

{{-- Historial del alumno --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between">
        <span>
            <i class="bi bi-person me-1 text-primary"></i>
            {{ $alumno->nombre_completo }}
            — {{ $alumno->curso?->nombre_completo ?? '—' }}
        </span>
        <span class="badge bg-secondary">{{ $resumen['total'] }} registros</span>
    </div>
    <div class="card-body p-0">
        @if($detalle->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                No hay registros de asistencia para este alumno.
            </div>
        @else
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Materia</th>
                    <th class="text-center">Estado</th>
                    <th>Hora llegada</th>
                    <th>Observación</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalle as $reg)
                <tr>
                    <td class="ps-4">{{ $reg->fecha->format('d/m/Y') }}</td>
                    <td>{{ $reg->materia?->nombre ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $reg->estadobadge }}">{{ $reg->estadolabel }}</span>
                    </td>
                    <td>
                        @if($reg->estado === 'tarde' && $reg->horallegada)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($reg->horallegada)->format('H:i') }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $reg->observacion ?? '—' }}</td>
                    <td class="text-center">
                        <a href="{{ route('asistencia.editar', $reg) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endif
@endsection