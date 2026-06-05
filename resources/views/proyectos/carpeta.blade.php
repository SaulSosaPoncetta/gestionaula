@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold">
            <i class="bi bi-folder2-open me-2 text-warning"></i>Carpeta de campo
        </h4>
        <p class="text-muted">
            <strong>{{ $carpeta->alumno?->nombre_completo }}</strong>
            — {{ $carpeta->proyecto?->titulo }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('proyectos.show', $carpeta->proyecto) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al proyecto
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

<div class="row g-4">

    {{-- Info de la carpeta --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark fw-semibold">
                <i class="bi bi-folder2 me-1"></i>Información
            </div>
            <div class="card-body">
                <div class="fw-bold fs-5 mb-1">{{ $carpeta->titulo }}</div>
                @if($carpeta->subtitulo)
                    <div class="text-muted mb-2">{{ $carpeta->subtitulo }}</div>
                @endif
                @if($carpeta->descripcion)
                    <div class="text-muted small">{{ $carpeta->descripcion }}</div>
                @endif
                <hr>
                <div class="text-muted small">
                    <i class="bi bi-people me-1"></i>
                    {{ $carpeta->proyecto?->curso?->nombre_completo ?? '—' }}
                </div>
                <div class="text-muted small">
                    <i class="bi bi-book me-1"></i>
                    {{ $carpeta->proyecto?->materia?->nombre ?? '—' }}
                </div>
            </div>
        </div>

        {{-- Formulario nueva entrada --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-plus-circle me-1 text-primary"></i>Agregar entrada
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('proyectos.entrada.store', $carpeta) }}"
                      enctype="multipart/form-data">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger small mb-3">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select form-select-sm" required>
                            @foreach(\App\Models\CarpetaCampoEntrada::TIPOS as $val => $label)
                                <option value="{{ $val }}" {{ old('tipo') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-sm"
                               value="{{ old('titulo') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-sm"
                                  rows="3">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control form-control-sm"
                               value="{{ old('fecha', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Archivo <span class="text-muted fw-normal">(imagen, PDF, doc)</span>
                        </label>
                        <input type="file" name="archivo" class="form-control form-control-sm"
                               accept="image/*,application/pdf,.doc,.docx">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>Agregar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Entradas de la carpeta --}}
    <div class="col-md-8">
        @if($carpeta->entradas->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Esta carpeta no tiene entradas todavía. Usá el formulario para agregar contenido.
            </div>
        @else
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-journal-richtext me-1"></i>
                Entradas ({{ $carpeta->entradas->count() }})
            </div>
            <div class="card-body p-0">
                @foreach($carpeta->entradas as $entrada)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-{{ $entrada->tipobadge }}">
                                    <i class="bi {{ $entrada->tipoinconoAttribute }} me-1"></i>
                                    {{ $entrada->tipolabel }}
                                </span>
                                <span class="fw-semibold">{{ $entrada->titulo }}</span>
                                <span class="text-muted small ms-auto">
                                    {{ $entrada->fecha->format('d/m/Y') }}
                                </span>
                            </div>
                            @if($entrada->descripcion)
                                <div class="text-muted small mb-2">{{ $entrada->descripcion }}</div>
                            @endif
                            @if($entrada->archivo)
                                <a href="{{ asset('storage/' . $entrada->archivo) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-paperclip me-1"></i>Ver archivo
                                </a>
                            @endif
                        </div>
                        <form method="POST"
                              action="{{ route('proyectos.entrada.destroy', $entrada) }}"
                              class="ms-3">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta entrada?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>
@endsection