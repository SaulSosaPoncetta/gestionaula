@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-arrow me-2"></i>Asignar actividad</h4>
        <p class="text-muted">
            <strong>{{ $curso->nombre_completo }}</strong>
            &mdash;
            <strong>{{ $materia->nombre }}</strong>
        </p>
    </div>
    <div class="col-auto">
        <a href="{{ route('asignarnuevo.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
@endif

@if($actividades->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay actividades cargadas para esta materia.
        <a href="{{ route('actividades.seleccionar') }}" class="alert-link ms-2">Crear una actividad</a>
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-list-check me-1"></i>Actividades disponibles ({{ $actividades->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Unidad</th>
                    <th>N° Act.</th>
                    <th>Tema</th>
                    <th>Tipo</th>
                    <th class="text-center">Items</th>
                    <th class="text-center">Estado asig.</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividades as $act)
                @php $asignada = in_array($act->id, $yaAsignadas); @endphp
                <tr>
                    <td class="ps-4 text-center">
                        <span class="badge bg-secondary">{{ $act->numerounidad ?? '—' }}</span>
                    </td>
                    <td class="text-center">{{ $act->numeroactividad ?? '—' }}</td>
                    <td class="fw-semibold">{{ $act->tema }}</td>
                    <td>{{ $act->tipoactividad?->denominacion ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $act->items->count() }}</span>
                    </td>
                    <td class="text-center">
                        @if($asignada)
                            <span class="badge bg-success">Asignada</span>
                        @else
                            <span class="badge bg-light text-dark">Sin asignar</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$asignada)
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAsignar{{ $act->id }}">
                                <i class="bi bi-send me-1"></i>Asignar
                            </button>
                        @else
                            <span class="text-muted small">Ya asignada</span>
                        @endif
                    </td>
                </tr>

                {{-- Modal de asignación --}}
                @if(!$asignada)
                <div class="modal fade" id="modalAsignar{{ $act->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">
                                    <i class="bi bi-send me-1"></i>Asignar actividad
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('asignarnuevo.asignar') }}">
                                @csrf
                                <input type="hidden" name="actividad_id" value="{{ $act->id }}">
                                <input type="hidden" name="curso_id"     value="{{ $curso->id }}">
                                <input type="hidden" name="materia_id"   value="{{ $materia->id }}">

                                <div class="modal-body">
                                    {{-- Info de la actividad --}}
                                    <div class="alert alert-light border mb-4">
                                        <div class="fw-semibold">{{ $act->tema }}</div>
                                        <div class="text-muted small">
                                            Unidad {{ $act->numerounidad ?? '—' }}
                                            @if($act->numeroactividad) | Actividad N° {{ $act->numeroactividad }} @endif
                                            | {{ $act->tipoactividad?->denominacion ?? '—' }}
                                            | {{ $act->items->count() }} consigna(s)
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                Fecha inicio <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="fechainicio"
                                                   class="form-control"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                Fecha entrega <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="fechaentrega"
                                                   class="form-control" required>
                                        </div>

                                        {{-- Modalidad --}}
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       name="esgrupal" value="1"
                                                       id="esgrupal{{ $act->id }}"
                                                       onchange="toggleGrupal({{ $act->id }})">
                                                <label class="form-check-label fw-semibold"
                                                       for="esgrupal{{ $act->id }}">
                                                    Trabajo en grupo
                                                </label>
                                            </div>
                                        </div>

                                        <div id="seccionGrupal{{ $act->id }}" style="display:none" class="col-12">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">
                                                        Integrantes por grupo
                                                    </label>
                                                    <input type="number" name="integrantesporgrupo"
                                                           class="form-control" min="2" value="2">
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label fw-semibold">Modo</label>
                                                    <div class="d-flex gap-3 mt-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                   name="modogrupo"
                                                                   value="aleatorio" checked>
                                                            <label class="form-check-label">
                                                                <i class="bi bi-shuffle me-1"></i>Aleatorio
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                   name="modogrupo"
                                                                   value="manual">
                                                            <label class="form-check-label">
                                                                <i class="bi bi-hand-index me-1"></i>Manual
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Confirmar asignación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function toggleGrupal(actId) {
    const chk     = document.getElementById(`esgrupal${actId}`);
    const seccion = document.getElementById(`seccionGrupal${actId}`);
    seccion.style.display = chk.checked ? '' : 'none';
}
</script>
@endpush