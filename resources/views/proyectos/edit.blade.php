@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar proyecto</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('proyectos.update', $proyecto) }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control"
                           value="{{ old('titulo', $proyecto->titulo) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Establecimiento presentación</label>
                    <select name="establecimiento_id" class="form-select">
                        <option value="">— Sin establecimiento —</option>
                        @foreach($establecimientos as $e)
                            <option value="{{ $e->id }}"
                                {{ old('establecimiento_id', $proyecto->establecimiento_id) == $e->id ? 'selected' : '' }}>
                                {{ $e->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        @foreach(\App\Models\Proyecto::ESTADOS as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('estado', $proyecto->estado) == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="fecha" class="form-control"
                           value="{{ old('fecha', $proyecto->fecha?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Hora</label>
                    <input type="time" name="hora" class="form-control"
                           value="{{ old('hora', substr($proyecto->hora ?? '', 0, 5)) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de presentación</label>
                    <input type="date" name="fechapresentacion" class="form-control"
                           value="{{ old('fechapresentacion', $proyecto->fechapresentacion?->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $proyecto->descripcion) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $proyecto->observaciones) }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Actualizar
                </button>
                <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection