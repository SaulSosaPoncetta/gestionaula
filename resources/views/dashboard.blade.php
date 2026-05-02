@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-house me-2"></i>Panel principal</h4>
        <p class="text-muted">Bienvenido, <strong>{{ auth()->user()->name }}</strong>.</p>
    </div>
</div>

{{-- Card fecha y hora --}}
<div class="card border-0 shadow-sm mb-4 bg-primary text-white">
    <div class="card-body text-center py-4">
        <div class="display-3 fw-bold" id="reloj">--:--:--</div>
        <div class="fs-4 fw-semibold mt-1" id="diaSemana">—</div>
        <div class="fs-5" id="fechaCompleta">—</div>
    </div>
</div>

{{-- Accesos rápidos --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-person-check fs-4 text-primary"></i>
                    </div>
                    <h6 class="card-title mb-0">Asistencia</h6>
                </div>
                <p class="card-text text-muted small">Registrá la asistencia diaria de tus alumnos.</p>
                <a href="{{ route('asistencia.index') }}" class="btn btn-sm btn-outline-primary">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-journal-text fs-4 text-success"></i>
                    </div>
                    <h6 class="card-title mb-0">Calificaciones</h6>
                </div>
                <p class="card-text text-muted small">Cargá y consultá las notas de tus alumnos.</p>
                <a href="{{ route('calificaciones.index') }}" class="btn btn-sm btn-outline-success">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-clipboard-check fs-4 text-warning"></i>
                    </div>
                    <h6 class="card-title mb-0">Tareas</h6>
                </div>
                <p class="card-text text-muted small">Asigná tareas y revisá las entregas.</p>
                <a href="{{ route('tareas.index') }}" class="btn btn-sm btn-outline-warning">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-calendar3 fs-4 text-info"></i>
                    </div>
                    <h6 class="card-title mb-0">Horarios</h6>
                </div>
                <p class="card-text text-muted small">Planificá y consultá tu horario semanal.</p>
                <a href="{{ route('horarios.index') }}" class="btn btn-sm btn-outline-info">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-file-earmark-text fs-4 text-secondary"></i>
                    </div>
                    <h6 class="card-title mb-0">Declaración jurada</h6>
                </div>
                <p class="card-text text-muted small">Completá y enviá tu declaración jurada de horarios.</p>
                <a href="{{ route('declaracion.index') }}" class="btn btn-sm btn-outline-secondary">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-chat-dots fs-4 text-danger"></i>
                    </div>
                    <h6 class="card-title mb-0">Comunicación</h6>
                </div>
                <p class="card-text text-muted small">Enviá mensajes a alumnos y familias.</p>
                <a href="{{ route('comunicacion.index') }}" class="btn btn-sm btn-outline-danger">Ir al módulo</a>
            </div>
        </div>
    </div>
</div>

{{-- Card clase actual --}}
<div id="claseActualContainer"></div>

@endsection

@push('scripts')
<script>
const horariosDocente = @json($horarios);

const diasSemana = ['domingo','lunes','martes','miercoles','jueves','viernes','sabado'];
const diasLabel  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
const meses      = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

function pad(n) { return String(n).padStart(2, '0'); }

function horaAMinutos(str) {
    const [h, m] = str.split(':').map(Number);
    return h * 60 + m;
}

function clasesActuales(diaActual, minutosActuales) {
    return horariosDocente.filter(h =>
        h.dia === diaActual &&
        horaAMinutos(h.horainicio) <= minutosActuales &&
        horaAMinutos(h.horafin)    >  minutosActuales
    );
}

function renderClaseActual(clases) {
    const container = document.getElementById('claseActualContainer');

    if (clases.length === 0) {
        container.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-4">
                    <i class="bi bi-clock fs-3 mb-2 d-block"></i>
                    No hay clase en este momento según tu horario.
                </div>
            </div>`;
        return;
    }

    let html = '';
    clases.forEach(clase => {
        html += `
        <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
            <div class="card-body">

                {{-- Título: materia y curso --}}
                <div class="text-center mb-3">
                    <div class="fs-3 fw-bold text-success">
                        <i class="bi bi-book me-2"></i>${clase.materia ?? 'Sin materia'}
                    </div>
                    <div class="fs-5 text-muted fw-semibold">${clase.curso ?? '—'}</div>
                </div>

                <hr>

                {{-- Datos del horario --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Horario</td>
                                <td class="fw-semibold">${clase.horainicio} — ${clase.horafin}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Lista de alumnos --}}
                <div class="fw-semibold mb-2">
                    <i class="bi bi-people me-1"></i>Estudiantes
                </div>`;

        if (clase.alumnos.length > 0) {
            html += `
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Apellido y nombre</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>`;

            clase.alumnos.forEach((a, idx) => {
                html += `
                        <tr>
                            <td class="text-muted">${idx + 1}</td>
                            <td class="fw-semibold">${a.nombre}</td>
                            <td class="text-end">
                                <a href="/alumnos/${a.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Ver ficha
                                </a>
                            </td>
                        </tr>`;
            });

            html += `
                    </tbody>
                </table>`;
        } else {
            html += `<div class="text-muted small">No hay alumnos registrados en este curso.</div>`;
        }

        html += `
            </div>
        </div>`;
    });

    container.innerHTML = html;
}

function actualizarReloj() {
    const ahora  = new Date();
    const h      = pad(ahora.getHours());
    const m      = pad(ahora.getMinutes());
    const s      = pad(ahora.getSeconds());
    const diaNum = ahora.getDay();
    const dia    = diasSemana[diaNum];

    document.getElementById('reloj').textContent     = `${h}:${m}:${s}`;
    document.getElementById('diaSemana').textContent = diasLabel[diaNum];
    document.getElementById('fechaCompleta').textContent =
        `${ahora.getDate()} de ${meses[ahora.getMonth()]} de ${ahora.getFullYear()}`;

    const minutosActuales = ahora.getHours() * 60 + ahora.getMinutes();
    renderClaseActual(clasesActuales(dia, minutosActuales));
}

actualizarReloj();
setInterval(actualizarReloj, 1000);
</script>
@endpush