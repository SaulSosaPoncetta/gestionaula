@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-envelope-open me-2"></i>{{ $mensaje->asunto }}</h4>
        <p class="text-muted">
            Enviado por <strong>{{ $mensaje->remitente->name }}</strong>
            el {{ $mensaje->created_at->format('d/m/Y') }} a las {{ $mensaje->created_at->format('H:i') }}
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('comunicacion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <p class="text-muted small mb-1">Tipo</p>
                @php $badges = ['general' => 'secondary', 'curso' => 'primary', 'alumno' => 'success']; @endphp
                <span class="badge bg-{{ $badges[$mensaje->tipo] }} fs-6">{{ ucfirst($mensaje->tipo) }}</span>
            </div>
            <div class="col-md-4">
                <p class="text-muted small mb-1">Destinatario</p>
                <p class="fw-semibold mb-0">{{ $mensaje->destinatario }}</p>
            </div>
            @if($mensaje->curso)
            <div class="col-md-4">
                <p class="text-muted small mb-1">Curso</p>
                <p class="fw-semibold mb-0">{{ $mensaje->curso->nombre_completo }}</p>
            </div>
            @endif
            @if($mensaje->alumno)
            <div class="col-md-4">
                <p class="text-muted small mb-1">Alumno</p>
                <p class="fw-semibold mb-0">{{ $mensaje->alumno->nombre_completo }}</p>
            </div>
            @endif
        </div>

        <hr>

        <div class="mt-3" style="white-space: pre-wrap;">{{ $mensaje->cuerpo }}</div>
    </div>
</div>

<form method="POST" action="{{ route('comunicacion.destroy', $mensaje) }}">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-outline-danger"
            onclick="return confirm('¿Eliminar este mensaje?')">
        <i class="bi bi-trash me-1"></i>Eliminar mensaje
    </button>
</form>
@endsection