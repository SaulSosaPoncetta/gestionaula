@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-person-check me-2"></i>Registrar asistencia</h4>
        <p class="text-muted">
            <strong>{{ $curso->nombre_completo }}</strong>
            @if($materia) — {{ $materia->nombre }} @endif
            &mdash; {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.accion', ['curso_id' => $curso->id, 'materia_id' => $materia?->id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($curso->alumnos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>Este curso no tiene alumnos registrados.
    </div>
@else

{{-- Resumen del día --}}
@php
    $presentesHoy    = $asistencias->where('estado', 'presente')->count();
    $ausentesHoy     = $asistencias->where('estado', 'ausente')->count();
    $justificadosHoy = $asistencias->where('estado', 'justificado')->count();
    $tardeHoy        = $asistencias->where('estado', 'tarde')->count();
    $totalAlumnos    = $curso->alumnos->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-success">{{ $presentesHoy }}</div>
                <div class="text-muted small">Presentes hoy</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-danger">{{ $ausentesHoy }}</div>
                <div class="text-muted small">Ausentes hoy</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-info">{{ $justificadosHoy }}</div>
                <div class="text-muted small">Justificados hoy</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-3 fw-bold text-warning">{{ $tardeHoy }}</div>
                <div class="text-muted small">Tarde hoy</div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('asistencia.guardar') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="curso_id"   value="{{ $curso->id }}">
    <input type="hidden" name="materia_id" value="{{ $materia?->id }}">
    <input type="hidden" name="fecha"      value="{{ $fecha }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="tablaasistencia">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="min-width:180px">Alumno</th>
                        <th class="text-center"><span class="badge bg-success">Presente</span></th>
                        <th class="text-center"><span class="badge bg-danger">Ausente</span></th>
                        <th class="text-center"><span class="badge bg-warning text-dark">Tarde</span></th>
                        <th style="min-width:160px">Hora llegada</th>
                        <th style="min-width:200px">Justificación (foto)</th>
                        <th style="min-width:160px">Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($curso->alumnos->sortBy('apellido') as $alumno)
                    @php
                        $reg          = $asistencias[$alumno->id] ?? null;
                        $estadoActual = $reg?->estado ?? 'presente';
                    @endphp
                    <tr class="fila-alumno" data-alumno="{{ $alumno->id }}">
                        <td class="ps-4 fw-semibold">{{ $alumno->nombre_completo }}</td>

                        <td class="text-center">
                            <input class="form-check-input radio-estado fs-5" type="radio"
                                   name="asistencias[{{ $alumno->id }}][estado]"
                                   value="presente"
                                   {{ $estadoActual === 'presente' ? 'checked' : '' }} required>
                        </td>

                        <td class="text-center">
                            <input class="form-check-input radio-estado fs-5" type="radio"
                                   name="asistencias[{{ $alumno->id }}][estado]"
                                   value="ausente"
                                   {{ $estadoActual === 'ausente' ? 'checked' : '' }}>
                        </td>

                        <td class="text-center">
                            <input class="form-check-input radio-estado fs-5" type="radio"
                                   name="asistencias[{{ $alumno->id }}][estado]"
                                   value="tarde"
                                   {{ $estadoActual === 'tarde' ? 'checked' : '' }}>
                        </td>

                        <td>
                            <input type="time"
                                   class="form-control form-control-sm campo-hora"
                                   name="asistencias[{{ $alumno->id }}][horallegada]"
                                   value="{{ $reg?->horallegada ?? '' }}"
                                   {{ $estadoActual !== 'tarde' ? 'style=display:none' : '' }}>
                        </td>

                        <td>
                            <div class="campo-foto"
                                 {{ !in_array($estadoActual, ['ausente','justificado']) ? 'style=display:none' : '' }}>
                                <input type="file"
                                       class="form-control form-control-sm"
                                       name="fotos[{{ $alumno->id }}]"
                                       accept="image/*,application/pdf">
                                @if($reg?->fotojustificacion)
                                    <small class="text-success d-block mt-1">
                                        <i class="bi bi-check-circle me-1"></i>Ya tiene foto
                                    </small>
                                @endif
                            </div>
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   name="asistencias[{{ $alumno->id }}][observacion]"
                                   value="{{ $reg?->observacion ?? '' }}"
                                   placeholder="Opcional">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Guardar asistencia
        </button>
        <a href="{{ route('asistencia.accion', ['curso_id' => $curso->id, 'materia_id' => $materia?->id]) }}"
           class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.fila-alumno').forEach(function (fila) {
        const radios    = fila.querySelectorAll('.radio-estado');
        const campoHora = fila.querySelector('.campo-hora');
        const campoFoto = fila.querySelector('.campo-foto');

        function actualizarCampos(estado) {
            if (estado === 'tarde') {
                campoHora.style.display = '';
                if (!campoHora.value) {
                    const ahora = new Date();
                    const hh = String(ahora.getHours()).padStart(2, '0');
                    const mm = String(ahora.getMinutes()).padStart(2, '0');
                    campoHora.value = `${hh}:${mm}`;
                }
            } else {
                campoHora.style.display = 'none';
                campoHora.value = '';
            }

            if (campoFoto) {
                if (estado === 'ausente') {
                    campoFoto.style.display = '';
                } else {
                    campoFoto.style.display = 'none';
                    const fileInput = campoFoto.querySelector('input[type=file]');
                    if (fileInput) fileInput.value = '';
                }
            }
        }

        const checkedRadio = fila.querySelector('.radio-estado:checked');
        if (checkedRadio) actualizarCampos(checkedRadio.value);

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                actualizarCampos(this.value);
            });
        });
    });
});
</script>
@endpush