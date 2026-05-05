@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-check me-2"></i>Actividades</h4>
        <p class="text-muted">Gestión de actividades pedagógicas por materia y curso.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('actividades.seleccionar') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva actividad
        </a>
    </div>
</div>

@if($actividades->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay actividades registradas.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Título</th>
                    <th>Materia</th>
                    <th>Curso</th>
                    <th>Tipo</th>
                    <th>Inicio</th>
                    <th>Entrega</th>
                    <th class="text-center">Grupal</th>
                    <th class="text-center">Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividades as $act)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $act->titulo }}</td>
                    <td>{{ $act->materia?->nombre ?? '—' }}</td>
                    <td>{{ $act->curso?->nombre_completo ?? '—' }}</td>
                    <td>{{ $act->tipoactividad?->denominacion ?? '—' }}</td>
                    <td>{{ $act->fechainicio->format('d/m/Y') }}</td>
                    <td>{{ $act->fechaentrega->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($act->esgrupal)
                            <span class="badge bg-info">Grupal</span>
                        @else
                            <span class="badge bg-secondary">Individual</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $act->estado === 'activa' ? 'success' : 'secondary' }}">
                            {{ ucfirst($act->estado) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('actividades.show', $act) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('actividades.destroy', $act) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta actividad?')">
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
<div class="mt-3">{{ $actividades->links() }}</div>
@endif
@endsection