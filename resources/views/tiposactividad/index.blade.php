@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-activity me-2"></i>Tipos de actividad</h4>
        <p class="text-muted">Gestión de tipos de actividades pedagógicas.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tiposactividad.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo tipo
        </a>
    </div>
</div>

@if($tipos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay tipos de actividad registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Denominación</th>
                    <th>Descripción</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tipos as $tipo)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $tipo->denominacion }}</td>
                    <td class="text-muted small">{{ $tipo->descripcion ?? '—' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('tiposactividad.edit', $tipo) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('tiposactividad.destroy', $tipo) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este tipo de actividad?')">
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
<div class="mt-3">{{ $tipos->links() }}</div>
@endif
@endsection