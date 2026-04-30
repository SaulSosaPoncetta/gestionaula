@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar materia</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('materias.update', $materia) }}">
            @csrf @method('PUT')
            <h6 class="fw-bold text-muted mb-3">Datos generales</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $materia->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Año</label>
                    <select name="anio" class="form-select">
                        <option value="">—</option>
                        @foreach(['1ro', '2do', '3ro', '4to', '5to', '6to', '7mo'] as $a)
                            <option value="{{ $a }}" {{ old('anio', $materia->anio) == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Establecimiento</label>
                    <select name="establecimiento_id" class="form-select">
                        <option value="">—</option>
                        @foreach($establecimientos as $est)
                            <option value="{{ $est->id }}" {{ old('establecimiento_id', $materia->establecimiento_id) == $est->id ? 'selected' : '' }}>
                                {{ $est->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-muted mb-3 mt-4">Clasificación</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ciclo</label>
                    <select name="ciclo_id" class="form-select">
                        <option value="">—</option>
                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->id }}" {{ old('ciclo_id', $materia->ciclo_id) == $ciclo->id ? 'selected' : '' }}>
                                {{ $ciclo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Área de formación</label>
                    <select name="area_formacion_id" class="form-select">
                        <option value="">—</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('area_formacion_id', $materia->area_formacion_id) == $area->id ? 'selected' : '' }}>
                                {{ $area->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Especialidad / Orientación</label>
                    <select name="especialidad_id" class="form-select">
                        <option value="">—</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id }}" {{ old('especialidad_id', $materia->especialidad_id) == $esp->id ? 'selected' : '' }}>
                                {{ $esp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de materia</label>
                    <select name="tipomateria" class="form-select">
                        <option value="">—</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipomateria', $materia->tipomateria) == $tipo ? 'selected' : '' }}>
                                {{ \App\Models\Materia::TIPOLABELS[$tipo] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-muted mb-3 mt-4">Carga horaria</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de hora</label>
                    <select name="tipohora" class="form-select">
                        <option value="">—</option>
                        <option value="catedra" {{ old('tipohora', $materia->tipohora) == 'catedra' ? 'selected' : '' }}>Hora cátedra</option>
                        <option value="modulo" {{ old('tipohora', $materia->tipohora) == 'modulo' ? 'selected' : '' }}>Módulo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Cantidad de horas</label>
                    <input type="number" name="cantidadhoras" class="form-control"
                           value="{{ old('cantidadhoras', $materia->cantidadhoras) }}" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Carga horaria semanal</label>
                    <input type="number" name="cargahorariasemanal" class="form-control"
                           value="{{ old('cargahorariasemanal', $materia->cargahorariasemanal) }}" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Carga horaria anual</label>
                    <input type="number" name="cargahorariaanual" class="form-control"
                           value="{{ old('cargahorariaanual', $materia->cargahorariaanual) }}" min="1">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Actualizar</button>
                <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection