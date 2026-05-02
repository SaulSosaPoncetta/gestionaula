@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-upload me-2"></i>Subir material teórico</h4>
        <p class="text-muted">Podés subir hasta 3 archivos PDF por práctico.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('materialteoricoarchivos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('materialteoricoarchivos.store') }}"
              enctype="multipart/form-data">
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

            @if(session('error'))
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">
                        Título <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="titulo"
                           class="form-control @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo') }}"
                           placeholder="Ej: Marco teórico - TP1 Circuitos eléctricos"
                           required>
                    @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Práctico asociado</label>
                    <select name="tarea_id" class="form-select">
                        <option value="">— Sin asociar —</option>
                        @foreach($tareas as $tarea)
                            <option value="{{ $tarea->id }}"
                                {{ old('tarea_id') == $tarea->id ? 'selected' : '' }}>
                                {{ $tarea->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <input type="text" name="descripcion" class="form-control"
                           value="{{ old('descripcion') }}"
                           placeholder="Descripción breve del contenido (opcional)">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Archivos PDF <span class="text-danger">*</span>
                        <span class="text-muted fw-normal">(máximo 3, solo PDF, hasta 10MB cada uno)</span>
                    </label>
                    <input type="file" name="archivos[]"
                           class="form-control @error('archivos') is-invalid @enderror"
                           accept="application/pdf"
                           multiple required>
                    @error('archivos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @error('archivos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Seleccioná hasta 3 archivos PDF a la vez manteniendo Ctrl (o Cmd en Mac).
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload me-1"></i>Subir material
                </button>
                <a href="{{ route('materialteoricoarchivos.index') }}"
                   class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection