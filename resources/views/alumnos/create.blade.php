@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo alumno</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('alumnos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('alumnos.store') }}">
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
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido"
                           class="form-control @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido') }}" required>
                    @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">DNI</label>
                    <input type="text" name="dni"
                           class="form-control @error('dni') is-invalid @enderror"
                           value="{{ old('dni') }}" placeholder="Opcional">
                    @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha de nacimiento</label>
                    <input type="date" name="fechanacimiento" class="form-control"
                           value="{{ old('fechanacimiento') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono"
                           class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono') }}" placeholder="Ej: 2284-123456">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="Ej: alumno@mail.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Tipo de cursada <span class="text-danger">*</span>
                    </label>
                    <select name="tipocursada"
                            class="form-select @error('tipocursada') is-invalid @enderror" required>
                        @foreach(\App\Models\Alumno::TIPOSCURSADA as $valor => $label)
                            <option value="{{ $valor }}"
                                {{ old('tipocursada', 'regular') === $valor ? 'selected' : '' }}>
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
                                {{ old('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('curso_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar
                </button>
                <a href="{{ route('alumnos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection