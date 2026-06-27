@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calculator me-2"></i>Prenotas calculadas</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
            &mdash;
            Tipo: <strong>{{ $tipocierre }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('prenotas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('prenotas.guardar') }}">
    @csrf
    <input type="hidden" name="materia_id" value="{{ $materia->id }}">
    <input type="hidden" name="curso_id"   value="{{ $curso->id }}">
    <input type="hidden" name="tipocierre" value="{{ $tipocierre }}">

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Alumno</th>
                    <th class="text-center">Prom. Calificaciones</th>
                    <th class="text-center">Prom. Actividades</th>
                    <th class="text-center">Nota Asistencia</th>
                    <th class="text-center">% Asistencia</th>
                    <th class="text-center fw-bold">Nota Final</th>
                    <th class="text-center" colspan="3">Valoraciones</th>
                    <th class="text-center">Ajustar nota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $res)
                @php
                    $nota       = $res['notaFinal'];
                    $colorNota  = $nota >= 7 ? 'success' : ($nota >= 4 ? 'warning' : 'danger');
                    $valoraciones = $res['valoraciones'];
                @endphp
                <tr>
                    <td class="ps-4 fw-semibold">{{ $res['alumno']->nombre_completo }}</td>

                    {{-- Promedio calificaciones --}}
                    <td class="text-center">
                        @if($res['promedioCalificaciones'] !== null)
                            <span class="badge bg-secondary">
                                {{ number_format($res['promedioCalificaciones'], 2) }}
                            </span>
                            <div class="text-muted" style="font-size:0.7rem">
                                {{ $res['cantCalificaciones'] }} reg.
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Promedio actividades --}}
                    <td class="text-center">
                        @if($res['promedioActividades'] !== null)
                            <span class="badge bg-secondary">
                                {{ number_format($res['promedioActividades'], 2) }}
                            </span>
                            <div class="text-muted" style="font-size:0.7rem">
                                {{ $res['cantActividades'] }} act.
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Nota asistencia --}}
                    <td class="text-center">
                        @if($res['notaAsistencia'] !== null)
                            <span class="badge bg-{{ $res['notaAsistencia'] >= 7 ? 'success' : 'danger' }}">
                                {{ $res['notaAsistencia'] }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- % Asistencia --}}
                    <td class="text-center">
                        @if($res['porcentajeAsistencia'] !== null)
                            <span class="badge bg-info">
                                {{ number_format($res['porcentajeAsistencia'], 1) }}%
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Nota final --}}
                    <td class="text-center">
                        <span class="badge bg-{{ $colorNota }} fs-5">
                            {{ number_format($nota, 2) }}
                        </span>
                    </td>

                    {{-- Valoraciones --}}
                    @if($valoraciones->isNotEmpty())
                        @foreach($valoraciones->take(3) as $val)
                        <td class="text-center">
                            <span class="badge bg-primary fs-6">
                                {{ $val->denominacion }}
                            </span>
                            <div class="text-muted" style="font-size:0.7rem">
                                {{ number_format($val->notainferior,1) }}-{{ number_format($val->notasuperior,1) }}
                            </div>
                        </td>
                        @endforeach
                        @for($i = $valoraciones->count(); $i < 3; $i++)
                        <td></td>
                        @endfor
                    @else
                        <td colspan="3" class="text-center text-muted small">Sin valoración</td>
                    @endif

                    {{-- Campos hidden para guardar --}}
                    <input type="hidden" name="registros[{{ $res['alumno']->id }}][notanumerica]"
                           value="{{ $nota }}">
                    <input type="hidden" name="registros[{{ $res['alumno']->id }}][notavalorativa]"
                           value="{{ $valoraciones->pluck('denominacion')->implode(' / ') }}">
                    <input type="hidden" name="registros[{{ $res['alumno']->id }}][promedioactividades]"
                           value="{{ $res['promedioActividades'] }}">
                    <input type="hidden" name="registros[{{ $res['alumno']->id }}][promediocalificaciones]"
                           value="{{ $res['promedioCalificaciones'] }}">
                    <input type="hidden" name="registros[{{ $res['alumno']->id }}][notaasistencia]"
                           value="{{ $res['notaAsistencia'] }}">
                    <input type="hidden" name="registros[{{ $res['alumno']->id }}][porcentajeasistencia]"
                           value="{{ $res['porcentajeAsistencia'] }}">

                    {{-- Ajuste manual de nota --}}
                    <td class="text-center">
                        <input type="number"
                               name="registros[{{ $res['alumno']->id }}][notanumerica]"
                               class="form-control form-control-sm text-center"
                               style="width:80px;margin:0 auto"
                               value="{{ number_format($nota, 2, '.', '') }}"
                               min="1" max="10" step="0.25">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex gap-2 mb-4">
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-circle me-1"></i>Guardar prenotas
    </button>
    <a href="{{ route('prenotas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>

</form>
@endsection