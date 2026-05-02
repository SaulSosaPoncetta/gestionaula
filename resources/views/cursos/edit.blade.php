@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar curso</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('cursos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('cursos.update', $curso) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Año</label>
                    <select name="anio" class="form-select @error('anio') is-invalid @enderror">
                        <option value="">— Seleccioná —</option>
                        @foreach($anios as $a)
                            <option value="{{ $a }}" {{ (old('anio', $curso->anio) == $a) ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                    @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">División</label>
                    <input type="text" name="division" class="form-control"
                           value="{{ old('division', $curso->division) }}" placeholder="A, B, C...">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Turno</label>
                    <select name="turno" class="form-select">
                        <option value="">—</option>
                        @foreach (['Mañana', 'Tarde', 'Noche', 'Vespertino'] as $t)
                            <option value="{{ $t }}" {{ old('turno', $curso->turno) == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nivel educativo</label>
                    <select name="nivel_id" class="form-select">
                        <option value="">—</option>
                        @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_id', $curso->nivel_id) == $nivel->id ? 'selected' : '' }}>
                                {{ $nivel->nombre }} ({{ ucfirst($nivel->tipo) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Orientación / Especialidad</label>
                    <select name="especialidad_id" class="form-select">
                        <option value="">—</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id }}" {{ old('especialidad_id', $curso->especialidad_id) == $esp->id ? 'selected' : '' }}>
                                {{ $esp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Establecimiento</label>
                    <select name="establecimiento_id" class="form-select">
                        <option value="">Sin establecimiento</option>
                        @foreach($establecimientos as $est)
                            <option value="{{ $est->id }}" {{ old('establecimiento_id', $curso->establecimiento_id) == $est->id ? 'selected' : '' }}>
                                {{ $est->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Actualizar
                </button>
                <a href="{{ route('cursos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
