@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar asistencia</h4>
        <p class="text-muted">
            <strong>{{ $asistencia->alumno->nombre_completo }}</strong>
            &mdash; {{ $asistencia->fecha->format('d/m/Y') }}
            &mdash; {{ $asistencia->materia?->nombre ?? '—' }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asistencia.alumno', ['alumno_id' => $asistencia->alumno_id, 'buscar' => $asistencia->alumno->apellido]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('asistencia.actualizar', $asistencia) }}"
              enctype="multipart/form-data">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info del registro --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Alumno</label>
                    <div class="fw-semibold">{{ $asistencia->alumno->nombre_completo }}</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Fecha</label>
                    <div class="fw-semibold">{{ $asistencia->fecha->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Materia</label>
                    <div class="fw-semibold">{{ $asistencia->materia?->nombre ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Curso</label>
                    <div class="fw-semibold">{{ $asistencia->curso?->nombre_completo ?? '—' }}</div>
                </div>
            </div>

            <hr>

            <div class="row g-3">
                {{-- Estado --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-select" required>
                        <option value="presente"    {{ $asistencia->estado === 'presente'    ? 'selected' : '' }}>Presente</option>
                        <option value="ausente"     {{ $asistencia->estado === 'ausente'     ? 'selected' : '' }}>Ausente</option>
                        <option value="tarde"       {{ $asistencia->estado === 'tarde'       ? 'selected' : '' }}>Tarde</option>
                        <option value="justificado" {{ $asistencia->estado === 'justificado' ? 'selected' : '' }}>Justificado</option>
                    </select>
                </div>

                {{-- Hora llegada (solo si es tarde) --}}
                <div class="col-md-3" id="campoHora"
                     style="{{ $asistencia->estado !== 'tarde' ? 'display:none' : '' }}">
                    <label class="form-label fw-semibold">Hora de llegada</label>
                    <input type="time" name="horallegada" class="form-control"
                           value="{{ $asistencia->horallegada ? substr($asistencia->horallegada, 0, 5) : '' }}">
                </div>

                {{-- Foto justificación (solo si es ausente) --}}
                <div class="col-md-5" id="campoFoto"
                     style="{{ !in_array($asistencia->estado, ['ausente']) ? 'display:none' : '' }}">
                    <label class="form-label fw-semibold">Foto justificación</label>
                    <input type="file" name="fotojustificacion" class="form-control"
                           accept="image/*,application/pdf">
                    @if($asistencia->fotojustificacion)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $asistencia->fotojustificacion) }}"
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-image me-1"></i>Ver foto actual
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Observación --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Observación</label>
                    <textarea name="observacion" class="form-control" rows="2"
                              placeholder="Opcional">{{ $asistencia->observacion }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar cambios
                </button>
                <a href="{{ route('asistencia.alumno', ['alumno_id' => $asistencia->alumno_id, 'buscar' => $asistencia->alumno->apellido]) }}"
                   class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('estado').addEventListener('change', function () {
    const estado     = this.value;
    const campoHora  = document.getElementById('campoHora');
    const campoFoto  = document.getElementById('campoFoto');

    campoHora.style.display = estado === 'tarde'   ? '' : 'none';
    campoFoto.style.display = estado === 'ausente' ? '' : 'none';

    if (estado === 'tarde' && !document.querySelector('[name=horallegada]').value) {
        const ahora = new Date();
        const hh = String(ahora.getHours()).padStart(2, '0');
        const mm = String(ahora.getMinutes()).padStart(2, '0');
        document.querySelector('[name=horallegada]').value = `${hh}:${mm}`;
    }
});
</script>
@endpush