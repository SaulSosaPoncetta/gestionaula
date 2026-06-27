@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva actividad</h4>
        <p class="text-muted">Seleccioná la materia para continuar.</p>
        @if($materiaActiva)
        <div class="mt-1">
            <span class="badge bg-success">
                <i class="bi bi-play-circle me-1"></i>Clase activa detectada
            </span>
        </div>
        @endif
    </div>
    <div class="col-auto">
        <a href="{{ route('actividades.index', $materiaActiva ? ['materia_id' => $materiaActiva] : []) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('actividades.create') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">
                        <span class="badge bg-primary me-1">1</span>Materia
                    </label>
                    <select name="materia_id" class="form-select" required>
                        <option value="">Seleccioná una materia...</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ ($materiaActiva ?? request('materia_id')) == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle me-1"></i>Siguiente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection