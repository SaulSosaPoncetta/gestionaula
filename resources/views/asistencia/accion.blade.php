@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Asistencia</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4 justify-content-center mt-2">

    {{-- Registrar nueva asistencia --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body py-5">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-calendar-plus fs-1 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-2">Registrar asistencia</h5>
                <p class="text-muted small mb-4">Tomá la asistencia del día de hoy o de una fecha específica.</p>
                <form method="GET" action="{{ route('asistencia.registrar') }}">
                    <input type="hidden" name="curso_id"   value="{{ $curso->id }}">
                    <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Fecha</label>
                        <input type="date" name="fecha" class="form-control"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-1"></i>Registrar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Ver listado de asistencias --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body py-5">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-list-check fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold mb-2">Ver listado</h5>
                <p class="text-muted small mb-4">Consultá el resumen de asistencias de todos los estudiantes.</p>
                <a href="{{ route('asistencia.listado', ['curso_id' => $curso->id, 'materia_id' => $materia->id]) }}"
                   class="btn btn-success w-100">
                    <i class="bi bi-eye me-1"></i>Ver listado
                </a>
            </div>
        </div>
    </div>

    {{-- Editar asistencia de un alumno --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body py-5">
                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-person-gear fs-1 text-warning"></i>
                </div>
                <h5 class="fw-bold mb-2">Editar por alumno</h5>
                <p class="text-muted small mb-4">Buscá un alumno para ver o editar su asistencia.</p>
                <a href="{{ route('asistencia.alumno') }}"
                   class="btn btn-warning text-dark w-100">
                    <i class="bi bi-search me-1"></i>Buscar alumno
                </a>
            </div>
        </div>
    </div>

</div>
@endsection