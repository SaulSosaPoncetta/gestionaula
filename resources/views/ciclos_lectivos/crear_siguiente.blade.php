@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar2-plus me-2"></i>Generar ciclo lectivo {{ $anioSiguiente }}</h4>
        <p class="text-muted">El ciclo lectivo {{ $cicloActual->anio }} finalizó o está por finalizar. Creá el nuevo ciclo para continuar.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ciclos_lectivos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Al activar el nuevo ciclo, todos los datos que cargues (asistencia, calificaciones, contenidos, actividades, etc.)
    quedarán asociados al ciclo <strong>{{ $anioSiguiente }}</strong> automáticamente.
    Los datos históricos del ciclo <strong>{{ $cicloActual->anio }}</strong> se mantienen intactos.
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
                    <label class="form-label fw-semibold">Año</label>
                    <input type="number" name="anio" class="form-control fw-bold"
                           value="{{ $anioSiguiente }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fechainicio" class="form-control"
                           value="{{ old('fechainicio', $anioSiguiente . '-03-01') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de finalización <span class="text-danger">*</span></label>
                    <input type="date" name="fechafin" class="form-control"
                           value="{{ old('fechafin', $anioSiguiente . '-12-15') }}" required>
                </div>
            </div>

            <input type="hidden" name="activo" value="1">

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle me-1"></i>Crear y activar ciclo lectivo {{ $anioSiguiente }}
                </button>
                <a href="{{ route('ciclos_lectivos.index') }}" class="btn btn-outline-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
