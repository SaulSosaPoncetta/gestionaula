@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de asistencia</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('asistencia.historial') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" class="form-select">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" {{ ($filtros['curso_id'] ?? '') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $filtros['fecha'] ?? '' }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($asistencias->isNotEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Alumno</th>
                    <th>Materia</th>
                    <th>Estado</th>
                    <th>Observación</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asistencias as $a)
                <tr>
                    <td class="ps-4">{{ $a->fecha->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $a->alumno->nombre_completo }}</td>
                    <td>{{ $a->materia?->nombre ?? '—' }}</td>
                    <td>
                        @php
                            $badges = [
                                'presente'    => 'success',
                                'ausente'     => 'danger',
                                'tarde'       => 'warning',
                                'justificado' => 'info',
                            ];
                        @endphp
                        <span class="badge bg-{{ $badges[$a->estado] }}">{{ ucfirst($a->estado) }}</span>
                    </td>
                    <td>{{ $a->observacion ?? '—' }}</td>
                    <td>{{ $a->docente->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $asistencias->links() }}</div>
@elseif(request()->filled('curso_id'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay registros para los filtros seleccionados.
    </div>
@endif
@endsection