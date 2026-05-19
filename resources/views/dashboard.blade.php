@extends('layouts.app')

@section('content')
    {{-- Saludo --}}
    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold"><i class="bi bi-house me-2"></i>Panel principal</h4>
            <p class="text-muted mb-0">Bienvenido, <strong>{{ auth()->user()->name }}</strong>.</p>
            <div id="leyendaClaseActual"
                class="mt-3 d-flex flex-column align-items-center justify-content-center
            w-100 p-3 rounded bg-light border"
                style="display:none!important">
            </div>
        </div>
    </div>

    {{-- Card fecha y hora --}}
    <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
        <div class="card-body text-center py-4">
            <div class="display-3 fw-bold" id="reloj">--:--:--</div>
            <div class="fs-4 fw-semibold mt-1" id="diaSemana">-</div>
            <div class="fs-5" id="fechaCompleta">-</div>
        </div>
    </div>

    {{-- Accesos rapidos --}}
    <div class="row g-3 mb-4" id="accesoRapido">
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-person-check fs-4 text-primary"></i>
                        </div>
                        <h6 class="card-title mb-0">Asistencia</h6>
                    </div>
                    <p class="card-text text-muted small">Registra la asistencia diaria de tus alumnos.</p>
                    <button id="btnAsistencia" class="btn btn-sm btn-outline-primary"
                        onclick="accesoRapidoModulo('asistencia')">
                        Ir al modulo
                    </button>
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
                    <p class="card-text text-muted small">Carga y consulta las notas de tus alumnos.</p>
                    <button id="btnCalificaciones" class="btn btn-sm btn-outline-success"
                        onclick="accesoRapidoModulo('calificaciones')">
                        Ir al modulo
                    </button>
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
                        <h6 class="card-title mb-0">Practicos</h6>
                    </div>
                    <p class="card-text text-muted small">Asigna practicos y revisa las entregas.</p>
                    <button id="btnPracticos" class="btn btn-sm btn-outline-warning"
                        onclick="accesoRapidoModulo('practicos')">
                        Ir al modulo
                    </button>
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
                    <p class="card-text text-muted small">Planifica y consulta tu horario semanal.</p>
                    <a href="{{ route('horarios.index') }}" class="btn btn-sm btn-outline-info">Ir al modulo</a>
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
                        <h6 class="card-title mb-0">Declaracion jurada</h6>
                    </div>
                    <p class="card-text text-muted small">Completa y envia tu declaracion jurada de horarios.</p>
                    <a href="{{ route('declaracion.index') }}" class="btn btn-sm btn-outline-secondary">Ir al modulo</a>
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
                        <h6 class="card-title mb-0">Comunicacion</h6>
                    </div>
                    <p class="card-text text-muted small">Envia mensajes a alumnos y familias.</p>
                    <a href="{{ route('comunicacion.index') }}" class="btn btn-sm btn-outline-danger">Ir al modulo</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Card clase actual --}}
    <div id="claseActualContainer"></div>

    {{-- Modal acceso rapido --}}
    <div class="modal fade" id="modalAcceso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Acceso rapido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalCuerpo"></div>
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')
    <script>
        const horariosDocente = @json($horarios);

        const diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        const diasLabel = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
        const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre',
            'noviembre', 'diciembre'
        ];

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function horaAMinutos(str) {
            const [h, m] = str.split(':').map(Number);
            return h * 60 + m;
        }

        function clasesActuales(diaActual, minutosActuales) {
            return horariosDocente.filter(h =>
                h.dia === diaActual &&
                horaAMinutos(h.horainicio) <= minutosActuales &&
                horaAMinutos(h.horafin) > minutosActuales
            );
        }

        function actualizarLeyenda(clases) {
            const leyenda = document.getElementById('leyendaClaseActual');
            if (!leyenda) return;

            if (clases.length === 0) {
                leyenda.style.display = 'none';
                leyenda.innerHTML = '';
                return;
            }

            const clase = clases[0];
            let html = '';

            if (clase.establecimiento) {
                html += `<div class="w-100 text-center mb-1">
            <i class="bi bi-building me-1 text-primary fs-5"></i>
            <span class="fs-5"><strong>Establecimiento:</strong> ${clase.establecimiento}</span>
        </div>`;
            }
            if (clase.materia) {
                html += `<div class="w-100 text-center mb-1">
            <i class="bi bi-book me-1 text-success fs-5"></i>
            <span class="fs-5"><strong>Materia:</strong> ${clase.materia}</span>
        </div>`;
            }
            if (clase.curso) {
                html += `<div class="w-100 text-center mb-1">
            <i class="bi bi-people me-1 text-warning fs-5"></i>
            <span class="fs-5"><strong>Curso:</strong> ${clase.curso}</span>
        </div>`;
            }

            leyenda.innerHTML = html;
            leyenda.style.display = 'flex';
        }

        function abrirModal(modulo, cursoId, materiaId, cursoNombre, materiaNombre, alumnos) {
            const titulo = document.getElementById('modalTitulo');
            const cuerpo = document.getElementById('modalCuerpo');
            const fecha = new Date();
            const hoy = `${fecha.getFullYear()}-${pad(fecha.getMonth()+1)}-${pad(fecha.getDate())}`;

            titulo.innerHTML =
                `<i class="bi bi-lightning me-2"></i>Acceso rapido - ${materiaNombre ?? 'Sin materia'} - ${cursoNombre}`;

            let html = '';

            if (modulo === 'asistencia') {
                html += `
        <p class="text-muted small mb-3">Selecciona como queres registrar la asistencia:</p>
        <div class="d-grid gap-2">
            <a href="/asistencia/registrar?curso_id=${cursoId}&materia_id=${materiaId}&fecha=${hoy}"
               class="btn btn-primary">
                <i class="bi bi-people me-2"></i>Registrar asistencia del curso completo
            </a>
        </div>
        <hr>
        <p class="text-muted small mb-2">O ver asistencia de un alumno en particular:</p>
        <div class="d-grid gap-2">`;
                alumnos.forEach(a => {
                    html += `<a href="/asistencia/alumno?alumno_id=${a.id}&buscar=${encodeURIComponent(a.nombre)}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person me-1"></i>${a.nombre}
                     </a>`;
                });
                html += `</div>`;
            } else if (modulo === 'calificaciones') {
                html += `
        <p class="text-muted small mb-3">Selecciona como queres ver las calificaciones:</p>
        <div class="d-grid gap-2">
            <a href="/calificaciones/historial?curso_id=${cursoId}&materia_id=${materiaId}"
               class="btn btn-success">
                <i class="bi bi-journal-text me-2"></i>Ver calificaciones del curso completo
            </a>
            <a href="/calificaciones?curso_id=${cursoId}&materia_id=${materiaId}"
               class="btn btn-outline-success">
                <i class="bi bi-plus-circle me-2"></i>Cargar nuevas calificaciones
            </a>
        </div>
        <hr>
        <p class="text-muted small mb-2">O ver calificaciones de un alumno en particular:</p>
        <div class="d-grid gap-2">`;
                alumnos.forEach(a => {
                    html += `<a href="/calificaciones/historial?curso_id=${cursoId}&materia_id=${materiaId}&alumno_id=${a.id}"
                       class="btn btn-sm btn-outline-success">
                        <i class="bi bi-person me-1"></i>${a.nombre}
                     </a>`;
                });
                html += `</div>`;
            } else if (modulo === 'practicos') {
                html += `
        <p class="text-muted small mb-3">Selecciona como queres ver los practicos:</p>
        <div class="d-grid gap-2">
            <a href="/tareas?curso_id=${cursoId}"
               class="btn btn-warning text-dark">
                <i class="bi bi-clipboard-check me-2"></i>Ver todos los practicos del curso
            </a>
            <a href="/tareas/crear?curso_id=${cursoId}"
               class="btn btn-outline-warning text-dark">
                <i class="bi bi-plus-circle me-2"></i>Crear nuevo practico
            </a>
        </div>`;
            }

            cuerpo.innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalAcceso')).show();
        }

        function renderClaseActual(clases) {
            const container = document.getElementById('claseActualContainer');

            if (clases.length === 0) {
                container.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-4">
                    <i class="bi bi-clock fs-3 mb-2 d-block"></i>
                    No hay clase en este momento segun tu horario.
                </div>
            </div>`;
                return;
            }

            let html = '';
            clases.forEach(clase => {
                const alumnosJson = JSON.stringify(clase.alumnos).replace(/"/g, '&quot;');
                const cursoNombre = (clase.curso ?? '').replace(/"/g, '&quot;');
                const materiaNombre = (clase.materia ?? '').replace(/"/g, '&quot;');
                const fecha = new Date();
                const hoy = `${fecha.getFullYear()}-${pad(fecha.getMonth()+1)}-${pad(fecha.getDate())}`;

                html += `
        <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="fs-3 fw-bold text-success">
                        <i class="bi bi-book me-2"></i>${clase.materia ?? 'Sin materia'}
                    </div>
                    <div class="fs-5 text-muted fw-semibold">${clase.curso ?? '-'}</div>
                    <div class="text-muted small">${clase.horainicio} - ${clase.horafin}</div>
                    ${clase.establecimiento ? `<div class="text-muted small"><i class="bi bi-building me-1"></i>${clase.establecimiento}</div>` : ''}
                </div>
                <hr>
                <div class="fw-semibold mb-2">
                    <i class="bi bi-people me-1"></i>Estudiantes
                </div>`;

                if (clase.alumnos.length > 0) {
                    html += `
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Apellido y nombre</th>
                            <th class="text-center" style="width:120px">Asistencia</th>
                            <th class="text-center" style="width:130px">Calificaciones</th>
                            <th class="text-center" style="width:110px">Practicos</th>
                            <th class="text-center" style="width:100px">Ficha</th>
                        </tr>
                    </thead>
                    <tbody>`;
                    clase.alumnos.forEach((a, idx) => {
                        html += `
                        <tr>
                            <td class="text-muted">${idx + 1}</td>
                            <td class="fw-semibold">${a.nombre}</td>
                            <td class="text-center">
                                <a href="/asistencia/alumno?alumno_id=${a.id}&buscar=${encodeURIComponent(a.nombre)}"
                                   class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;padding:2px 8px;">
                                    <i class="bi bi-person-check"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="/calificaciones/historial?curso_id=${clase.curso_id}&materia_id=${clase.materia_id}&alumno_id=${a.id}"
                                   class="btn btn-xs btn-outline-success" style="font-size:0.75rem;padding:2px 8px;">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="/tareas?curso_id=${clase.curso_id}"
                                   class="btn btn-xs btn-outline-warning" style="font-size:0.75rem;padding:2px 8px;">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="/alumnos/${a.id}"
                                   class="btn btn-xs btn-outline-dark" style="font-size:0.75rem;padding:2px 8px;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>`;
                    });
                    html += `</tbody></table>`;
                } else {
                    html += `<div class="text-muted small">No hay alumnos registrados en este curso.</div>`;
                }

                html += `</div></div>`;
            });

            container.innerHTML = html;
        }

        let claseActivaActual = null;

 function accesoRapidoModulo(modulo) {
    if (modulo === 'asistencia') {
        if (claseActivaActual) {
            // Hay clase activa: ir directo al registro de asistencia con materia y curso
            const fecha = new Date();
            const hoy   = `${fecha.getFullYear()}-${pad(fecha.getMonth()+1)}-${pad(fecha.getDate())}`;
            window.location.href = `/asistencia/registrar?curso_id=${claseActivaActual.curso_id}&materia_id=${claseActivaActual.materia_id}&fecha=${hoy}`;
        } else {
            // No hay clase activa: mostrar modal de aviso
            mostrarModalSinClase();
        }
        return;
    }

    // Para los demás módulos
    if (claseActivaActual) {
        abrirModal(
            modulo,
            claseActivaActual.curso_id,
            claseActivaActual.materia_id,
            claseActivaActual.curso ?? '',
            claseActivaActual.materia ?? '',
            claseActivaActual.alumnos
        );
    } else {
        const rutas = {
            'calificaciones': '/calificaciones',
            'practicos':      '/tareas',
        };
        window.location.href = rutas[modulo] ?? '/dashboard';
    }
}

function mostrarModalSinClase() {
    const modalEl = document.getElementById('modalSinClase');
    new bootstrap.Modal(modalEl).show();
}

        function actualizarReloj() {
            const ahora = new Date();
            const h = pad(ahora.getHours());
            const m = pad(ahora.getMinutes());
            const s = pad(ahora.getSeconds());
            const diaNum = ahora.getDay();
            const dia = diasSemana[diaNum];

            document.getElementById('reloj').textContent = `${h}:${m}:${s}`;
            document.getElementById('diaSemana').textContent = diasLabel[diaNum];
            document.getElementById('fechaCompleta').textContent =
                `${ahora.getDate()} de ${meses[ahora.getMonth()]} de ${ahora.getFullYear()}`;

            const minutosActuales = ahora.getHours() * 60 + ahora.getMinutes();
            const clases = clasesActuales(dia, minutosActuales);
            claseActivaActual = clases.length > 0 ? clases[0] : null;

            actualizarLeyenda(clases);
            renderClaseActual(clases);
        }

        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    </script>
@endpush
