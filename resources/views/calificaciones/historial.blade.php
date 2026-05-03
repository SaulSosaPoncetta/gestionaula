@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de calificaciones</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('calificaciones.historial') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ ($filtros['curso_id'] ?? '') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Período</label>
                    <select name="periodo_id" class="form-select">
                        <option value="">Todos</option>
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
                        <option value="">Todos</option>
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
                        <option value="">Todos</option>
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
            </div>
        </form>
    </div>
</div>

@if($calificaciones->isNotEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th>Materia</th>
                    <th>Período</th>
                    <th>Tipo</th>
                    <th class="text-center">Nota</th>
                    <th>Observación</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calificaciones as $cal)
                @php
                    $color = match(true) {
                        $cal->nota >= 7 => 'success',
                        $cal->nota >= 4 => 'warning',
                        default         => 'danger',
                    };
                @endphp
                <tr>
                    <td class="ps-4 fw-semibold">{{ $cal->alumno->nombre_completo }}</td>
                    <td>{{ $cal->materia?->nombre ?? '—' }}</td>
                    <td>{{ $cal->periodo?->denominacion ?? '—' }}</td>
                    <td>{{ $cal->tipoevaluacion?->denominacion ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color }} fs-6">
                            {{ number_format($cal->nota, 2) }}
                        </span>
                    </td>
                    <td>{{ $cal->observacion ?? '—' }}</td>
                    <td>{{ $cal->docente->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $calificaciones->links() }}</div>
@elseif(request()->filled('curso_id'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay calificaciones para los filtros seleccionados.
    </div>
@endif
@endsection