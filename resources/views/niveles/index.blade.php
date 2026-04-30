@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-diagram-3 me-2"></i>Niveles educativos</h4>
        <p class="text-muted">Gestión de niveles del sistema educativo.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('niveles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nuevo nivel
        </a>
    </div>
</div>

@if($niveles->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay niveles registrados.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>Tipo</th>
                    <th class="text-center">Establecimientos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($niveles as $nivel)
                @php
                    $colores = ['inicial' => 'success', 'primario' => 'primary', 'secundario' => 'warning', 'terciario' => 'danger'];
                @endphp
                <tr>
                    <td class="ps-4 fw-semibold">{{ $nivel->nombre }}</td>
                    <td><span class="badge bg-{{ $colores[$nivel->tipo] }}">{{ ucfirst($nivel->tipo) }}</span></td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $nivel->establecimientos_count }}</span></td>
                    <td class="text-end pe-3">
                        <a href="{{ route('niveles.edit', $nivel) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('niveles.destroy', $nivel) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este nivel?')">
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
<div class="mt-3">{{ $niveles->links() }}</div>
@endif
@endsection