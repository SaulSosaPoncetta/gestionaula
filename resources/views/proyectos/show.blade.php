@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-folder2-open me-2"></i>{{ $proyecto->titulo }}</h4>
        <p class="text-muted">
            {{ $proyecto->materia?->nombre }} — {{ $proyecto->curso?->nombre_completo }}
            <span class="badge bg-{{ $proyecto->estadobadge }} ms-2">{{ $proyecto->estadolabel }}</span>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

<div class="row g-4">
    {{-- Datos del proyecto --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle me-1"></i>Datos del proyecto
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Materia</td>
                        <td class="fw-semibold">{{ $proyecto->materia?->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Curso</td>
                        <td>{{ $proyecto->curso?->nombre_completo ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Establecimiento</td>
                        <td>{{ $proyecto->establecimiento?->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fecha</td>
                        <td>{{ $proyecto->fecha?->format('d/m/Y') ?? '—' }}
                            @if($proyecto->hora) — {{ substr($proyecto->hora, 0, 5) }}hs @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fecha presentación</td>
                        <td>{{ $proyecto->fechapresentacion?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                    @if($proyecto->descripcion)
                    <tr>
                        <td class="text-muted">Descripción</td>
                        <td>{{ $proyecto->descripcion }}</td>
                    </tr>
                    @endif
                    @if($proyecto->observaciones)
                    <tr>
                        <td class="text-muted">Observaciones</td>
                        <td>{{ $proyecto->observaciones }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Actividad generada --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clipboard2-check me-1 text-success"></i>Actividad generada
            </div>
            <div class="card-body">
                @if($proyecto->actividad)
                    <div class="fw-semibold">{{ $proyecto->actividad->titulo }}</div>
                    <div class="text-muted small mb-2">
                        Unidad {{ $proyecto->actividad->numerounidad ?? '—' }}
                        — {{ $proyecto->actividad->tipoactividad?->denominacion ?? '—' }}
                    </div>
                    <a href="{{ route('actividades.show', $proyecto->actividad) }}"
                       class="btn btn-sm btn-outline-success">
                        <i class="bi bi-eye me-1"></i>Ver actividad
                    </a>
                @else
                    <div class="text-muted">Sin actividad asociada.</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Alumnos y carpetas --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-people me-1"></i>Alumnos y carpetas de campo
        <span class="badge bg-info ms-2">{{ $proyecto->carpetas->count() }} carpetas</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th>Tipo cursada</th>
                    <th class="text-center">Entradas</th>
                    <th class="text-center">Carpeta de campo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyecto->carpetas as $carpeta)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $carpeta->alumno?->nombre_completo ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $carpeta->alumno?->tipocursadabadge }}">
                            {{ $carpeta->alumno?->tipocursadalabel }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $carpeta->entradas->count() }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('proyectos.carpeta', $carpeta) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-folder2-open me-1"></i>Ver carpeta
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection