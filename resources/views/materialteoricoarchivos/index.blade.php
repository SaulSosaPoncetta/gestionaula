@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-file-earmark-pdf me-2"></i>Material teórico</h4>
        <p class="text-muted">Archivos PDF de marco teórico para los trabajos prácticos.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('materialteoricoarchivos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Subir material
        </a>
    </div>
</div>

@if($archivos->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay material teórico cargado.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Título</th>
                    <th>Práctico asociado</th>
                    <th>Descripción</th>
                    <th>Archivo</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($archivos as $archivo)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $archivo->titulo }}</td>
                    <td>{{ $archivo->tarea?->titulo ?? '—' }}</td>
                    <td class="text-muted small">{{ $archivo->descripcion ?? '—' }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $archivo->ruta) }}"
                           target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-pdf me-1"></i>Ver PDF
                        </a>
                    </td>
                    <td class="text-muted small">
                        {{ $archivo->created_at->format('d/m/Y') }}
                    </td>
                    <td class="text-end pe-3">
                        <form method="POST"
                              action="{{ route('materialteoricoarchivos.destroy', $archivo) }}"
                              class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este archivo?')">
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
<div class="mt-3">{{ $archivos->links() }}</div>
@endif
@endsection