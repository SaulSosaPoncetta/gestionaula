@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-book me-2"></i>Materias</h4>
        <p class="text-muted">Gestión de materias por curso.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('materias.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva materia
        </a>
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
                    <th class="ps-4">Materia</th>
                    <th>Curso</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($materias as $materia)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $materia->nombre }}</td>
                    <td>{{ $materia->curso->nombre_completo }}</td>
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