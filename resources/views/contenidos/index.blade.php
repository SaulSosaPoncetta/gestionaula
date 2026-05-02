@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-richtext me-2"></i>Contenidos</h4>
        <p class="text-muted">Registro de temas y contenidos dictados por materia.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('contenidos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo contenido
        </a>
    </div>
</div>

{{-- Filtro por materia --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('contenidos.index') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Filtrar por materia</label>
                <select name="materia_id" class="form-select">
                    <option value="">Todas las materias</option>
                    @foreach($materias as $materia)
                        <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                            {{ $materia->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
            @if(request('materia_id'))
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('contenidos.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@if($contenidos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay contenidos registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Materia</th>
                    <th>Tema</th>
                    <th>Subtemas</th>
                    <th>Observación</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($contenidos as $contenido)
                <tr>
                    <td class="ps-4">{{ $contenido->fecha->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-primary">{{ $contenido->materia?->nombre ?? '—' }}</span>
                    </td>
                    <td class="fw-semibold">{{ $contenido->tema }}</td>
                    <td>
                        @if($contenido->subtemas->isNotEmpty())
                            <ul class="mb-0 ps-3">
                                @foreach($contenido->subtemas as $sub)
                                    <li class="small">{{ $sub->subtema }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $contenido->observacion ?? '—' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('contenidos.edit', $contenido) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('contenidos.destroy', $contenido) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este contenido?')">
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
<div class="mt-3">{{ $contenidos->links() }}</div>
@endif
@endsection