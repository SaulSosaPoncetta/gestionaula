@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar3 me-2"></i>Períodos</h4>
        <p class="text-muted">Gestión de períodos de evaluación.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('periodos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo período
        </a>
    </div>
</div>

@if($periodos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay períodos registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Orden</th>
                    <th>Denominación</th>
                    <th class="text-center">Calificaciones</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($periodos as $periodo)
                <tr>
                    <td class="ps-4">{{ $periodo->orden }}</td>
                    <td class="fw-semibold">{{ $periodo->denominacion }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ $periodo->calificaciones_count }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('periodos.edit', $periodo) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('periodos.destroy', $periodo) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este período?')">
                                <i class="bi bi-trash"></i>
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