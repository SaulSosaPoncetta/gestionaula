@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-list-check me-2"></i>Listado de asistencias</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.accion', ['curso_id' => $curso->id, 'materia_id' => $materia->id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($resumenAlumnos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay alumnos registrados en este curso.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th class="text-center text-success">Presentes</th>
                    <th class="text-center text-danger">Ausentes</th>
                    <th class="text-center text-warning">Tardanzas</th>
                    <th class="text-center text-info">Justificadas</th>
                    <th class="text-center">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenAlumnos as $item)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $item['alumno']->nombre_completo }}</td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ $item['presente'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-danger">{{ $item['ausente'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark">{{ $item['tarde'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $item['justificado'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $item['total'] }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('asistencia.alumno', ['alumno_id' => $item['alumno']->id]) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Ver / Editar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection