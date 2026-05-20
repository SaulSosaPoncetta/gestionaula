@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de prenotas</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('prenotas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('prenotas.historial') }}" class="row g-3">
            <div class="col-md-4">
                <select name="materia_id" class="form-select">
                    <option value="">— Todas las materias —</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}"
                            {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="curso_id" class="form-select">
                    <option value="">— Todos los cursos —</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id }}"
                            {{ request('curso_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

@if(!request('materia_id') || !request('curso_id'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Seleccioná materia y curso para ver el historial.
    </div>
@elseif($registros->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay prenotas registradas para este filtro.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        {{ $materia?->nombre }} — {{ $curso?->nombre_completo }}
        <span class="badge bg-primary ms-2">{{ $registros->total() }} registros</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th>Tipo de cierre</th>
                    <th class="text-center">Nota numérica</th>
                    <th>Nota valorativa</th>
                    <th class="text-center">Prom. Calif.</th>
                    <th class="text-center">Prom. Act.</th>
                    <th class="text-center">Nota Asist.</th>
                    <th class="text-center">% Asist.</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $reg)
                @php
                    $color = $reg->notanumerica >= 7 ? 'success' :
                            ($reg->notanumerica >= 4 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td class="ps-4 fw-semibold">{{ $reg->alumno->nombre_completo }}</td>
                    <td>{{ $reg->tipocierre }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color }} fs-6">
                            {{ number_format($reg->notanumerica, 2) }}
                        </span>
                    </td>
                    <td>
                        @foreach(explode(' / ', $reg->notavalorativa ?? '') as $val)
                            @if($val)
                                <span class="badge bg-primary me-1">{{ $val }}</span>
                            @endif
                        @endforeach
                    </td>
                    <td class="text-center">{{ $reg->promediocalificaciones ? number_format($reg->promediocalificaciones, 2) : '—' }}</td>
                    <td class="text-center">{{ $reg->promedioactividades ? number_format($reg->promedioactividades, 2) : '—' }}</td>
                    <td class="text-center">{{ $reg->notaasistencia ?? '—' }}</td>
                    <td class="text-center">{{ $reg->porcentajeasistencia ? number_format($reg->porcentajeasistencia, 1) . '%' : '—' }}</td>
                    <td>{{ $reg->fecharegistro->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $registros->links() }}</div>
@endif
@endsection