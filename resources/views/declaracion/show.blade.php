@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold">
            <i class="bi bi-file-earmark-text me-2"></i>Declaración jurada — {{ $declaracion->ciclo }}
        </h4>
        <p class="text-muted">
            Docente: <strong>{{ $declaracion->docente->name }}</strong>
            &mdash;
            <span class="badge bg-{{ $declaracion->estadobadge }}">{{ ucfirst($declaracion->estado) }}</span>
        </p>
    </div>
    <div class="col-auto d-flex gap-2 align-items-start">
        @if($declaracion->estado === 'borrador' && $declaracion->user_id === auth()->id())
            <form method="POST" action="{{ route('declaracion.presentar', $declaracion) }}">
                @csrf
                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('¿Presentar esta declaración? No podrás modificarla.')">
                    <i class="bi bi-send me-1"></i>Presentar
                </button>
            </form>
        @endif
        <a href="{{ route('declaracion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

{{-- Resolución del director --}}
@if(auth()->user()->hasRole('director') && $declaracion->estado === 'presentada')
<div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-check2-square me-2"></i>Resolver declaración</h6>
        <form method="POST" action="{{ route('declaracion.resolver', $declaracion) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Resolución</label>
                    <select name="estado" class="form-select" required>
                        <option value="aprobada">Aprobar</option>
                        <option value="rechazada">Rechazar</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Observación <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="text" name="observacion" class="form-control" placeholder="Motivo o comentario...">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle me-1"></i>Confirmar resolución
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Observación si fue rechazada --}}
@if($declaracion->estado === 'rechazada' && $declaracion->observacion)
<div class="alert alert-danger">
    <i class="bi bi-x-circle me-2"></i><strong>Motivo de rechazo:</strong> {{ $declaracion->observacion }}
</div>
@endif

{{-- Tabla de horarios por día --}}
@foreach($dias as $dia)
    @if($itemspordía[$dia]->isNotEmpty())
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-primary text-white fw-semibold text-capitalize">
            <i class="bi bi-calendar-day me-1"></i>{{ ucfirst($dia) }}
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Hora inicio</th>
                        <th>Hora fin</th>
                        <th>Curso</th>
                        <th>Materia</th>
                        <th>Actividad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemspordía[$dia] as $item)
                    <tr>
                        <td class="ps-4">{{ \Carbon\Carbon::parse($item->horainicio)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->horafin)->format('H:i') }}</td>
                        <td>{{ $item->curso?->nombre_completo ?? '—' }}</td>
                        <td>{{ $item->materia?->nombre ?? '—' }}</td>
                        <td>{{ $item->actividad ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endforeach

{{-- Info de resolución --}}
@if($declaracion->fecharesolucion)
<div class="text-muted small mt-3">
    <i class="bi bi-info-circle me-1"></i>
    Resuelta el {{ $declaracion->fecharesolucion->format('d/m/Y H:i') }}
    por {{ $declaracion->resolutor?->name ?? '—' }}
</div>
@endif
@endsection