@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold"><i class="bi bi-person-badge me-2"></i>{{ $alumno->nombre_completo }}</h4>
            <p class="text-muted">Ficha del estudiante</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    {{-- Datos del alumno --}}
    <div class="row g-3 mb-4">

        {{-- Datos personales --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person me-1"></i>Datos personales
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Apellido y nombre</td>
                            <td class="fw-semibold">{{ $alumno->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">DNI</td>
                            <td>{{ $alumno->dni ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha de nacimiento</td>
                            <td>{{ $alumno->fechanacimiento ? $alumno->fechanacimiento->format('d/m/Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Teléfono</td>
                            <td>
                                @if ($alumno->telefono)
                                    <i class="bi bi-telephone me-1 text-muted"></i>{{ $alumno->telefono }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>
                                @if ($alumno->email)
                                    <a href="mailto:{{ $alumno->email }}" class="text-decoration-none">
                                        <i class="bi bi-envelope me-1 text-muted"></i>{{ $alumno->email }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Datos académicos --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-building me-1"></i>Datos académicos
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Año</td>
                            <td>{{ $alumno->curso?->anio ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">División</td>
                            <td>{{ $alumno->curso?->division ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Turno</td>
                            <td>{{ $alumno->curso?->turno ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipo de cursada</td>
                            <td>
                                <span class="badge bg-{{ $alumno->tipocursadabadge }}">
                                    {{ $alumno->tipocursadalabel }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nivel</td>
                            <td>{{ $alumno->curso?->nivel?->nombre ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Especialidad</td>
                            <td>{{ $alumno->curso?->especialidad?->nombre ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Establecimiento</td>
                            <td>{{ $alumno->curso?->establecimiento?->nombre ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen asistencias --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-success">{{ $resumen['presente'] }}</div>
                    <div class="text-muted small">Presentes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $resumen['ausente'] }}</div>
                    <div class="text-muted small">Ausentes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $resumen['tarde'] }}</div>
                    <div class="text-muted small">Tardanzas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-info">{{ $resumen['justificado'] }}</div>
                    <div class="text-muted small">Justificadas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de asistencias --}}
    @if ($asistencias->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-calendar-check me-1"></i>Historial de asistencias ({{ $resumen['total'] }})
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Materia</th>
                            <th class="text-center">Estado</th>
                            <th>Hora llegada</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asistencias as $reg)
                            <tr>
                                <td class="ps-4">{{ $reg->fecha->format('d/m/Y') }}</td>
                                <td>{{ $reg->materia?->nombre ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $reg->estadobadge }}">{{ $reg->estadolabel }}</span>
                                </td>
                                <td>
                                    {{ $reg->horallegada ? \Carbon\Carbon::parse($reg->horallegada)->format('H:i') : '—' }}
                                </td>
                                <td class="text-muted small">{{ $reg->observacion ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
