@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Registrar asistencia</h4>
        <p class="text-muted">
            <strong>{{ $curso->nombre_completo }}</strong>
            @if($materia) — {{ $materia->nombre }} @endif
            &mdash; {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($curso->alumnos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>Este curso no tiene alumnos registrados.
    </div>
@else
<form method="POST" action="{{ route('asistencia.guardar') }}">
    @csrf
    <input type="hidden" name="curso_id" value="{{ $curso->id }}">
    <input type="hidden" name="materia_id" value="{{ $materia?->id }}">
    <input type="hidden" name="fecha" value="{{ $fecha }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Alumno</th>
                        <th class="text-center">Presente</th>
                        <th class="text-center">Ausente</th>
                        <th class="text-center">Tarde</th>
                        <th class="text-center">Justificado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($curso->alumnos->sortBy('apellido') as $alumno)
                    @php $estadoActual = $asistencias[$alumno->id]->estado ?? 'presente'; @endphp
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $alumno->nombre_completo }}</td>
                        @foreach(['presente', 'ausente', 'tarde', 'justificado'] as $estado)
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input"
                                       type="radio"
                                       name="asistencias[{{ $alumno->id }}][estado]"
                                       value="{{ $estado }}"
                                       {{ $estadoActual === $estado ? 'checked' : '' }}
                                       required>
                            </div>
                        </td>
                        @endforeach
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   name="asistencias[{{ $alumno->id }}][observacion]"
                                   value="{{ $asistencias[$alumno->id]->observacion ?? '' }}"
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
            <i class="bi bi-check-circle me-1"></i>Guardar asistencia
        </button>
        <a href="{{ route('asistencia.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endif
@endsection