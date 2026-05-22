@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-people me-2"></i>Alumnos</h4>
        <p class="text-muted">Gestión de alumnos por curso.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('alumnos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo alumno
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('alumnos.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Buscar por nombre, apellido o DNI..."
                           value="{{ request('buscar') }}">
                </div>
                <div class="col-md-3">
                    <select name="curso_id" class="form-select">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipocursada" class="form-select">
                        <option value="">Todos los tipos</option>
                        @foreach(\App\Models\Alumno::TIPOSCURSADA as $valor => $label)
                            <option value="{{ $valor }}"
                                {{ request('tipocursada') === $valor ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($alumnos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay alumnos registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Apellido y nombre</th>
                    <th>DNI</th>
                    <th>Nacimiento</th>
                    <th>Año</th>
                    <th>División</th>
                    <th>Turno</th>
                    <th class="text-center">Tipo cursada</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $alumno)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $alumno->nombre_completo }}</td>
                    <td>{{ $alumno->dni ?? '—' }}</td>
                    <td>{{ $alumno->fechanacimiento ? $alumno->fechanacimiento->format('d/m/Y') : '—' }}</td>
                    <td>{{ $alumno->curso?->anio ?? '—' }}</td>
                    <td>{{ $alumno->curso?->division ?? '—' }}</td>
                    <td>{{ $alumno->curso?->turno ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $alumno->tipocursadabadge }}">
                            {{ $alumno->tipocursadalabel }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('alumnos.show', $alumno) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('alumnos.edit', $alumno) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('alumnos.destroy', $alumno) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este alumno?')">
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
<div class="mt-3">{{ $alumnos->links() }}</div>
@endif
@endsection