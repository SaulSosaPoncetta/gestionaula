@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva planificación</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('planificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('planificaciones.store') }}">
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
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                    <select name="materia_id" class="form-select @error('materia_id') is-invalid @enderror" required>
                        <option value="">— Seleccioná una materia —</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ old('materia_id') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ciclo lectivo <span class="text-danger">*</span></label>
                    <input type="text" name="ciclo" class="form-control @error('ciclo') is-invalid @enderror"
                           value="{{ old('ciclo', $ciclo) }}" required>
                    @error('ciclo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción general <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción general de la planificación...">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Crear planificación
                </button>
                <a href="{{ route('planificaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection