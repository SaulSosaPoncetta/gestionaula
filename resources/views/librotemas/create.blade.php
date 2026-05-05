@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Registrar clase</h4>
        <p class="text-muted">Fecha: <strong>{{ now()->format('d/m/Y') }}</strong></p>
    </div>
    <div class="col-auto">
        <a href="{{ route('librotemas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($materiaActiva)
<div class="alert alert-success mb-4">
    <i class="bi bi-lightning-fill me-2"></i>
    Clase activa detectada: <strong>{{ $materiaActiva->nombre }}</strong>
    @if($cursoActivo) — {{ $cursoActivo->nombre_completo }} @endif
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('librotemas.store') }}" id="formLibro">
            @csrf

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
                {{-- Materia --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">Selecciona...</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}"
                                {{ ($materiaId == $m->id) ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Curso --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Selecciona...</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id }}"
                                {{ ($cursoId == $c->id) ? 'selected' : '' }}>
                                {{ $c->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Número de clase --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° de clase <span class="text-danger">*</span></label>
                    <input type="number" name="numeroclase" class="form-control"
                           value="{{ old('numeroclase', $siguienteClase) }}" min="1" required>
                </div>

                {{-- Número de unidad --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° de unidad</label>
                    <input type="number" name="numerounidad" class="form-control"
                           value="{{ old('numerounidad') }}" min="1"
                           placeholder="Opcional">
                </div>

                {{-- Tipo de clase --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de clase</label>
                    <select name="tipoclase_id" class="form-select">
                        <option value="">— Sin especificar —</option>
                        @foreach($tiposclase as $tipo)
                            <option value="{{ $tipo->id }}"
                                {{ old('tipoclase_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Contenido --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contenido desarrollado</label>
                    <select name="contenido_id" id="contenido_id" class="form-select">
                        <option value="">— Sin seleccionar —</option>
                        @foreach($contenidos as $cont)
                            <option value="{{ $cont->id }}"
                                {{ old('contenido_id') == $cont->id ? 'selected' : '' }}>
                                {{ $cont->tema }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actividad --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Actividad desarrollada</label>
                    <select name="actividad_id" id="actividad_id" class="form-select">
                        <option value="">— Sin seleccionar —</option>
                        @foreach($actividades as $act)
                            <option value="{{ $act->id }}"
                                {{ old('actividad_id') == $act->id ? 'selected' : '' }}>
                                {{ $act->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Observaciones --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <textarea name="observacion" class="form-control" rows="3"
                              placeholder="Observaciones opcionales...">{{ old('observacion') }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar
                </button>
                <a href="{{ route('librotemas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Al cambiar materia o curso, recargar para actualizar contenidos y actividades
document.getElementById('materia_id').addEventListener('change', function () {
    const cursoId = document.getElementById('curso_id').value;
    window.location.href = `/librotemas/crear?materia_id=${this.value}&curso_id=${cursoId}`;
});

document.getElementById('curso_id').addEventListener('change', function () {
    const materiaId = document.getElementById('materia_id').value;
    window.location.href = `/librotemas/crear?materia_id=${materiaId}&curso_id=${this.value}`;
});
</script>
@endpush