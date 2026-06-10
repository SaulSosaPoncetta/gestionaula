@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva designación</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('designaciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('designaciones.store') }}">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Datos institucionales --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-building me-1"></i>Datos del establecimiento
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Distrito <span class="text-danger">*</span></label>
                    <input type="text" name="distrito" class="form-control"
                           value="{{ old('distrito') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de establecimiento <span class="text-danger">*</span></label>
                    <input type="text" name="tipoestablecimiento" class="form-control"
                           value="{{ old('tipoestablecimiento') }}"
                           placeholder="Ej: E.E.S.T., E.E.S., E.P.B." required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° Escuela <span class="text-danger">*</span></label>
                    <input type="text" name="numeroescuela" class="form-control"
                           value="{{ old('numeroescuela') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Secuencia</label>
                    <input type="text" name="secuencia" class="form-control"
                           value="{{ old('secuencia') }}" placeholder="Opcional">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nombre del establecimiento <span class="text-danger">*</span></label>
                    <input type="text" name="nombreestablecimiento" class="form-control"
                           value="{{ old('nombreestablecimiento') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dependencia <span class="text-danger">*</span></label>
                    <select name="dependencia_tipo" class="form-select" required>
                        @foreach(\App\Models\Designacion::DEPENDENCIA_TIPOS as $val => $label)
                            <option value="{{ $val }}" {{ old('dependencia_tipo') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dependencia (detalle)</label>
                    <input type="text" name="dependencia" class="form-control"
                           value="{{ old('dependencia') }}" placeholder="Opcional">
                </div>
            </div>
        </div>
    </div>

    {{-- Datos de la designación --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-file-earmark-person me-1"></i>Datos de la designación
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Régimen estatutario <span class="text-danger">*</span></label>
                    <input type="text" name="regimenstatutario" class="form-control"
                           value="{{ old('regimenstatutario') }}"
                           placeholder="Ej: Titular, Provisional, Suplente" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Carácter de revista <span class="text-danger">*</span></label>
                    <input type="text" name="caracterderevista" class="form-control"
                           value="{{ old('caracterderevista') }}"
                           placeholder="Ej: Titular, Suplente" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Tipo hora <span class="text-danger">*</span></label>
                    <select name="tipohora" class="form-select" required>
                        @foreach(\App\Models\Designacion::TIPOS_HORA as $val => $label)
                            <option value="{{ $val }}" {{ old('tipohora') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Cupof</label>
                    <input type="text" name="cupof" class="form-control"
                           value="{{ old('cupof') }}" placeholder="Opcional">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha desde</label>
                    <input type="date" name="fechadesde" class="form-control"
                           value="{{ old('fechadesde') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha hasta</label>
                    <input type="date" name="fechahasta" class="form-control"
                           value="{{ old('fechahasta') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha designación</label>
                    <input type="date" name="fechadesignacion" class="form-control"
                           value="{{ old('fechadesignacion') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha toma posesión</label>
                    <input type="date" name="fechatomaposecion" class="form-control"
                           value="{{ old('fechatomaposecion') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Datos de la clase --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-calendar3 me-1"></i>Datos de la clase
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre de la materia <span class="text-danger">*</span></label>
                    <input type="text" name="nombremateria" class="form-control"
                           value="{{ old('nombremateria') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Año designado <span class="text-danger">*</span></label>
                    <input type="text" name="anodesignado" class="form-control"
                           value="{{ old('anodesignado') }}" placeholder="Ej: 4to" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">División <span class="text-danger">*</span></label>
                    <input type="text" name="divisiondesignada" class="form-control"
                           value="{{ old('divisiondesignada') }}" placeholder="Ej: 1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Turno <span class="text-danger">*</span></label>
                    <select name="turnodesempeno" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach(\App\Models\Designacion::TURNOS as $val => $label)
                            <option value="{{ $val }}" {{ old('turnodesempeno') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Día de la semana <span class="text-danger">*</span></label>
                    <select name="diasemana" class="form-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach(\App\Models\Designacion::DIAS as $val => $label)
                            <option value="{{ $val }}" {{ old('diasemana') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Hora entrada <span class="text-danger">*</span></label>
                    <input type="time" name="horaentrada" class="form-control"
                           value="{{ old('horaentrada') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Hora salida <span class="text-danger">*</span></label>
                    <input type="time" name="horasalida" class="form-control"
                           value="{{ old('horasalida') }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Guardar
        </button>
        <a href="{{ route('designaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection