@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-building me-2"></i>Cursos</h4>
        <p class="text-muted">Gestión de cursos de la institución.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('cursos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo curso
        </a>
    </div>
</div>

@if($cursos->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay cursos registrados.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>División</th>
                    <th>Turno</th>
                    <th>Nivel</th>
                    <th class="text-center">Alumnos</th>
                    <th class="text-center">Materias</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cursos as $curso)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $curso->nombre }}</td>
                    <td>{{ $curso->division ?? '—' }}</td>
                    <td>{{ $curso->turno ?? '—' }}</td>
                    <td>{{ $curso->nivel ?? '—' }}</td>
                    <td class="text-center"><span class="badge bg-primary">{{ $curso->alumnos_count }}</span></td>
                    <td class="text-center"><span class="badge bg-info">{{ $curso->materias_count }}</span></td>
                    <td class="text-end pe-3">
                        <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('cursos.destroy', $curso) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este curso? Se eliminarán todos sus datos.')">
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
<div class="mt-3">{{ $cursos->links() }}</div>
@endif
@endsection