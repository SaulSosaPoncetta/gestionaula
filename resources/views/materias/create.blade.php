@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva materia</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('materias.store') }}">
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

                <h6 class="fw-bold text-muted mb-3">Datos generales</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Año</label>
                        <select name="anio" class="form-select">
                            <option value="">—</option>
                            @foreach (['1ro', '2do', '3ro', '4to', '5to', '6to', '7mo'] as $a)
                                <option value="{{ $a }}" {{ old('anio') == $a ? 'selected' : '' }}>
                                    {{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Establecimiento</label>
                        <select name="establecimiento_id" class="form-select">
                            <option value="">—</option>
                            @foreach ($establecimientos as $est)
                                <option value="{{ $est->id }}"
                                    {{ old('establecimiento_id') == $est->id ? 'selected' : '' }}>
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
                            @foreach ($ciclos as $ciclo)
                                <option value="{{ $ciclo->id }}" {{ old('ciclo_id') == $ciclo->id ? 'selected' : '' }}>
                                    {{ $ciclo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Área de formación</label>
                        <select name="area_formacion_id" class="form-select">
                            <option value="">—</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}"
                                    {{ old('area_formacion_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Especialidad / Orientación</label>
                        <select name="especialidad_id" class="form-select">
                            <option value="">—</option>
                            @foreach ($especialidades as $esp)
                                <option value="{{ $esp->id }}"
                                    {{ old('especialidad_id') == $esp->id ? 'selected' : '' }}>
                                    {{ $esp->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo de espacio</label>
                        <select name="tipomateria" class="form-select">
                            <option value="">—</option>
                            <option value="aula" {{ old('tipomateria') == 'aula' ? 'selected' : '' }}>Aula</option>
                            <option value="taller" {{ old('tipomateria') == 'taller' ? 'selected' : '' }}>Taller</option>
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold text-muted mb-3 mt-4">Carga horaria</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de hora</label>
                        <select name="tipohora" class="form-select">
                            <option value="">—</option>
                            <option value="catedra" {{ old('tipohora') == 'catedra' ? 'selected' : '' }}>Hora cátedra
                            </option>
                            <option value="modulo" {{ old('tipohora') == 'modulo' ? 'selected' : '' }}>Módulo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Carga horaria semanal</label>
                        <input type="number" name="cargahorariasemanal" class="form-control"
                            value="{{ old('cargahorariasemanal') }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Carga horaria anual</label>
                        <input type="number" name="cargahorariaanual" id="cargahorariaanual" class="form-control"
                            value="{{ old('cargahorariaanual') }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Hs. por clase</label>
                        <input type="number" name="hsporclase" id="hsporclase" class="form-control"
                            value="{{ old('hsporclase') }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Clases anuales</label>
                        <input type="text" id="cantidadclasesanuales" class="form-control bg-light"
                            placeholder="Se calcula automáticamente" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        % límite de asistencia <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" name="porcentajelimite" class="form-control"
                            value="{{ old('porcentajelimite', 75) }}" min="1" max="100" step="0.5"
                            required>
                        <span class="input-group-text">%</span>
                    </div>
                    <div class="form-text">Porcentaje mínimo requerido de asistencia.</div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Guardar
                    </button>
                    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script>
            function calcularClases() {
                const anual = parseInt(document.getElementById('cargahorariaanual').value) || 0;
                const hsPorClase = parseInt(document.getElementById('hsporclase').value) || 0;
                const resultado = document.getElementById('cantidadclasesanuales');

                if (anual > 0 && hsPorClase > 0) {
                    resultado.value = Math.floor(anual / hsPorClase);
                } else {
                    resultado.value = '';
                }
            }

            document.getElementById('cargahorariaanual').addEventListener('input', calcularClases);
            document.getElementById('hsporclase').addEventListener('input', calcularClases);

            // Calcular al cargar si ya hay valores
            calcularClases();
        </script>
    @endpush
@endsection
