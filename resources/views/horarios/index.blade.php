@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar3 me-2"></i>Horarios</h4>
        <p class="text-muted">Planificación semanal de clases.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('horarios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Agregar horario
        </a>
    </div>
</div>

@if(collect($horarios)->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay horarios cargados todavía.
    </div>
@else
<div class="row g-3">
    @foreach($dias as $dia)
        @if(isset($horarios[$dia]) && $horarios[$dia]->isNotEmpty())
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-calendar-day me-1"></i>{{ ucfirst($dia) }}
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($horarios[$dia] as $horario)
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-semibold">{{ $horario->curso?->nombre_completo ?? '—' }}</div>
                                <div class="text-muted small">
                                    {{ $horario->materia?->nombre ?? 'Sin materia' }}
                                </div>
                                @if($horario->establecimiento)
                                    <div class="text-muted small">
                                        <i class="bi bi-building me-1"></i>{{ $horario->establecimiento->nombre }}
                                    </div>
                                @endif
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($horario->horainicio)->format('H:i') }}
                                    &ndash;
                                    {{ \Carbon\Carbon::parse($horario->horafin)->format('H:i') }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('horarios.destroy', $horario) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Eliminar este horario?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>
@endif
@endsection