@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Cargar notas</h4>
        <p class="text-muted">
            <strong>{{ $curso->nombre_completo }}</strong>
            &mdash; {{ $materia->nombre }}
            &mdash; {{ $periodo->denominacion }}
            &mdash; {{ $tipo->denominacion }}
            &mdash; {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($curso->alumnos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>Este curso no tiene alumnos registrados.
    </div>
@else
<form method="POST" action="{{ route('calificaciones.guardar') }}">
    @csrf
    <input type="hidden" name="curso_id"          value="{{ $curso->id }}">
    <input type="hidden" name="materia_id"         value="{{ $materia->id }}">
    <input type="hidden" name="periodo_id"         value="{{ $periodo->id }}">
    <input type="hidden" name="tipoevaluacion_id"  value="{{ $tipo->id }}">
    <input type="hidden" name="fecha"              value="{{ $fecha }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Alumno</th>
                        <th style="width:160px">Nota (0 a 10)</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($curso->alumnos->sortBy('apellido') as $alumno)
                    @php $cal = $calificaciones[$alumno->id] ?? null; @endphp
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $alumno->nombre_completo }}</td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm"
                                   name="calificaciones[{{ $alumno->id }}][nota]"
                                   value="{{ $cal?->nota }}"
                                   min="0" max="10" step="0.25"
                                   placeholder="—">
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   name="calificaciones[{{ $alumno->id }}][observacion]"
                                   value="{{ $cal?->observacion }}"
                                   placeholder="Opcional">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Guardar calificaciones
        </button>
        <a href="{{ route('calificaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endif
@endsection