@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Asistencias por alumno</h4>
        <p class="text-muted">Buscá un alumno para ver su historial de asistencias e inasistencias.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Buscador --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('asistencia.alumno') }}" class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Buscar alumno</label>
                <input type="text" name="buscar" class="form-control"
                       value="{{ request('buscar') }}"
                       placeholder="Apellido, nombre o DNI...">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Resultados de búsqueda --}}
@if($alumnos->isNotEmpty() && !$alumno)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-list-ul me-1"></i>Resultados ({{ $alumnos->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th>DNI</th>
                    <th>Año</th>
                    <th>División</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $a)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $a->nombre_completo }}</td>
                    <td>{{ $a->dni ?? '—' }}</td>
                    <td>{{ $a->curso?->anio ?? '—' }}</td>
                    <td>{{ $a->curso?->division ?? '—' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('asistencia.alumno', ['buscar' => request('buscar'), 'alumno_id' => $a->id]) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Ver historial
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif(request('buscar') && $alumnos->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No se encontraron alumnos con ese criterio.
</div>
@endif

{{-- Detalle del alumno seleccionado --}}
@if($alumno)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">{{ $alumno->nombre_completo }}</h5>
            <small class="text-muted">
                DNI: {{ $alumno->dni ?? '—' }} &mdash;
                {{ $alumno->curso?->anio ?? '' }} {{ $alumno->curso?->division ?? '' }}
                {{ $alumno->curso?->turno ?? '' }}
            </small>
        </div>
        <a href="{{ route('asistencia.alumno', ['buscar' => request('buscar')]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver a resultados
        </a>
    </div>
</div>

{{-- Tarjetas resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">{{ $resumen['presente'] }}</div>
                <div class="text-muted small">Presentes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-danger">{{ $resumen['ausente'] }}</div>
                <div class="text-muted small">Ausentes injustificadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning">{{ $resumen['tarde'] }}</div>
                <div class="text-muted small">Tardanzas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-info">{{ $resumen['justificado'] }}</div>
                <div class="text-muted small">Justificadas</div>
            </div>
        </div>
    </div>
</div>

{{-- Detalle cronológico --}}
@if($detalle->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No hay registros de asistencia para este alumno.
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-calendar-check me-1"></i>Historial detallado ({{ $resumen['total'] }} registros)
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Materia</th>
                    <th>Curso</th>
                    <th class="text-center">Estado</th>
                    <th>Hora llegada</th>
                    <th>Justificación</th>
                    <th>Observación</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalle as $reg)
                <tr>
                    <td class="ps-4">{{ $reg->fecha->format('d/m/Y') }}</td>
                    <td>{{ $reg->materia?->nombre ?? '—' }}</td>
                    <td>{{ $reg->curso?->nombre_completo ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $reg->estadobadge }}">{{ $reg->estadolabel }}</span>
                    </td>
                    <td>
                        {{ $reg->horallegada ? \Carbon\Carbon::parse($reg->horallegada)->format('H:i') : '—' }}
                    </td>
                    <td>
                        @if($reg->fotojustificacion)
                            <a href="{{ asset('storage/' . $reg->fotojustificacion) }}"
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-image me-1"></i>Ver foto
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $reg->observacion ?? '—' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('asistencia.editar', $reg) }}"
                           class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif
@endsection