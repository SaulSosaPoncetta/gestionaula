@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar3 me-2"></i>Calendario escolar</h4>
        <p class="text-muted">Registro de eventos, períodos y feriados del ciclo lectivo.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('calendarioescolar.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo evento
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if($eventos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay eventos registrados en el calendario.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Denominación</th>
                    <th>Período</th>
                    <th class="text-center">Feriado</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventos as $evento)
                <tr class="{{ $evento->esferiado ? 'table-danger' : '' }}">
                    <td class="ps-4 fw-semibold">
                        {{ $evento->fecha->format('d/m/Y') }}
                    </td>
                    <td>{{ $evento->denominacion }}</td>
                    <td>
                        @if($evento->periodo)
                            <span class="badge bg-primary">{{ $evento->periodo->denominacion }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($evento->esferiado)
                            <span class="badge bg-danger">
                                <i class="bi bi-check-lg me-1"></i>Sí
                            </span>
                        @else
                            <span class="badge bg-light text-dark">No</span>
                        @endif
                    </td>
                    <td>{{ $evento->fechainicio ? $evento->fechainicio->format('d/m/Y') : '—' }}</td>
                    <td>{{ $evento->fechafin ? $evento->fechafin->format('d/m/Y') : '—' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('calendarioescolar.edit', $evento) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('calendarioescolar.destroy', $evento) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este evento?')">
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
<div class="mt-3">{{ $eventos->links() }}</div>
@endif
@endsection