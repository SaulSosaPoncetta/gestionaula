@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-building me-2"></i>Establecimientos</h4>
        <p class="text-muted">Gestión de establecimientos educativos.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('establecimientos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo establecimiento
        </a>
    </div>
</div>

@if($establecimientos->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay establecimientos registrados.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>CUE</th>
                    <th>Modalidad</th>
                    <th>Nivel</th>
                    <th>Localidad</th>
                    <th class="text-center">Cursos</th>
                    <th class="text-center">Docentes</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($establecimientos as $est)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $est->nombre }}</td>
                    <td>{{ $est->cue ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $est->modalidad === 'tecnico' ? 'warning' : 'info' }}">
                            {{ $est->modalidadlabel }}
                        </span>
                    </td>
                    <td>{{ $est->nivel->nombre }}</td>
                    <td>{{ $est->localidad ?? '—' }}</td>
                    <td class="text-center"><span class="badge bg-primary">{{ $est->cursos_count }}</span></td>
                    <td class="text-center"><span class="badge bg-success">{{ $est->docentes_count }}</span></td>
                    <td class="text-end pe-3">
                        <a href="{{ route('establecimientos.show', $est) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('establecimientos.edit', $est) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('establecimientos.destroy', $est) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este establecimiento?')">
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
<div class="mt-3">{{ $establecimientos->links() }}</div>
@endif
@endsection