@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo evento</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('calendarioescolar.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('calendarioescolar.store') }}">
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
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Fecha <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fecha"
                           class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', date('Y-m-d')) }}" required>
                    @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Denominación <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="denominacion"
                           class="form-control @error('denominacion') is-invalid @enderror"
                           value="{{ old('denominacion') }}"
                           placeholder="Ej: Inicio de clases, Feriado nacional..." required>
                    @error('denominacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de período</label>
                    <select name="periodo_id" class="form-select">
                        <option value="">— Sin período —</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}"
                                {{ old('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                {{ $periodo->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">¿Es feriado?</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox"
                               name="esferiado" id="esferiado" value="1"
                               {{ old('esferiado') ? 'checked' : '' }}>
                        <label class="form-check-label" for="esferiado">
                            Sí, es feriado
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha inicio</label>
                    <input type="date" name="fechainicio"
                           class="form-control @error('fechainicio') is-invalid @enderror"
                           value="{{ old('fechainicio') }}">
                    @error('fechainicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha fin</label>
                    <input type="date" name="fechafin"
                           class="form-control @error('fechafin') is-invalid @enderror"
                           value="{{ old('fechafin') }}">
                    @error('fechafin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar
                </button>
                <a href="{{ route('calendarioescolar.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection