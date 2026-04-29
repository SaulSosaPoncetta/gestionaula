@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard-check me-2"></i>{{ $tarea->titulo }}</h4>
        <p class="text-muted">
            <strong>{{ $tarea->curso->nombre_completo }}</strong>
            @if($tarea->materia) — {{ $tarea->materia->nombre }} @endif
            &mdash; Vence: {{ $tarea->fechavencimiento->format('d/m/Y') }}
            <span class="badge bg-{{ $tarea->estado === 'activa' ? 'success' : 'secondary' }} ms-2">
                {{ ucfirst($tarea->estado) }}
            </span>
        </p>
        @if($tarea->descripcion)
            <div class="alert alert-light border">{{ $tarea->descripcion }}</div>
        @endif
    </div>
    <div class="col-auto d-flex gap-2 align-items-start">
        @if($tarea->estado === 'activa')
            <form method="POST" action="{{ route('tareas.cerrar', $tarea) }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary"
                        onclick="return confirm('¿Cerrar esta tarea?')">
                    <i class="bi bi-lock me-1"></i>Cerrar tarea
                </button>
            </form>
        @endif
        <a href="{{ route('tareas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('tareas.entregas', $tarea) }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Alumno</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entregas as $entrega)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $entrega->alumno->nombre_completo }}</td>
                        <td>
                            @if($tarea->estado === 'activa')
                            <select name="entregas[{{ $entrega->id }}][estado]" class="form-select form-select-sm" style="width:160px">
                                @foreach(['pendiente', 'entregado', 'aprobado', 'desaprobado'] as $est)
                                    <option value="{{ $est }}" {{ $entrega->estado === $est ? 'selected' : '' }}>
                                        {{ ucfirst($est) }}
                                    </option>
                                @endforeach
                            </select>
                            @else
                                @php
                                    $badges = ['pendiente' => 'secondary', 'entregado' => 'primary', 'aprobado' => 'success', 'desaprobado' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $badges[$entrega->estado] }}">{{ ucfirst($entrega->estado) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($tarea->estado === 'activa')
                            <input type="text" class="form-control form-control-sm"
                                   name="entregas[{{ $entrega->id }}][observacion]"
                                   value="{{ $entrega->observacion }}" placeholder="Opcional">
                            @else
                                {{ $entrega->observacion ?? '—' }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($tarea->estado === 'activa')
    <div class="mt-4">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Guardar entregas
        </button>
    </div>
    @endif
</form>
@endsection