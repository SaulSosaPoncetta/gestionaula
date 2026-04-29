@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-chat-dots me-2"></i>Comunicación</h4>
        <p class="text-muted">Mensajes enviados a alumnos, cursos y familias.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('comunicacion.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo mensaje
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('comunicacion.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Buscar por asunto o destinatario..."
                           value="{{ request('buscar') }}">
                </div>
                <div class="col-md-4">
                    <select name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="general" {{ request('tipo') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="curso" {{ request('tipo') == 'curso' ? 'selected' : '' }}>Por curso</option>
                        <option value="alumno" {{ request('tipo') == 'alumno' ? 'selected' : '' }}>Por alumno</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($mensajes->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay mensajes registrados.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Asunto</th>
                    <th>Destinatario</th>
                    <th>Tipo</th>
                    <th>Remitente</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($mensajes as $mensaje)
                @php
                    $badges = ['general' => 'secondary', 'curso' => 'primary', 'alumno' => 'success'];
                @endphp
                <tr>
                    <td class="ps-4 fw-semibold">{{ $mensaje->asunto }}</td>
                    <td>{{ $mensaje->destinatario }}</td>
                    <td><span class="badge bg-{{ $badges[$mensaje->tipo] }}">{{ ucfirst($mensaje->tipo) }}</span></td>
                    <td>{{ $mensaje->remitente->name }}</td>
                    <td>{{ $mensaje->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-end pe-3 d-flex gap-1 justify-content-end">
                        <a href="{{ route('comunicacion.show', $mensaje) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('comunicacion.destroy', $mensaje) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este mensaje?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $mensajes->links() }}</div>
@endif
@endsection