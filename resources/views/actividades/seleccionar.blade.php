@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva actividad</h4>
        <p class="text-muted">Selecciona la materia para continuar.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">
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
                        <option value="">Selecciona una materia...</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}">
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