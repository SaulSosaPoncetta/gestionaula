@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Calificar actividades</h4>
        <p class="text-muted">
            <strong>{{ $materia->nombre }}</strong>
            &mdash;
            <strong>{{ $curso->nombre_completo }}</strong>
        </p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('calificaractividad.historial', ['materia_id' => $materia->id, 'curso_id' => $curso->id]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Historial
        </a>
        <a href="{{ route('calificaractividad.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if($asignaciones->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay actividades asignadas para esta materia y curso.
    </div>
@elseif($todosAlumnos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>No hay alumnos registrados en este curso.
    </div>
@else

{{-- Buscador --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('calificaractividad.ver') }}"
              class="row g-2 align-items-center" id="form-buscar">
            @csrf
            <input type="hidden" name="materia_id" value="{{ $materia->id }}">
            <input type="hidden" name="curso_id"   value="{{ $curso->id }}">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="buscar" id="input-buscar"
                           class="form-control border-start-0"
                           placeholder="Buscar por apellido o nombre..."
                           value="{{ $buscar ?? '' }}"
                           autocomplete="off">
                    @if($buscar)
                    <a href="{{ route('calificaractividad.ver', ['materia_id' => $materia->id, 'curso_id' => $curso->id]) }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
            <div class="col-auto ms-auto text-muted small">
                @if($buscar)
                    {{ $alumnos->count() }} resultado(s) para "{{ $buscar }}"
                @else
                    {{ $todosAlumnos->count() }} alumno(s) en total
                    &mdash;
                    <button type="button" class="btn btn-link btn-sm p-0 text-primary"
                            onclick="expandirTodos()">Expandir todos</button>
                    /
                    <button type="button" class="btn btn-link btn-sm p-0 text-secondary"
                            onclick="colapsarTodos()">Colapsar todos</button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Sin resultados --}}
@if($alumnos->isEmpty())
<div class="alert alert-warning">
    <i class="bi bi-person-x me-2"></i>No se encontraron alumnos con "{{ $buscar }}".
</div>
@endif

{{-- Acordeón de alumnos --}}
<div class="accordion" id="acordeonAlumnos">

@foreach($alumnos as $alumnoIdx => $alumno)
@php
    $acId         = 'alumno_' . $alumno->id;
    $primerAlumno = $alumnoIdx === 0;
@endphp

