@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-building me-2"></i>{{ $establecimiento->nombre }}</h4>
        <p class="text-muted">
            <span class="badge bg-{{ $establecimiento->modalidad === 'tecnico' ? 'warning' : 'info' }} me-1">
                {{ $establecimiento->modalidadlabel }}
            </span>
            <span class="badge bg-secondary">{{ $establecimiento->nivel->nombre }}</span>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('establecimientos.edit', $establecimiento) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('establecimientos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle me-1"></i>Información
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">CUE</dt>
                    <dd class="col-7">{{ $establecimiento->cue ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Modalidad</dt>
                    <dd class="col-7">{{ $establecimiento->modalidadlabel }}</dd>
                    <dt class="col-5 text-muted">Nivel</dt>
                    <dd class="col-7">{{ $establecimiento->nivel->nombre }}</dd>
                    <dt class="col-5 text-muted">Tipo</dt>
                    <dd class="col-7">{{ ucfirst($establecimiento->nivel->tipo) }}</dd>
                    <dt class="col-5 text-muted">Dirección</dt>
                    <dd class="col-7">{{ $establecimiento->direccion ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Localidad</dt>
                    <dd class="col-7">{{ $establecimiento->localidad ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Provincia</dt>
                    <dd class="col-7">{{ $establecimiento->provincia ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Teléfono</dt>
                    <dd class="col-7">{{ $establecimiento->telefono ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $establecimiento->email ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-journal-text me-1"></i>Cursos ({{ $establecimiento->cursos->count() }})
            </div>
            <div class="card-body p-0">
                @if($establecimiento->cursos->isEmpty())
                    <p class="text-muted p-3 mb-0">Sin cursos asignados.</p>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($establecimiento->cursos as $curso)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $curso->nombre_completo }}
                        <span class="badge bg-primary">{{ $curso->alumnos->count() }} alumnos</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-people me-1"></i>Docentes ({{ $establecimiento->docentes->count() }})
            </div>
            <div class="card-body p-0">
                @if($establecimiento->docentes->isEmpty())
                    <p class="text-muted p-3 mb-0">Sin docentes asignados.</p>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($establecimiento->docentes as $docente)
                    <li class="list-group-item">{{ $docente->name }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection