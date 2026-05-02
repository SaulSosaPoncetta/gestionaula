@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-bookmark me-2"></i>Planificaciones</h4>
        <p class="text-muted">Planificación anual por materia.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('planificaciones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva planificación
        </a>
    </div>
</div>

@if($planificaciones->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay planificaciones registradas.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Materia</th>
                    <th>Ciclo lectivo</th>
                    <th>Descripción</th>
                    <th class="text-center">Unidades</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($planificaciones as $plan)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $plan->materia?->nombre ?? '—' }}</td>
                    <td>{{ $plan->ciclo }}</td>
                    <td class="text-muted small">{{ Str::limit($plan->descripcion, 60) ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ $plan->unidades_count }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('planificaciones.show', $plan) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('planificaciones.edit', $plan) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('planificaciones.destroy', $plan) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta planificación y todas sus unidades?')">
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
<div class="mt-3">{{ $planificaciones->links() }}</div>
@endif
@endsection