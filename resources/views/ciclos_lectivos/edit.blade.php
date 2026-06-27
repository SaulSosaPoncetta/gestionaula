@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar ciclo lectivo {{ $ciclo->anio }}</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('ciclos_lectivos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('ciclos_lectivos.update', $ciclo) }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
                    <input type="number" name="anio" class="form-control"
                           value="{{ old('anio', $ciclo->anio) }}"
                           min="2000" max="2100" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fechainicio" class="form-control"
                           value="{{ old('fechainicio', $ciclo->fechainicio->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de finalización <span class="text-danger">*</span></label>
                    <input type="date" name="fechafin" class="form-control"
                           value="{{ old('fechafin', $ciclo->fechafin->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="activo" id="activo"
                               class="form-check-input" value="1"
                               {{ old('activo', $ciclo->activo) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="activo">
                            Ciclo activo
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Actualizar
                </button>
                <a href="{{ route('ciclos_lectivos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
