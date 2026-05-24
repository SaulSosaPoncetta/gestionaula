@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar alumno</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('alumnos.index', array_filter([
            'curso_id'    => request('filtro_curso_id'),
            'tipocursada' => request('filtro_tipocursada'),
            'buscar'      => request('filtro_buscar'),
        ])) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('alumnos.update', $alumno) }}">
            @csrf @method('PUT')

            <input type="hidden" name="filtro_curso_id"    value="{{ request('filtro_curso_id') }}">
            <input type="hidden" name="filtro_tipocursada" value="{{ request('filtro_tipocursada') }}">
            <input type="hidden" name="filtro_buscar"      value="{{ request('filtro_buscar') }}">

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Código de alumno (solo lectura) --}}
            <div class="alert alert-light border mb-4 d-flex align-items-center gap-3">
                <i class="bi bi-qr-code fs-3 text-primary"></i>
                <div>
                    <div class="text-muted small">Código de alumno</div>
                    <div class="fw-bold fs-4 font-monospace">{{ $alumno->codigo }}</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido"
                           class="form-control @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido', $alumno->apellido) }}" required>
                    @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $alumno->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha de nacimiento</label>
                    <input type="date" name="fechanacimiento" class="form-control"
                           value="{{ old('fechanacimiento', $alumno->fechanacimiento?->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Tipo de cursada <span class="text-danger">*</span>
                    </label>
                    <select name="tipocursada"
                            class="form-select @error('tipocursada') is-invalid @enderror" required>
                        @foreach(\App\Models\Alumno::TIPOSCURSADA as $valor => $label)
                            <option value="{{ $valor }}"
                                {{ old('tipocursada', $alumno->tipocursada) === $valor ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipocursada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                    <select name="curso_id"
                            class="form-select @error('curso_id') is-invalid @enderror" required>
                        <option value="">Seleccioná...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                {{ old('curso_id', $alumno->curso_id) == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('curso_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Actualizar
                </button>
                <a href="{{ route('alumnos.index', array_filter([
                    'curso_id'    => request('filtro_curso_id'),
                    'tipocursada' => request('filtro_tipocursada'),
                    'buscar'      => request('filtro_buscar'),
                ])) }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection