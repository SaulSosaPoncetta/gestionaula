@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Entregas incompletas</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong> &mdash; <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaractividad.ver', ['materia_id' => $materia->id, 'curso_id' => $curso->id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($registros->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay entregas incompletas.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th>Actividad</th>
                    <th>Fecha entrega</th>
                    <th class="text-center">Nota</th>
                    <th>Observación</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $reg)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $reg->alumno->nombre_completo }}</td>
                    <td>
                        <div class="fw-semibold">{{ $reg->actividad?->titulo }}</div>
                        @if($reg->actividad?->tema)
                            <div class="text-muted small">{{ $reg->actividad->tema }}</div>
                        @endif
                    </td>
                    <td>{{ $reg->fechaestado?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-center">
                        @if($reg->nota !== null)
                            <span class="badge bg-{{ $reg->nota >= 7 ? 'success' : ($reg->nota >= 4 ? 'warning' : 'danger') }}">
                                {{ number_format($reg->nota, 2) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $reg->observacion ?? '—' }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('calificaractividad.vencida', $reg) }}">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-sm btn-warning text-dark"
                                    onclick="return confirm('¿Cambiar a entrega vencida?')">
                                <i class="bi bi-arrow-right-circle me-1"></i>Pasar a vencida
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection