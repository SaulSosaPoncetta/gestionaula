@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-star me-2"></i>Tipos de valoración</h4>
        <p class="text-muted">Gestión de escalas de valoración para calificaciones.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tipovaloraciones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva valoración
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if($valoraciones->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay tipos de valoración registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Denominación</th>
                    <th class="text-center">Nota límite inferior</th>
                    <th class="text-center">Nota límite superior</th>
                    <th class="text-center">Rango</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($valoraciones as $val)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $val->denominacion }}</td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ number_format($val->notainferior, 2) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ number_format($val->notasuperior, 2) }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $color = $val->notasuperior >= 7 ? 'success' :
                                    ($val->notasuperior >= 4 ? 'warning' : 'danger');
                        @endphp
                        <span class="badge bg-{{ $color }}">
                            {{ number_format($val->notainferior, 2) }}
                            —
                            {{ number_format($val->notasuperior, 2) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('tipovaloraciones.edit', $val) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('tipovaloraciones.destroy', $val) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta valoración?')">
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
<div class="mt-3">{{ $valoraciones->links() }}</div>
@endif
@endsection