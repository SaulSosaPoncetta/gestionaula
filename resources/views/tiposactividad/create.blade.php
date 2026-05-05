@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo tipo de actividad</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('tiposactividad.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('tiposactividad.store') }}">
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
                    <label class="form-label fw-semibold">
                        Denominación <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="denominacion"
                           class="form-control @error('denominacion') is-invalid @enderror"
                           value="{{ old('denominacion') }}"
                           placeholder="Ej: Trabajo grupal" required>
                    @error('denominacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar
                </button>
                <a href="{{ route('tiposactividad.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection