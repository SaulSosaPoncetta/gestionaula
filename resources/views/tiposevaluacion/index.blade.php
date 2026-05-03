@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-card-checklist me-2"></i>Tipos de evaluación</h4>
        <p class="text-muted">Gestión de tipos de evaluación.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tiposevaluacion.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo tipo
        </a>
    </div>
</div>

@if($tipos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay tipos de evaluación registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Denominación</th>
                    <th class="text-center">Calificaciones</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tipos as $tipo)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $tipo->denominacion }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ $tipo->calificaciones_count }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('tiposevaluacion.edit', $tipo) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('tiposevaluacion.destroy', $tipo) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este tipo de evaluación?')">
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