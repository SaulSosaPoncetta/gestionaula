@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-star me-2"></i>Especialidades</h4>
        <p class="text-muted">Gestión de especialidades u orientaciones.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('especialidades.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva especialidad
        </a>
    </div>
</div>

@if($especialidades->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay especialidades registradas.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Materias</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($especialidades as $esp)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $esp->nombre }}</td>
                    <td class="text-muted">{{ $esp->descripcion ?? '—' }}</td>
                    <td class="text-center"><span class="badge bg-primary">{{ $esp->materias_count }}</span></td>
                    <td class="text-end pe-3">
                        <a href="{{ route('especialidades.edit', $esp) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('especialidades.destroy', $esp) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta especialidad?')">
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
<div class="mt-3">{{ $especialidades->links() }}</div>
@endif
@endsection