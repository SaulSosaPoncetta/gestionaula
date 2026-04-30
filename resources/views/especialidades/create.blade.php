@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva especialidad</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('especialidades.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('especialidades.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" placeholder="Ej: Electrónica, Informática..." required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Guardar</button>
                <a href="{{ route('especialidades.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection