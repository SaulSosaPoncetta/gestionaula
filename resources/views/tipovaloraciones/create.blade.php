@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva valoración</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('tipovaloraciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('tipovaloraciones.store') }}">
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
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Denominación <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="denominacion"
                           class="form-control @error('denominacion') is-invalid @enderror"
                           value="{{ old('denominacion') }}"
                           placeholder="Ej: Sobresaliente, Aprobado, Insuficiente..." required>
                    @error('denominacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Nota límite inferior <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="notainferior"
                           class="form-control @error('notainferior') is-invalid @enderror"
                           value="{{ old('notainferior') }}"
                           min="0" max="10" step="0.25"
                           placeholder="Ej: 0.00" required>
                    @error('notainferior')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Nota límite superior <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="notasuperior"
                           class="form-control @error('notasuperior') is-invalid @enderror"
                           value="{{ old('notasuperior') }}"
                           min="0" max="10" step="0.25"
                           placeholder="Ej: 10.00" required>
                    @error('notasuperior')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Vista previa</label>
                    <div class="form-control bg-light text-center fw-semibold" id="preview">
                        — — —
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar
                </button>
                <a href="{{ route('tipovaloraciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function actualizarPreview() {
    const inf  = parseFloat(document.querySelector('[name=notainferior]').value) || 0;
    const sup  = parseFloat(document.querySelector('[name=notasuperior]').value) || 0;
    const den  = document.querySelector('[name=denominacion]').value || '—';
    const prev = document.getElementById('preview');
    prev.textContent = `${den}: ${inf.toFixed(2)} — ${sup.toFixed(2)}`;
}

document.querySelector('[name=denominacion]').addEventListener('input', actualizarPreview);
document.querySelector('[name=notainferior]').addEventListener('input', actualizarPreview);
document.querySelector('[name=notasuperior]').addEventListener('input', actualizarPreview);
</script>
@endpush