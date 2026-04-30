@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo establecimiento</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('establecimientos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('establecimientos.store') }}">
            @csrf
            <h6 class="fw-bold mb-3 text-muted">Datos institucionales</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">CUE</label>
                    <input type="text" name="cue" class="form-control @error('cue') is-invalid @enderror"
                           value="{{ old('cue') }}" placeholder="Código único del establecimiento">
                    @error('cue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Modalidad <span class="text-danger">*</span></label>
                    <select name="modalidad" class="form-select @error('modalidad') is-invalid @enderror" required>
                        <option value="">Seleccioná...</option>
                        @foreach($modalidades as $m)
                            <option value="{{ $m }}" {{ old('modalidad') == $m ? 'selected' : '' }}>
                                {{ $m === 'comun' ? 'Común' : 'Técnico' }}
                            </option>
                        @endforeach
                    </select>
                    @error('modalidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nivel educativo <span class="text-danger">*</span></label>
                    <select name="nivel_id" class="form-select @error('nivel_id') is-invalid @enderror" required>
                        <option value="">Seleccioná...</option>
                        @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_id') == $nivel->id ? 'selected' : '' }}>
                                {{ $nivel->nombre }} ({{ ucfirst($nivel->tipo) }})
                            </option>
                        @endforeach
                    </select>
                    @error('nivel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold mb-3 mt-4 text-muted">Datos de contacto</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" name="direccion" class="form-control"
                           value="{{ old('direccion') }}" placeholder="Calle y número">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Localidad</label>
                    <input type="text" name="localidad" class="form-control"
                           value="{{ old('localidad') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Provincia</label>
                    <input type="text" name="provincia" class="form-control"
                           value="{{ old('provincia') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="{{ old('telefono') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Guardar</button>
                <a href="{{ route('establecimientos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection