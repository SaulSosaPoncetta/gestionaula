@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar-x me-2"></i>Ceses</h4>
        <p class="text-muted">Registro de ceses de materias y horarios.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ceses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Registrar cese
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if($ceses->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay ceses registrados.
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Materia</th>
                    <th>Establecimiento</th>
                    <th>N° Secuencia</th>
                    <th>Horario cesado</th>
                    <th>Toma de posesión</th>
                    <th>Fecha de cese</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($ceses as $cese)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $cese->materia?->nombre ?? '—' }}</td>
                    <td>{{ $cese->establecimiento?->nombre ?? '—' }}</td>
                    <td>{{ $cese->numerosecuencia ?? '—' }}</td>
                    <td>
                        @if($cese->dia)
                            <span class="badge bg-secondary">{{ ucfirst($cese->dia) }}</span>
                            {{ \Carbon\Carbon::parse($cese->horainicio)->format('H:i') }}
                            —
                            {{ \Carbon\Carbon::parse($cese->horafin)->format('H:i') }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $cese->fechatomapossesion->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-danger">
                            {{ $cese->fechacese->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <form method="POST" action="{{ route('ceses.destroy', $cese) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este cese?')">
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
<div class="mt-3">{{ $ceses->links() }}</div>
@endif
@endsection