@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar calificación</h4>
        <p class="text-muted">
            <strong>{{ $estado->alumno->nombre_completo }}</strong>
            &mdash;
            {{ $estado->actividad?->titulo }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('calificaractividad.calificadas', ['materia_id' => $estado->actividad->materia_id, 'curso_id' => $estado->actividad->curso_id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        {{-- Info de la actividad (readonly) --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="text-muted small">Materia</div>
                <div class="fw-semibold">{{ $estado->actividad?->materia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Curso</div>
                <div class="fw-semibold">{{ $estado->actividad?->curso?->nombre_completo ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Tema</div>
                <div class="fw-semibold">{{ $estado->actividad?->tema ?? '—' }}</div>
            </div>
        </div>

        <hr>

        <form method="POST" action="{{ route('calificaractividad.update', $estado) }}">
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

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-select" required>
                        <option value="finalizado" {{ $estado->estado === 'finalizado' ? 'selected' : '' }}>
                            Finalizado
                        </option>
                        <option value="vencida" {{ $estado->estado === 'vencida' ? 'selected' : '' }}>
                            Entrega vencida
                        </option>
                        <option value="incompleta" {{ $estado->estado === 'incompleta' ? 'selected' : '' }}>
                            Entrega incompleta
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="fechaestado" class="form-control"
                           value="{{ $estado->fechaestado?->format('Y-m-d') ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nota</label>
                    <input type="number" name="nota" class="form-control"
                           value="{{ $estado->nota }}"
                           min="0" max="10" step="0.25"
                           placeholder="0 - 10">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Observación</label>
                    <textarea name="observacion" class="form-control" rows="2">{{ $estado->observacion }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Actualizar
                </button>
                <a href="{{ route('calificaractividad.calificadas', ['materia_id' => $estado->actividad->materia_id, 'curso_id' => $estado->actividad->curso_id]) }}"
                   class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
