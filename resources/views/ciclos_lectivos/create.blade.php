@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo ciclo lectivo</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('ciclos_lectivos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('ciclos_lectivos.store') }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
                    <input type="number" name="anio" class="form-control"
                           value="{{ old('anio', $anioSugerido) }}"
                           min="2000" max="2100" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fechainicio" class="form-control"
                           value="{{ old('fechainicio', $anioSugerido . '-03-01') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de finalización <span class="text-danger">*</span></label>
                    <input type="date" name="fechafin" class="form-control"
                           value="{{ old('fechafin', $anioSugerido . '-12-15') }}" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="activo" id="activo"
                               class="form-check-input" value="1"
                               {{ old('activo', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="activo">
                            Activar como ciclo lectivo actual
                        </label>
                        <div class="form-text">Al activar, el ciclo anterior quedará inactivo.</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Guardar
                </button>
                <a href="{{ route('ciclos_lectivos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
