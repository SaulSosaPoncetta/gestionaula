@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo contenido</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('contenidos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('contenidos.store') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Materia <span class="text-danger">*</span></label>
                        <select name="materia_id" class="form-select @error('materia_id') is-invalid @enderror" required>
                            <option value="">— Seleccioná una materia —</option>
                            @foreach ($materias as $materia)
                                <option value="{{ $materia->id }}"
                                    {{ old('materia_id', request('materia_id')) == $materia->id ? 'selected' : '' }}>
                                    {{ $materia->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('materia_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Número de unidad <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <input type="number" name="numerounidad"
                            class="form-control @error('numerounidad') is-invalid @enderror"
                            value="{{ old('numerounidad') }}" min="1" placeholder="Ej: 1, 2, 3...">
                        @error('numerounidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Tema <span class="text-danger">*</span></label>
                        <input type="text" name="tema" class="form-control @error('tema') is-invalid @enderror"
                            value="{{ old('tema') }}" placeholder="Tema principal de la clase" required>
                        @error('tema')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Subtemas <span class="text-muted fw-normal">(opcionales, hasta 3)</span>
                        </label>
                        @for ($i = 0; $i < 3; $i++)
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted">{{ $i + 1 }}</span>
                                <input type="text" name="subtemas[{{ $i }}]" class="form-control"
                                    value="{{ old("subtemas.$i") }}" placeholder="Subtema {{ $i + 1 }} (opcional)">
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Observación <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <textarea name="observacion" class="form-control" rows="2" placeholder="Notas adicionales...">{{ old('observacion') }}</textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Guardar
                    </button>
                    <a href="{{ route('contenidos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