<div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden alumno-item"
     data-nombre="{{ strtolower($alumno->apellido . ' ' . $alumno->nombre) }}">

    {{-- Header del acordeón --}}
    <h2 class="accordion-header" id="header_{{ $acId }}">
        <button class="accordion-button {{ $primerAlumno ? '' : 'collapsed' }} fw-semibold"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse_{{ $acId }}"
                aria-expanded="{{ $primerAlumno ? 'true' : 'false' }}"
                aria-controls="collapse_{{ $acId }}"
                style="background: #f8f9fa">
            <div class="d-flex align-items-center gap-3 w-100 me-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:36px;height:36px;font-size:13px;font-weight:700">
                    {{ strtoupper(substr($alumno->apellido, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <span class="fs-6">{{ $alumno->apellido }}, {{ $alumno->nombre }}</span>
                    @if($alumno->tipocursada === 'libre')
                        <span class="badge bg-warning text-dark ms-2 small">Libre</span>
                    @endif
                </div>
                @php
                    $notasAlumno   = $notasRegistradas->filter(fn($n) => $n->alumno_id === $alumno->id);
                    $totalActs     = $asignaciones->count();
                    $calificadas   = $notasAlumno->count();
                    $porcentaje    = $totalActs > 0 ? intval($calificadas / $totalActs * 100) : 0;
                    $colorBarra    = $porcentaje === 100 ? 'success' : ($porcentaje >= 50 ? 'warning' : 'danger');
                @endphp
                <div class="d-flex align-items-center gap-2 me-3">
                    <small class="text-muted">{{ $calificadas }}/{{ $totalActs }} calificadas</small>
                    <div class="progress flex-shrink-0" style="width:80px;height:6px">
                        <div class="progress-bar bg-{{ $colorBarra }}" style="width:{{ $porcentaje }}%"></div>
                    </div>
                    @if($calificadas === $totalActs && $totalActs > 0)
                        <span class="badge bg-success"><i class="bi bi-check-all"></i></span>
                    @endif
                </div>
            </div>
        </button>
    </h2>

    {{-- Contenido del acordeón --}}
    <div id="collapse_{{ $acId }}"
         class="accordion-collapse collapse {{ $primerAlumno ? 'show' : '' }}"
         aria-labelledby="header_{{ $acId }}"
         data-bs-parent="">
        <div class="accordion-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Actividad</th>
                        <th>Tipo</th>
                        <th>Entrega</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Nota individual</th>
                        <th class="text-center">Nota grupal</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asignaciones as $asig)
                    @php
                        $key  = $alumno->id . '_' . $asig->id;
                        $nota = $notasRegistradas->get($key);

                        $estadoAuto = 'pendiente';
                        if ($nota) {
                            $estadoAuto = $nota->estado;
                        } elseif ($asig->fechaentrega->isPast()) {
                            $estadoAuto = 'vencido';
                        } elseif ($asig->fechainicio->isPast() || $asig->fechainicio->isToday()) {
                            $estadoAuto = 'enproceso';
                        }

                        $grupoAlumno = null;
                        if ($asig->actividad?->esgrupal) {
                            foreach ($asig->actividad->grupos as $grupo) {
                                if ($grupo->alumnos->contains('id', $alumno->id)) {
                                    $grupoAlumno = $grupo;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $asig->actividad?->tema ?? '—' }}</div>
                            @if($asig->actividad?->numerounidad)
                                <span class="badge bg-secondary">Unidad {{ $asig->actividad->numerounidad }}</span>
                            @endif
                            @if($asig->actividad?->numeroactividad)
                                <span class="badge bg-secondary">Act. N° {{ $asig->actividad->numeroactividad }}</span>
                            @endif
                            @if($grupoAlumno)
                                <span class="badge bg-info">{{ $grupoAlumno->nombre }}</span>
                            @endif
                        </td>
                        <td class="small">{{ $asig->actividad?->tipoactividad?->denominacion ?? '—' }}</td>
                        <td class="small">{{ $asig->fechaentrega->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @php
                                $badgeEstado = match($estadoAuto) {
                                    'entregado' => 'success',
                                    'vencido'   => 'danger',
                                    'enproceso' => 'primary',
                                    default     => 'secondary',
                                };
                                $labelEstado = match($estadoAuto) {
                                    'entregado' => 'Entregado',
                                    'vencido'   => 'Vencido',
                                    'enproceso' => 'En proceso',
                                    default     => 'Pendiente',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeEstado }}">{{ $labelEstado }}</span>
                        </td>
                        <td class="text-center">
                            @if($nota?->notaindividual !== null)
                                <span class="badge bg-{{ $nota->notaindividual >= 7 ? 'success' : ($nota->notaindividual >= 4 ? 'warning' : 'danger') }} fs-6">
                                    {{ number_format($nota->notaindividual, 2) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($asig->esgrupal)
                                @if($nota?->notagrupal !== null)
                                    <span class="badge bg-{{ $nota->notagrupal >= 7 ? 'success' : ($nota->notagrupal >= 4 ? 'warning' : 'danger') }} fs-6">
                                        {{ number_format($nota->notagrupal, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalCalificar{{ $alumno->id }}_{{ $asig->id }}">
                                <i class="bi bi-pencil me-1"></i>Calificar
                            </button>
                        </td>
                    </tr>
                {{-- Modal calificar --}}
                <div class="modal fade" id="modalCalificar{{ $alumno->id }}_{{ $asig->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-pencil me-1"></i>Calificar actividad
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('calificaractividad.calificar') }}">
                                @csrf
                                <input type="hidden" name="asignacion_id" value="{{ $asig->id }}">
                                <input type="hidden" name="alumno_id"     value="{{ $alumno->id }}">
                                <input type="hidden" name="actividad_id"  value="{{ $asig->actividad_id }}">

                                <div class="modal-body">
                                    <div class="alert alert-light border mb-3">
                                        <div class="fw-semibold">{{ $alumno->nombre_completo }}</div>
                                        <div class="text-muted small">{{ $asig->actividad?->tema }}</div>
                                        @if($grupoAlumno)
                                            <div class="text-muted small">
                                                <i class="bi bi-people me-1"></i>{{ $grupoAlumno->nombre }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                Nota individual <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="notaindividual"
                                                   class="form-control"
                                                   value="{{ $nota?->notaindividual }}"
                                                   min="1" max="10" step="0.25"
                                                   placeholder="1 — 10">
                                        </div>

                                        @if($asig->esgrupal)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                Nota grupal
                                            </label>
                                            <input type="number" name="notagrupal"
                                                   class="form-control"
                                                   value="{{ $nota?->notagrupal }}"
                                                   min="1" max="10" step="0.25"
                                                   placeholder="1 — 10">
                                            <div class="form-text">Nota por el trabajo del grupo.</div>
                                        </div>
                                        @endif

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Estado</label>
                                            <select name="estado" class="form-select">
                                                <option value="pendiente"  {{ ($nota?->estado ?? $estadoAuto) === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                                                <option value="enproceso"  {{ ($nota?->estado ?? $estadoAuto) === 'enproceso'  ? 'selected' : '' }}>En proceso</option>
                                                <option value="entregado"  {{ ($nota?->estado ?? $estadoAuto) === 'entregado'  ? 'selected' : '' }}>Entregado</option>
                                                <option value="vencido"    {{ ($nota?->estado ?? $estadoAuto) === 'vencido'    ? 'selected' : '' }}>Vencido</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Observación</label>
                                            <textarea name="observacion" class="form-control" rows="2"
                                                      placeholder="Opcional">{{ $nota?->observacion }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endforeach

</div>{{-- fin acordeon --}}
@endif
@endsection

@push('scripts')
<script>
// Búsqueda en tiempo real (filtra localmente sin recargar)
document.getElementById('input-buscar')?.addEventListener('input', function () {
    const q     = this.value.trim().toLowerCase();
    const items = document.querySelectorAll('.alumno-item');
    let visibles = 0;

    items.forEach(item => {
        const nombre = item.dataset.nombre || '';
        if (!q || nombre.includes(q)) {
            item.style.display = '';
            visibles++;
        } else {
            item.style.display = 'none';
        }
    });
});

// Expandir / colapsar todos
function expandirTodos() {
    document.querySelectorAll('.accordion-collapse').forEach(el => {
        new bootstrap.Collapse(el, { show: true });
    });
}
function colapsarTodos() {
    document.querySelectorAll('.accordion-collapse.show').forEach(el => {
        new bootstrap.Collapse(el, { hide: true });
    });
}
</script>
@endpush
