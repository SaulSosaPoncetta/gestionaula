@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journals me-2"></i>Modalidades educativas</h4>
        <p class="text-muted">Modalidades del sistema educativo provincial (Ley 13688).</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('modalidades.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva modalidad
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($modalidades->isEmpty())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay modalidades registradas.</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($modalidades as $modalidad)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $modalidad->nombre }}</td>
                    <td class="text-end pe-3">
                        @if($modalidad->user_id === auth()->id())
                            <a href="{{ route('modalidades.edit', $modalidad) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('modalidades.destroy', $modalidad) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Eliminar esta modalidad?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $modalidades->links() }}</div>
@endif
@endsection
