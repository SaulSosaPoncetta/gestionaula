@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard-check me-2"></i>Tareas</h4>
        <p class="text-muted">Listado de tareas asignadas a tus cursos.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tareas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva tarea
        </a>
    </div>
</div>

@if($tareas->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay tareas creadas todavía.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Título</th>
                    <th>Curso</th>
                    <th>Materia</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th>Entregas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tareas as $tarea)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $tarea->titulo }}</td>
                    <td>{{ $tarea->curso->nombre_completo }}</td>
                    <td>{{ $tarea->materia?->nombre ?? '—' }}</td>
                    <td>
                        {{ $tarea->fechavencimiento->format('d/m/Y') }}
                        @if($tarea->fechavencimiento->isPast() && $tarea->estado === 'activa')
                            <span class="badge bg-danger ms-1">Vencida</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $tarea->estado === 'activa' ? 'success' : 'secondary' }}">
                            {{ ucfirst($tarea->estado) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $entregadas = $tarea->entregas->whereIn('estado', ['entregado', 'aprobado', 'desaprobado'])->count();
                            $total = $tarea->entregas->count();
                        @endphp
                        {{ $entregadas }}/{{ $total }}
                    </td>
                    <td>
                        <a href="{{ route('tareas.show', $tarea) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $tareas->links() }}</div>
@endif
@endsection