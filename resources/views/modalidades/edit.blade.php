@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar modalidad</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('modalidades.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('modalidades.update', $modalidad) }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre', $modalidad->nombre) }}"
                       placeholder="Ej: Educación Técnico-Profesional"
                       required autofocus>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">El slug se regenera automáticamente al guardar.</div>
            </div>

            @if($modalidad->slug)
            <div class="mb-3">
                <label class="form-label fw-semibold text-muted">Slug actual</label>
                <p class="font-monospace text-secondary">{{ $modalidad->slug }}</p>
            </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Actualizar
                </button>
                <a href="{{ route('modalidades.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
