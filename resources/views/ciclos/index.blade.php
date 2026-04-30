@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Ciclos</h4>
        <p class="text-muted">Gestión de ciclos educativos aplicables a las materias.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ciclos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo ciclo
        </a>
    </div>
</div>

@if($ciclos->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay ciclos registrados.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th class="text-center">Materias</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($ciclos as $ciclo)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $ciclo->nombre }}</td>
                    <td>
                        <span class="badge bg-{{ $ciclo->tipo === 'basico' ? 'info' : 'warning' }}">
                            {{ $ciclo->tipolabel }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $ciclo->descripcion ?? '—' }}</td>
                    <td class="text-center"><span class="badge bg-primary">{{ $ciclo->materias_count }}</span></td>
                    <td class="text-end pe-3">
                        <a href="{{ route('ciclos.edit', $ciclo) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('ciclos.destroy', $ciclo) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este ciclo?')">
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
<div class="mt-3">{{ $ciclos->links() }}</div>
@endif
@endsection