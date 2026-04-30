@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar nivel educativo</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('niveles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('niveles.update', $nivel) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $nivel->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="">Seleccioná...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo', $nivel->tipo) == $tipo ? 'selected' : '' }}>
                                {{ ucfirst($tipo) }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Actualizar</button>
                <a href="{{ route('niveles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection