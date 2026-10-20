@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar período</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('periodos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('periodos.update', $periodo) }}">
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
                <label class="form-label fw-semibold">Denominación <span class="text-danger">*</span></label>
                <input type="text" name="denominacion"
                       class="form-control @error('denominacion') is-invalid @enderror"
                       value="{{ old('denominacion', $periodo->denominacion) }}"
                       placeholder="Ej: 1er Trimestre, 1er Cuatrimestre..."
                       required autofocus>
                @error('denominacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Actualizar
                </button>
                <a href="{{ route('periodos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
