@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-printer me-2"></i>Informes impresos</h4>
        <p class="text-muted">Seleccioná el reporte que querés generar.</p>
    </div>
</div>

<div class="row g-4">

    {{-- 1. Listado de alumnos --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e3f2fd">
                <i class="bi bi-people me-2 text-primary"></i>1. Listado de alumnos
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.alumnos') }}" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Curso</label>
                        <select name="curso_id" class="form-select" required>
                            <option value="">— Seleccioná —</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Registro de asistencia --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-person-check me-2 text-success"></i>2. Registro de asistencia
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.asistencia') }}" target="_blank">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Materia</label>
                            <select name="materia_id" class="form-select form-select-sm" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Curso</label>
                            <select name="curso_id" class="form-select form-select-sm" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($cursos as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Desde</label>
                            <input type="date" name="fechainicio" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Hasta</label>
                            <input type="date" name="fechafin" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button class="btn btn-success btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 3. Historial de calificaciones --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#fff8e1">
                <i class="bi bi-journal-text me-2 text-warning"></i>3. Historial de calificaciones
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.calificaciones') }}" target="_blank">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Materia</label>
                            <select name="materia_id" class="form-select form-select-sm" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Curso</label>
                            <select name="curso_id" class="form-select form-select-sm" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($cursos as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 4. Boletín --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#f3e5f5">
                <i class="bi bi-card-text me-2 text-purple" style="color:#7b1fa2"></i>4. Boletín de notas
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.boletin') }}" target="_blank">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Curso</label>
                        <select name="curso_id" class="form-select form-select-sm" id="boletin_curso" required>
                            <option value="">— Seleccioná —</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Alumno</label>
                        <select name="alumno_id" class="form-select form-select-sm" id="boletin_alumno" required>
                            <option value="">— Primero seleccioná curso —</option>
                        </select>
                    </div>
                    <button class="btn btn-sm w-100" style="background:#7b1fa2;color:white">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 5. Cierre de notas --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e0f7fa">
                <i class="bi bi-journal-check me-2" style="color:#00838f"></i>5. Cierre de notas
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.cierre') }}" target="_blank">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Materia</label>
                            <select name="materia_id" class="form-select form-select-sm" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Curso</label>
                            <select name="curso_id" class="form-select form-select-sm" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($cursos as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="tipocierre" class="form-select form-select-sm">
                                <option value="">— Todos —</option>
                                <option>1er Cuatrimestre</option>
                                <option>2do Cuatrimestre</option>
                                <option>Anual</option>
                                <option>Recuperatorio</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-sm w-100" style="background:#00838f;color:white">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 6. Declaración jurada --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#fce4ec">
                <i class="bi bi-file-earmark-text me-2 text-danger"></i>6. Declaración jurada
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.declaracion') }}" target="_blank">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Declaración</label>
                        <select name="declaracion_id" class="form-select form-select-sm" required>
                            <option value="">— Seleccioná —</option>
                            @php
                                $declaraciones = \App\Models\Declaracion::where('user_id', auth()->id())
                                    ->orderBy('fechadeclaracion','desc')->get();
                            @endphp
                            @foreach($declaraciones as $d)
                                <option value="{{ $d->id }}">
                                    Ciclo {{ $d->ciclo }} — {{ $d->fechadeclaracion?->format('d/m/Y') }}
                                    ({{ $d->estado }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 7. Planilla en blanco --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#f1f8e9">
                <i class="bi bi-table me-2" style="color:#558b2f"></i>7. Planilla de asistencia (en blanco)
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.planilla') }}" target="_blank">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Materia</label>
                            <select name="materia_id" class="form-select form-select-sm" required>
                                <option value="">—</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Curso</label>
                            <select name="curso_id" class="form-select form-select-sm" required>
                                <option value="">—</option>
                                @foreach($cursos as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mes</label>
                            <select name="mes" class="form-select form-select-sm">
                                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                                    <option value="{{ $i + 1 }}" {{ ($i + 1) == date('n') ? 'selected' : '' }}>
                                        {{ $mes }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-sm w-100" style="background:#558b2f;color:white">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 8. Contenidos / temario --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#ede7f6">
                <i class="bi bi-book me-2" style="color:#4527a0"></i>8. Contenidos por unidad
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.contenidos') }}" target="_blank">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Materia</label>
                        <select name="materia_id" class="form-select form-select-sm" required>
                            <option value="">—</option>
                            @foreach($materias as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-sm w-100" style="background:#4527a0;color:white">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 9. Libro de temas --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#fff3e0">
                <i class="bi bi-journal-bookmark me-2 text-warning"></i>9. Libro de temas
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pdf.librotemas') }}" target="_blank">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Materia</label>
                            <select name="materia_id" class="form-select form-select-sm" required>
                                <option value="">—</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Curso</label>
                            <select name="curso_id" class="form-select form-select-sm" required>
                                <option value="">—</option>
                                @foreach($cursos as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Desde</label>
                            <input type="date" name="fechainicio" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Hasta</label>
                            <input type="date" name="fechafin" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 10. Reporte general --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8eaf6">
                <i class="bi bi-clipboard-data me-2 text-primary"></i>10. Reporte general del docente
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <p class="text-muted small">Resumen completo: cursos, materias, alumnos, estadísticas de asistencia y calificaciones.</p>
                <a href="{{ route('pdf.docente') }}" target="_blank" class="btn btn-primary btn-sm w-100 mt-2">
                    <i class="bi bi-file-pdf me-1"></i>Generar PDF
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Carga alumnos dinámicamente para el boletín
document.getElementById('boletin_curso').addEventListener('change', function() {
    const cursoId = this.value;
    const alumnoSel = document.getElementById('boletin_alumno');
    alumnoSel.innerHTML = '<option value="">— Cargando... —</option>';
    if (!cursoId) return;

    fetch(`/api/cursos/${cursoId}/alumnos`)
        .then(r => r.json())
        .then(alumnos => {
            alumnoSel.innerHTML = '<option value="">— Seleccioná un alumno —</option>';
            alumnos.forEach(a => {
                alumnoSel.innerHTML += `<option value="${a.id}">${a.apellido}, ${a.nombre}</option>`;
            });
        });
});
</script>
@endpush