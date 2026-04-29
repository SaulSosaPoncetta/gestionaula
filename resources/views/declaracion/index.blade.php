@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Declaraciones juradas</h4>
        <p class="text-muted">Historial de declaraciones juradas de horarios.</p>
    </div>
    <div class="col-auto">
        @if(auth()->user()->hasRole('docente'))
        <a href="{{ route('declaracion.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva declaración
        </a>
        @endif
    </div>
</div>

@if($declaraciones->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay declaraciones juradas todavía.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Ciclo</th>
                    @if(auth()->user()->hasRole('director'))
                        <th>Docente</th>
                    @endif
                    <th>Estado</th>
                    <th>Presentación</th>
                    <th>Resolución</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($declaraciones as $dec)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $dec->ciclo }}</td>
                    @if(auth()->user()->hasRole('director'))
                        <td>{{ $dec->docente->name }}</td>
                    @endif
                    <td>
                        <span class="badge bg-{{ $dec->estadobadge }}">{{ ucfirst($dec->estado) }}</span>
                    </td>
                    <td>{{ $dec->fechapresentacion?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>{{ $dec->fecharesolucion?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('declaracion.show', $dec) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $declaraciones->links() }}</div>
@endif
@endsection