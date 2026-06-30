@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Exportar a Excel</h4>
        <p class="text-muted">Seleccioná el reporte a exportar. El archivo se descarga directamente.</p>
    </div>
</div>

<div class="row g-4">

    {{-- 1. Listado de alumnos --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-people me-2 text-success"></i>Listado de alumnos
            </div>
            <div class="card-body">
                <p class="text-muted small">Datos completos de los alumnos de un curso: DNI, fecha de nacimiento, teléfono, email y contacto de emergencia.</p>
                <form method="GET" action="{{ route('excel.alumnos') }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Curso</label>
                        <select name="curso_id" class="form-select" required>
                            <option value="">— Seleccioná —</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success btn-sm w-100">
                        <i class="bi bi-download me-1"></i>Descargar Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Asistencia --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-person-check me-2 text-success"></i>Registro de asistencia
            </div>
            <div class="card-body">
                <p class="text-muted small">Resumen y detalle de asistencias por alumno. Genera 2 hojas: resumen con % y detalle por fecha.</p>
                <form method="GET" action="{{ route('excel.asistencia') }}">
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
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-download me-1"></i>Descargar Excel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 3. Calificaciones --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-journal-text me-2 text-success"></i>Calificaciones
            </div>
            <div class="card-body">
                <p class="text-muted small">Detalle de todas las calificaciones por alumno y resumen con promedio, mínima y máxima.</p>
                <form method="GET" action="{{ route('excel.calificaciones') }}">
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
                    </div>
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-download me-1"></i>Descargar Excel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 4. Cierre de notas --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-journal-check me-2 text-success"></i>Cierre de notas
            </div>
            <div class="card-body">
                <p class="text-muted small">Notas de cierre con valoración, promedios de calificaciones, actividades y porcentaje de asistencia.</p>
                <form method="GET" action="{{ route('excel.cierre') }}">
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
                            <label class="form-label fw-semibold">Tipo de cierre</label>
                            <select name="tipocierre" class="form-select form-select-sm">
                                <option value="">— Todos —</option>
                                <option>1er Cuatrimestre</option>
                                <option>2do Cuatrimestre</option>
                                <option>Anual</option>
                                <option>Recuperatorio</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-download me-1"></i>Descargar Excel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 5. Declaración jurada --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-file-earmark-text me-2 text-success"></i>Declaración jurada
            </div>
            <div class="card-body">
                <p class="text-muted small">Horario declarado: día, entrada, salida, establecimiento, curso y materia.</p>
                <form method="GET" action="{{ route('excel.declaracion') }}">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Declaración</label>
                        <select name="declaracion_id" class="form-select form-select-sm" required>
                            <option value="">— Seleccioná —</option>
                            @php $declaraciones = \App\Models\Declaracion::where('user_id', auth()->id())->orderBy('fechadeclaracion','desc')->get(); @endphp
                            @foreach($declaraciones as $d)
                                <option value="{{ $d->id }}">Ciclo {{ $d->ciclo }} — {{ $d->fechadeclaracion?->format('d/m/Y') }} ({{ $d->estado }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-download me-1"></i>Descargar Excel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 6. Contenidos --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-book me-2 text-success"></i>Contenidos por unidad
            </div>
            <div class="card-body">
                <p class="text-muted small">Temario completo con unidades, temas y subtemas de una materia.</p>
                <form method="GET" action="{{ route('excel.contenidos') }}">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Materia</label>
                        <select name="materia_id" class="form-select form-select-sm" required>
                            <option value="">—</option>
                            @foreach($materias as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-download me-1"></i>Descargar Excel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 7. Libro de temas --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-journal-bookmark me-2 text-success"></i>Libro de temas
            </div>
            <div class="card-body">
                <p class="text-muted small">Registro diario de clases con fecha, tipo, tema y observación.</p>
                <form method="GET" action="{{ route('excel.librotemas') }}">
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
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-download me-1"></i>Descargar Excel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 8. Reporte general --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#e8f5e9">
                <i class="bi bi-clipboard-data me-2 text-success"></i>Reporte general del docente
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <p class="text-muted small">Resumen completo en 3 hojas: estadísticas generales, listado de materias y listado de cursos con alumnos.</p>
                <a href="{{ route('excel.docente') }}" class="btn btn-success btn-sm w-100 mt-2">
                    <i class="bi bi-download me-1"></i>Descargar Excel
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
