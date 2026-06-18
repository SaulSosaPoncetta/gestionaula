@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold">
            <i class="bi bi-file-earmark-text me-2"></i>
            Declaración jurada — Ciclo {{ $declaracion->ciclo }}
        </h4>
        <p class="text-muted mb-0">
            Docente: <strong>{{ $declaracion->docente->name }}</strong>
            &mdash;
            Fecha: <strong>{{ $declaracion->fechadeclaracion ? $declaracion->fechadeclaracion->format('d/m/Y') : '—' }}</strong>
            &mdash;
            <span class="badge bg-{{ $declaracion->estadobadge }}">
                {{ ucfirst($declaracion->estado) }}
            </span>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        @if($declaracion->estado === 'borrador')
        <a href="{{ route('declaracion.edit', $declaracion) }}" class="btn btn-secondary">
            <i class="bi bi-pencil text-white me-1"></i>Editar
        </a>
        <form method="POST" action="{{ route('declaracion.presentar', $declaracion) }}">
            @csrf
            <button type="submit" class="btn btn-primary"
                    onclick="return confirm('¿Presentar la declaración? No podrás editarla.')">
                <i class="bi bi-send me-1"></i>Presentar
            </button>
        </form>
        @endif
        <a href="{{ route('declaracion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@foreach($dias as $dia)
@if($itemspordia[$dia]->isNotEmpty())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-calendar-day me-1"></i>{{ ucfirst($dia) }}
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Hora inicio</th>
                    <th>Hora fin</th>
                    <th>Establecimiento</th>
                    <th>Curso</th>
                    <th>Materia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemspordia[$dia] as $item)
                <tr>
                    <td class="ps-4 fw-semibold">
                        {{ \Carbon\Carbon::parse($item->horainicio)->format('H:i') }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->horafin)->format('H:i') }}</td>
                    <td>{{ $item->establecimiento?->nombre ?? '—' }}</td>
                    <td>{{ $item->curso?->nombre_completo ?? '—' }}</td>
                    <td>{{ $item->materia?->nombre ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

@if($declaracion->items->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>Esta declaración no tiene ítems registrados.
</div>
@endif
@endsection