@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-check me-2"></i>Cierre de notas del cuatrimestre</h4>
        <p class="text-muted">Calculá la nota de cierre de cada alumno en base a calificaciones, actividades y asistencia.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('cierre_cuatri.historial') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Ver historial
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-sliders me-1"></i>Parámetros del cierre
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('cierre_cuatri.calcular') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">— Seleccioná —</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">— Seleccioná —</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de cierre <span class="text-danger">*</span></label>
                    <select name="tipocierre" class="form-select" required>
                        <option value="">— Seleccioná —</option>
                        <option value="1er Cuatrimestre">1er Cuatrimestre</option>
                        <option value="2do Cuatrimestre">2do Cuatrimestre</option>
                        <option value="Anual">Anual</option>
                        <option value="Recuperatorio">Recuperatorio</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-calculator me-1"></i>Calcular
                    </button>
                </div>
            </div>

            <div class="mt-3 p-3 bg-light rounded small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                La nota final se calcula ponderando la cantidad de registros de cada componente:
                calificaciones, notas de actividades, y asistencia.
                <strong>No se incluyen prenotas.</strong>
            </div>
        </form>
    </div>
</div>
@endsection
