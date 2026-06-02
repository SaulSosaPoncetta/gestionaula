@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de asistencias</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('asistencia.historial') }}" id="formHistorial">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" id="curso_id" class="form-select">
                        <option value="">— Selecciona —</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ ($filtros['curso_id'] ?? '') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Materia</label>
                    <select name="materia_id" class="form-select">
                        <option value="">— Todas —</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}"
                                {{ ($filtros['materia_id'] ?? '') == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha inicio</label>
                    <input type="date" name="fechainicio" class="form-control"
                           value="{{ $filtros['fechainicio'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha fin</label>
                    <input type="date" name="fechafin" class="form-control"
                           value="{{ $filtros['fechafin'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Alumno</label>
                    <select name="alumno_id" class="form-select">
                        <option value="">— Todos —</option>
                        @foreach($alumnos as $alumno)
                            <option value="{{ $alumno->id }}"
                                {{ ($filtros['alumno_id'] ?? '') == $alumno->id ? 'selected' : '' }}>
                                {{ $alumno->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    @if(!empty($filtros))
                    <a href="{{ route('asistencia.historial') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@if(empty($filtros))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Seleccioná un curso para ver el historial.
    </div>
@elseif($asistencias->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay registros para los filtros seleccionados.
    </div>
@else

{{-- Resumen general --}}
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

{{-- Detalle por alumno --}}
@foreach($asistencias as $alumnoId => $registros)
@php
    $alumno      = $registros->first()->alumno;
    $presentes   = $registros->where('estado', 'presente')->count();
    $ausentes    = $registros->where('estado', 'ausente')->count();
    $justific    = $registros->where('estado', 'justificado')->count();
    $tardes      = $registros->where('estado', 'tarde');
@endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">
            <i class="bi bi-person me-1 text-primary"></i>
            {{ $alumno->nombre_completo }}
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success">{{ $presentes }} presentes</span>
            <span class="badge bg-danger">{{ $ausentes }} ausentes</span>
            <span class="badge bg-info">{{ $justific }} justificados</span>
            <span class="badge bg-warning text-dark">{{ $tardes->count() }} tardanzas</span>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Materia</th>
                    <th class="text-center">Estado</th>
                    <th>Hora llegada</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $reg)
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

@endif
@endsection

@push('scripts')
<script>
// Al cambiar curso recargar alumnos para el filtro
document.getElementById('curso_id').addEventListener('change', function () {
    const cursoId = this.value;
    if (cursoId) {
        // Mantener otros filtros y recargar
        const form = document.getElementById('formHistorial');
        form.submit();
    }
});
</script>
@endpush