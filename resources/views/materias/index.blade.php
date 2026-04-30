@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-book me-2"></i>Materias</h4>
        <p class="text-muted">Gestión de materias del plan de estudios.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('materias.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva materia
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('materias.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <select name="ciclo_id" class="form-select">
                        <option value="">Todos los ciclos</option>
                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->id }}" {{ request('ciclo_id') == $ciclo->id ? 'selected' : '' }}>
                                {{ $ciclo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="area_formacion_id" class="form-select">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_formacion_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($materias->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay materias registradas.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>Ciclo</th>
                    <th>Área</th>
                    <th>Tipo</th>
                    <th>Año</th>
                    <th>HS Sem.</th>
                    <th>HS Anu.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($materias as $materia)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $materia->nombre }}</td>
                    <td>
                        @if($materia->ciclo)
                            <span class="badge bg-{{ $materia->ciclo->tipo === 'basico' ? 'info' : 'warning' }}">
                                {{ $materia->ciclo->nombre }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $materia->areaformacion?->nombre ?? '—' }}</td>
                    <td>{{ $materia->tipomaterialabel ?? '—' }}</td>
                    <td>{{ $materia->anio ?? '—' }}</td>
                    <td>{{ $materia->cargahorariasemanal ?? '—' }}</td>
                    <td>{{ $materia->cargahorariaanual ?? '—' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('materias.edit', $materia) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('materias.destroy', $materia) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta materia?')">
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
<div class="mt-3">{{ $materias->links() }}</div>
@endif
@endsection