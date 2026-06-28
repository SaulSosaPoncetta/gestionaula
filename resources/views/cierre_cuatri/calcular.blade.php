@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-check me-2"></i>Resultado del cierre</h4>
        <p class="text-muted mb-0">
            <strong>{{ $materia->nombre }}</strong> —
            {{ $curso->nombre_completo }} —
            <span class="badge bg-primary">{{ $tipocierre }}</span>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('cierre_cuatri.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('cierre_cuatri.guardar') }}">
    @csrf
    <input type="hidden" name="materia_id" value="{{ $materia->id }}">
    <input type="hidden" name="curso_id"   value="{{ $curso->id }}">
    <input type="hidden" name="tipocierre" value="{{ $tipocierre }}">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle text-center">
                    <thead style="background-color:#0dcaf0">
                        <tr>
                            <th class="text-start ps-3 text-dark">Alumno</th>
                            <th class="text-dark">Prom.<br>Calificaciones<br><small class="fw-normal">( cant. )</small></th>
                            <th class="text-dark">Prom.<br>Actividades<br><small class="fw-normal">( cant. )</small></th>
                            <th class="text-dark">Asistencia<br><small class="fw-normal">(%)</small></th>
                            <th class="text-dark">Nota<br>Asistencia</th>
                            <th class="text-white" style="background-color:#0a9cc1">Nota<br>Final</th>
                            <th class="text-dark" colspan="{{ $resultados->max(fn($r) => $r['valoraciones']->count()) ?: 2 }}">
                                Valoraciones
                            </th>
                            <th class="text-dark">Nota<br>Manual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxValoraciones = $resultados->max(fn($r) => $r['valoraciones']->count()) ?: 2; @endphp
                        @foreach($resultados as $r)
                        @php
                            $alumno  = $r['alumno'];
                            $nota    = $r['notaFinal'];
                            $valors  = $r['valoraciones'];
                            // Nota valorativa: concatenar todas las denominaciones
                            $notaVal = $valors->pluck('denominacion')->join(' / ');
                        @endphp
                        <tr>
                            <td class="text-start ps-3 fw-semibold">
                                {{ $alumno->apellido }}, {{ $alumno->nombre }}
                            </td>
                            <td>
                                @if($r['promedioCalificaciones'] !== null)
                                    <strong>{{ number_format($r['promedioCalificaciones'], 2) }}</strong>
                                    <br><small class="text-muted">({{ $r['cantCalificaciones'] }})</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($r['promedioActividades'] !== null)
                                    <strong>{{ number_format($r['promedioActividades'], 2) }}</strong>
                                    <br><small class="text-muted">({{ $r['cantActividades'] }})</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($r['porcentajeAsistencia'] !== null)
                                    <span class="badge bg-{{ $r['porcentajeAsistencia'] >= 90 ? 'success' : ($r['porcentajeAsistencia'] >= 75 ? 'warning text-dark' : 'danger') }}">
                                        {{ number_format($r['porcentajeAsistencia'], 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($r['notaAsistencia'] !== null)
                                    <span class="badge bg-secondary fs-6">{{ $r['notaAsistencia'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="bg-primary bg-opacity-10">
                                <span class="fw-bold fs-5 text-primary">{{ number_format($nota, 2) }}</span>
                            </td>

                            {{-- Columnas de valoraciones --}}
                            @for($i = 0; $i < $maxValoraciones; $i++)
                            <td>
                                @if($valors->get($i))
                                    <span class="badge bg-{{ $nota >= 7 ? 'success' : ($nota >= 4 ? 'warning text-dark' : 'danger') }} px-3 py-2">
                                        {{ $valors->get($i)->denominacion }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endfor

                            {{-- Nota manual editable --}}
                            <td>
                                <input type="number" step="0.01" min="1" max="10"
                                       name="registros[{{ $alumno->id }}][notanumerica]"
                                       class="form-control form-control-sm text-center"
                                       style="width:80px; margin:0 auto;"
                                       value="{{ number_format($nota, 2, '.', '') }}">
                                {{-- Campos ocultos con los datos calculados --}}
                                <input type="hidden" name="registros[{{ $alumno->id }}][notavalorativa]"
                                       value="{{ $notaVal }}">
                                <input type="hidden" name="registros[{{ $alumno->id }}][promediocalificaciones]"
                                       value="{{ $r['promedioCalificaciones'] ?? '' }}">
                                <input type="hidden" name="registros[{{ $alumno->id }}][promedioactividades]"
                                       value="{{ $r['promedioActividades'] ?? '' }}">
                                <input type="hidden" name="registros[{{ $alumno->id }}][notaasistencia]"
                                       value="{{ $r['notaAsistencia'] ?? '' }}">
                                <input type="hidden" name="registros[{{ $alumno->id }}][porcentajeasistencia]"
                                       value="{{ $r['porcentajeAsistencia'] ?? '' }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 p-3 bg-light">
        <div class="row text-center">
            <div class="col">
                <div class="small text-muted">Total alumnos</div>
                <div class="fw-bold fs-5">{{ $resultados->count() }}</div>
            </div>
            <div class="col">
                <div class="small text-muted">Promedio del grupo</div>
                <div class="fw-bold fs-5 text-primary">
                    {{ number_format($resultados->avg(fn($r) => $r['notaFinal']), 2) }}
                </div>
            </div>
            <div class="col">
                <div class="small text-muted">Aprobados (≥7)</div>
                <div class="fw-bold fs-5 text-success">
                    {{ $resultados->filter(fn($r) => $r['notaFinal'] >= 7)->count() }}
                </div>
            </div>
            <div class="col">
                <div class="small text-muted">Regulares (4-6.99)</div>
                <div class="fw-bold fs-5 text-warning">
                    {{ $resultados->filter(fn($r) => $r['notaFinal'] >= 4 && $r['notaFinal'] < 7)->count() }}
                </div>
            </div>
            <div class="col">
                <div class="small text-muted">Reprobados (&lt;4)</div>
                <div class="fw-bold fs-5 text-danger">
                    {{ $resultados->filter(fn($r) => $r['notaFinal'] < 4)->count() }}
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="bi bi-floppy me-1"></i>Guardar calificaciones de cierre
        </button>
        <a href="{{ route('cierre_cuatri.index') }}" class="btn btn-outline-secondary btn-lg">
            Cancelar
        </a>
    </div>
</form>
@endsection
