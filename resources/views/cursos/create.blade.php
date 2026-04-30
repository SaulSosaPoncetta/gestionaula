@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo curso</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('cursos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('cursos.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}" placeholder="Ej: 1ro, 2do, 3ro..." required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">División</label>
                        <input type="text" name="division" class="form-control" value="{{ old('division') }}"
                            placeholder="Ej: A, B, C">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Turno</label>
                        <select name="turno" class="form-select">
                            <option value="">—</option>
                            @foreach (['Mañana', 'Tarde', 'Noche', 'Vespertino'] as $t)
                                <option value="{{ $t }}" {{ old('turno') == $t ? 'selected' : '' }}>
                                    {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Establecimiento</label>
                        <select name="establecimiento_id" class="form-select">
                            <option value="">Sin establecimiento</option>
                            @foreach ($establecimientos as $est)
                                <option value="{{ $est->id }}"
                                    {{ old('establecimiento_id') == $est->id ? 'selected' : '' }}>
                                    {{ $est->nombre }} — {{ $est->modalidadlabel }} ({{ ucfirst($est->nivel->tipo) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Guardar</button>
                    <a href="{{ route('cursos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
