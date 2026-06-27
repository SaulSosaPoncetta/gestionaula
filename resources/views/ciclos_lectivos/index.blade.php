@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar2-range me-2"></i>Ciclos lectivos</h4>
        <p class="text-muted">Administrá los ciclos lectivos. Solo puede haber uno activo a la vez.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ciclos_lectivos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo ciclo lectivo
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>{{ session('info') }}</div>
@endif

@if($cicloActivo && $cicloActivo->terminoPronto())
<div class="alert alert-warning d-flex align-items-center gap-3">
    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
    <div>
        <strong>El ciclo lectivo {{ $cicloActivo->anio }} está por terminar</strong>
        (vence el {{ $cicloActivo->fechafin->format('d/m/Y') }}).
        <br>
        <a href="{{ route('ciclos_lectivos.siguiente', $cicloActivo) }}" class="btn btn-sm btn-warning mt-2">
            <i class="bi bi-arrow-right-circle me-1"></i>Generar ciclo {{ (int)$cicloActivo->anio + 1 }}
        </a>
    </div>
</div>
@endif

@if($cicloActivo && $cicloActivo->yaTermino())
<div class="alert alert-danger d-flex align-items-center gap-3">
    <i class="bi bi-calendar-x-fill fs-4"></i>
    <div>
        <strong>El ciclo lectivo {{ $cicloActivo->anio }} ya finalizó</strong>
        (venció el {{ $cicloActivo->fechafin->format('d/m/Y') }}).
        <br>
        <a href="{{ route('ciclos_lectivos.siguiente', $cicloActivo) }}" class="btn btn-sm btn-danger mt-2">
            <i class="bi bi-plus-circle me-1"></i>Crear ciclo lectivo {{ (int)$cicloActivo->anio + 1 }}
        </a>
    </div>
</div>
@endif

@if($ciclos->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No tenés ciclos lectivos creados.
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 text-center">Año</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ciclos as $ciclo)
                <tr class="{{ $ciclo->activo ? 'table-success' : '' }}">
                    <td class="ps-4 text-center fw-bold fs-5">{{ $ciclo->anio }}</td>
                    <td>{{ $ciclo->fechainicio->format('d/m/Y') }}</td>
                    <td>{{ $ciclo->fechafin->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($ciclo->activo)
                            <span class="badge bg-success">Activo</span>
                            @if($ciclo->yaTermino())
                                <span class="badge bg-danger ms-1">Vencido</span>
                            @elseif($ciclo->terminoPronto())
                                <span class="badge bg-warning text-dark ms-1">Próximo a vencer</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center" style="white-space:nowrap">
                        <a href="{{ route('ciclos_lectivos.edit', $ciclo) }}"
                           class="btn btn-sm btn-secondary me-1">
                            <i class="bi bi-pencil text-white"></i>
                        </a>
                        @if(!$ciclo->activo)
                        <form method="POST" action="{{ route('ciclos_lectivos.activar', $ciclo) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary me-1">
                                <i class="bi bi-check-circle text-white"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('ciclos_lectivos.destroy', $ciclo) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar ciclo lectivo {{ $ciclo->anio }}?')">
                                <i class="bi bi-trash text-white"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
