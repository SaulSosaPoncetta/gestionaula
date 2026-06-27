@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de cierres</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('cierre_cuatri.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Nuevo cierre
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cierre_cuatri.historial') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Materia</label>
                <select name="materia_id" class="form-select">
                    <option value="">— Seleccioná —</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Curso</label>
                <select name="curso_id" class="form-select">
                    <option value="">— Seleccioná —</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id }}" {{ request('curso_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tipo de cierre</label>
                <select name="tipocierre" class="form-select">
                    <option value="">— Todos —</option>
                    @foreach($tiposCierre as $t)
                        <option value="{{ $t }}" {{ request('tipocierre') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
        </form>
    </div>
</div>

@if($registros->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        @if(request()->filled('materia_id'))
            No hay registros para los filtros seleccionados.
        @else
            Seleccioná una materia y un curso para ver los cierres registrados.
        @endif
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-start ps-3">Alumno</th>
                    <th>Tipo cierre</th>
                    <th>Nota numérica</th>
                    <th>Nota valorativa</th>
                    <th>Prom. Calific.</th>
                    <th>Prom. Activ.</th>
                    <th>% Asistencia</th>
                    <th>Fecha registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $reg)
                <tr>
                    <td class="text-start ps-3 fw-semibold">
                        {{ $reg->alumno?->apellido }}, {{ $reg->alumno?->nombre }}
                    </td>
                    <td><span class="badge bg-primary">{{ $reg->tipocierre }}</span></td>
                    <td>
                        <span class="fw-bold fs-5 text-{{ $reg->notanumerica >= 7 ? 'success' : ($reg->notanumerica >= 4 ? 'warning' : 'danger') }}">
                            {{ number_format($reg->notanumerica, 2) }}
                        </span>
                    </td>
                    <td>
                        @foreach(explode(' / ', $reg->notavalorativa ?? '') as $val)
                            @if(trim($val))
                            <span class="badge bg-{{ $reg->notanumerica >= 7 ? 'success' : ($reg->notanumerica >= 4 ? 'warning text-dark' : 'danger') }} me-1">
                                {{ trim($val) }}
                            </span>
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $reg->promediocalificaciones !== null ? number_format($reg->promediocalificaciones, 2) : '—' }}</td>
                    <td>{{ $reg->promedioactividades !== null ? number_format($reg->promedioactividades, 2) : '—' }}</td>
                    <td>{{ $reg->porcentajeasistencia !== null ? number_format($reg->porcentajeasistencia, 1) . '%' : '—' }}</td>
                    <td>{{ $reg->fecharegistro?->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
