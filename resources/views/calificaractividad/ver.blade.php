@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Calificar actividades</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('calificaractividad.historial', ['materia_id' => $materia->id, 'curso_id' => $curso->id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Historial
        </a>
        <a href="{{ route('calificaractividad.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($actividades->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay actividades activas para esta materia y curso.
    </div>
@elseif($alumnos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay alumnos registrados en este curso.
    </div>
@else

{{-- Card registro de estados de actividades --}}
<form method="POST" action="{{ route('calificaractividad.guardar') }}" id="formCalificar">
    @csrf
    <input type="hidden" name="materia_id" value="{{ $materia->id }}">
    <input type="hidden" name="curso_id"   value="{{ $curso->id }}">

    @foreach($alumnos as $alumno)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-person me-1"></i>{{ $alumno->nombre_completo }}
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Actividad</th>
                        <th>Período</th>
                        <th style="width:180px">Estado</th>
                        <th style="width:130px">Fecha</th>
                        <th style="width:100px">Nota</th>
                        <th style="width:180px">Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($actividades as $act)
                    @php
                        $key = $alumno->id . '_' . $act->id;
                        $reg = $estadosRegistrados[$key]->first() ?? null;
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $act->titulo }}</div>
                            @if($act->tema)
                                <div class="text-muted small">{{ $act->tema }}</div>
                            @endif
                            @if($act->numerounidad)
                                <span class="badge bg-secondary">Unidad {{ $act->numerounidad }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <div>Inicio: {{ $act->fechainicio->format('d/m/Y') }}</div>
                            <div>Entrega: {{ $act->fechaentrega->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <select name="registros[{{ $alumno->id }}][{{ $act->id }}][estado]"
                                    class="form-select form-select-sm select-estado"
                                    data-alumno="{{ $alumno->id }}"
                                    data-actividad="{{ $act->id }}">
                                <option value="">— Sin estado —</option>
                                <option value="enproceso"
                                    {{ ($reg?->estado ?? '') === 'enproceso' ? 'selected' : '' }}>
                                    En proceso
                                </option>
                                <option value="finalizado"
                                    {{ ($reg?->estado ?? '') === 'finalizado' ? 'selected' : '' }}>
                                    Finalizado
                                </option>
                                <option value="vencida"
                                    {{ ($reg?->estado ?? '') === 'vencida' ? 'selected' : '' }}>
                                    Entrega vencida
                                </option>
                                <option value="incompleta"
                                    {{ ($reg?->estado ?? '') === 'incompleta' ? 'selected' : '' }}>
                                    Entrega incompleta
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="date"
                                   class="form-control form-control-sm campo-fecha"
                                   name="registros[{{ $alumno->id }}][{{ $act->id }}][fechaestado]"
                                   value="{{ $reg?->fechaestado?->format('Y-m-d') ?? '' }}"
                                   style="{{ !$reg || $reg->estado === 'enproceso' ? 'display:none' : '' }}">
                        </td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm"
                                   name="registros[{{ $alumno->id }}][{{ $act->id }}][nota]"
                                   value="{{ $reg?->nota ?? '' }}"
                                   min="0" max="10" step="0.25"
                                   placeholder="—">
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   name="registros[{{ $alumno->id }}][{{ $act->id }}][observacion]"
                                   value="{{ $reg?->observacion ?? '' }}"
                                   placeholder="Opcional">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    <div class="d-flex gap-2 mb-5">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Guardar estados y notas
        </button>
    </div>
</form>

{{-- Card calificaciones por evaluación (el existente) --}}
<div class="card border-0 shadow-sm border-start border-primary border-4 mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-journal-text me-1"></i>Registrar calificaciones por evaluación
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Para registrar notas por período, tipo de evaluación y fecha usá el módulo de calificaciones.
        </p>
        <a href="{{ route('calificaciones.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-right-circle me-1"></i>Ir a calificaciones
        </a>
    </div>
</div>

@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.select-estado').forEach(function (sel) {
        sel.addEventListener('change', function () {
            const fila       = this.closest('tr');
            const campoFecha = fila.querySelector('.campo-fecha');
            const estado     = this.value;

            if (['finalizado', 'vencida', 'incompleta'].includes(estado)) {
                campoFecha.style.display = '';
                if (!campoFecha.value) {
                    campoFecha.value = new Date().toISOString().split('T')[0];
                }
            } else {
                campoFecha.style.display = 'none';
                campoFecha.value = '';
            }
        });
    });
});
</script>
@endpush