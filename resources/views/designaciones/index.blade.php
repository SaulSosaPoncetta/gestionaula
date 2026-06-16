@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-file-earmark-person me-2"></i>Designaciones</h4>
        <p class="text-muted">Registro de designaciones docentes.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('designaciones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nueva designación
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('designaciones.index') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="buscar" class="form-control"
                       placeholder="Buscar por establecimiento, materia, IGE o número..."
                       value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
            @if(request('buscar'))
            <div class="col-md-3">
                <a href="{{ route('designaciones.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@if($designaciones->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay designaciones registradas.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Establecimiento</th>
                    <th>N° Escuela</th>
                    <th>IGE</th>
                    <th>Secuencia</th>
                    <th>Materia</th>
                    <th>Año/Div.</th>
                    <th>Cant. Mód/Hs</th>
                    <th>Horario</th>
                    <th>Turno</th>
                    <th class="text-center">Tipo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($designaciones as $d)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $d->nombreestablecimiento }}</td>
                    <td>{{ $d->numeroescuela }}</td>
                    <td>{{ $d->ige ?? '—' }}</td>
                    <td>{{ $d->secuencia ?? '—' }}</td>
                    <td>{{ $d->nombremateria }}</td>
                    <td>{{ $d->anodesignado }} {{ $d->divisiondesignada }}</td>
                    <td>{{ $d->cantmodulos ?? '—' }}</td>
                    <td>
                        @if($d->tipohorario === 'dividido')
                            <span class="badge bg-primary mb-1">
                                <i class="bi bi-calendar-week me-1"></i>Dividido ({{ $d->horarios->count() }} días)
                            </span>
                            <div class="small text-muted">
                                @foreach($d->horarios as $h)
                                    <div>
                                        {{ \App\Models\Designacion::DIAS[$h->dia] ?? $h->dia }}:
                                        {{ substr($h->horaentrada, 0, 5) }}-{{ substr($h->horasalida, 0, 5) }}
                                        @if($h->cantmodulos)
                                            <span class="badge bg-light text-dark">{{ $h->cantmodulos }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="badge bg-secondary mb-1">
                                {{ \App\Models\Designacion::DIAS[$d->diasemana] ?? $d->diasemana }}
                            </span>
                            <div class="small text-muted">
                                {{ $d->horaentrada ? substr($d->horaentrada, 0, 5) : '—' }}
                                -
                                {{ $d->horasalida ? substr($d->horasalida, 0, 5) : '—' }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $d->turnodesempeno }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">
                            {{ \App\Models\Designacion::TIPOS_HORA[$d->tipohora] ?? $d->tipohora }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('designaciones.edit', $d) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('designaciones.destroy', $d) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar esta designación?')">
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
<div class="mt-3">{{ $designaciones->links() }}</div>
@endif
@endsection
