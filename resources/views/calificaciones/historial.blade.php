@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de calificaciones</h4>
        <p class="text-muted">Notas agrupadas por estudiante de todas las fuentes.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('calificaciones.historial') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" class="form-select">
                        <option value="">— Todos —</option>
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

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Período</label>
                    <select name="periodo_id" class="form-select">
                        <option value="">— Todos —</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p->id }}"
                                {{ ($filtros['periodo_id'] ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de evaluación</label>
                    <select name="tipoevaluacion_id" class="form-select">
                        <option value="">— Todos —</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->id }}"
                                {{ ($filtros['tipoevaluacion_id'] ?? '') == $t->id ? 'selected' : '' }}>
                                {{ $t->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
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

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>

                @if(!empty($filtros))
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('calificaciones.historial') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

@if(empty($filtros))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Seleccioná al menos un filtro para ver el historial.
    </div>
@elseif($calificaciones->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay calificaciones para los filtros seleccionados.
    </div>
@else

{{-- Agrupado por alumno --}}
@foreach($calificaciones as $alumnoId => $items)
@php
    $alumno  = $items->first()['alumno'];
    $curso   = $alumno?->curso;
    $promedio = $items->avg('nota');
    $color   = $promedio >= 7 ? 'success' : ($promedio >= 4 ? 'warning' : 'danger');
@endphp

<div class="card border-0 shadow-sm mb-4">
    {{-- Header del alumno --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold fs-5">
                <i class="bi bi-person me-1 text-primary"></i>
                {{ $alumno?->nombre_completo ?? '—' }}
            </span>
            <span class="ms-3 text-muted small">
                @if($curso)
                    <i class="bi bi-building me-1"></i>
                    Año: <strong>{{ $curso->anio }}</strong>
                    &nbsp;|&nbsp;
                    División: <strong>{{ $curso->division }}</strong>
                    @if($curso->turno)
                        &nbsp;|&nbsp; Turno: <strong>{{ $curso->turno }}</strong>
                    @endif
                    @if($curso->especialidad)
                        &nbsp;|&nbsp; {{ $curso->especialidad->nombre }}
                    @endif
                @endif
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                {{ $items->count() }} nota(s)
            </span>
            <span class="badge bg-{{ $color }} fs-6">
                Promedio: {{ number_format($promedio, 2) }}
            </span>
        </div>
    </div>

    {{-- Tabla de notas --}}
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Materia</th>
                    <th>Período</th>
                    <th>Trabajo / Actividad</th>
                    <th>Tipo de evaluación</th>
                    <th>Origen</th>
                    <th class="text-center">Nota</th>
                    <th>Observación</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                @php
                    $notaColor = $item['nota'] >= 7 ? 'success' :
                                ($item['nota'] >= 4 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td class="ps-4 fw-semibold">{{ $item['materia'] }}</td>
                    <td>{{ $item['periodo'] }}</td>
                    <td>{{ $item['trabajo'] }}</td>
                    <td>{{ $item['tipoevaluacion'] }}</td>
                    <td>
                        @if($item['origen'] === 'actividad')
                            <span class="badge bg-info">Actividad</span>
                        @else
                            <span class="badge bg-secondary">Evaluación</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $notaColor }} fs-6">
                            {{ number_format($item['nota'], 2) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $item['observacion'] ?? '—' }}</td>
                    <td class="text-muted small">{{ $item['docente'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

@endif
@endsection