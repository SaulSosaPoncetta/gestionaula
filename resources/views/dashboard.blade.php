@extends('layouts.app')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php $cicloActivoDash = \App\Models\CicloLectivo::where('user_id', auth()->id())->where('activo', true)->first(); @endphp

    @if(!$cicloActivoDash)
    <div class="alert alert-danger d-flex justify-content-between align-items-center mb-3">
        <span><i class="bi bi-calendar-x me-2"></i><strong>No tenés un ciclo lectivo activo.</strong> Creá uno para comenzar a registrar datos.</span>
        <a href="{{ route('ciclos_lectivos.create') }}" class="btn btn-sm btn-danger"><i class="bi bi-plus-circle me-1"></i>Crear ciclo lectivo</a>
    </div>
    @elseif($cicloActivoDash->yaTermino())
    <div class="alert alert-danger d-flex justify-content-between align-items-center mb-3">
        <span><i class="bi bi-calendar-x me-2"></i>El ciclo lectivo <strong>{{ $cicloActivoDash->anio }}</strong> ya finalizó. ¿Querés crear el del próximo año?</span>
        <a href="{{ route('ciclos_lectivos.siguiente', $cicloActivoDash) }}" class="btn btn-sm btn-danger"><i class="bi bi-arrow-right-circle me-1"></i>Crear ciclo {{ (int)$cicloActivoDash->anio + 1 }}</a>
    </div>
    @elseif($cicloActivoDash->terminoPronto())
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
        <span><i class="bi bi-exclamation-triangle me-2"></i>El ciclo lectivo <strong>{{ $cicloActivoDash->anio }}</strong> vence el {{ $cicloActivoDash->fechafin->format('d/m/Y') }}.</span>
        <a href="{{ route('ciclos_lectivos.siguiente', $cicloActivoDash) }}" class="btn btn-sm btn-warning"><i class="bi bi-calendar2-plus me-1"></i>Generar ciclo {{ (int)$cicloActivoDash->anio + 1 }}</a>
    </div>
    @endif

    {{-- Saludo --}}
    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold"><i class="bi bi-house me-2"></i>Panel principal</h4>
            <p class="text-muted mb-0">Bienvenido, <strong>{{ auth()->user()->name }}</strong>.</p>
        </div>
    </div>

    {{-- Cards Clase actual y Próxima clase --}}
    <div class="row g-3 mb-4">

        {{-- Clase actual --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
                <div class="card-header bg-white fw-bold text-success">
                    <i class="bi bi-play-circle me-1"></i>Clase actual
                </div>
                <div class="card-body" id="leyendaClaseActual">
                    @if ($establecimientoActual || $materiaActual)
                        @if ($establecimientoActual)
                            <div class="mb-2">
                                <div class="text-muted small">Establecimiento</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-building me-1 text-primary"></i>
                                    {{ $establecimientoActual->nombre }}
                                </div>
                            </div>
                        @endif
                        @if ($materiaActual)
                            <div class="mb-2">
                                <div class="text-muted small">Materia</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-book me-1 text-success"></i>
                                    {{ $materiaActual->nombre }}
                                </div>
                            </div>
                        @endif
                        @if ($cursoActual)
                            <div>
                                <div class="text-muted small">Curso</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-people me-1 text-warning"></i>
                                    {{ $cursoActual->nombre_completo }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-clock fs-2 d-block mb-2"></i>
                            No hay clase activa en este momento.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Proximo establecimiento --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-header bg-white fw-bold text-primary">
                    <i class="bi bi-skip-forward-circle me-1"></i>Próximo establecimiento
                </div>
                <div class="card-body">
                    @if ($materiaProxima || $establecimientoProximo)
                        @if ($diaProximo && $horaProximo)
                            <div class="mb-2">
                                <div class="text-muted small">Cuándo</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-calendar me-1 text-primary"></i>
                                    {{ ucfirst($diaProximo) }} a las {{ $horaProximo }}
                                </div>
                            </div>
                        @endif
                        @if ($establecimientoProximo)
                            <div class="mb-2">
                                <div class="text-muted small">Establecimiento</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-building me-1 text-primary"></i>
                                    {{ $establecimientoProximo->nombre }}
                                </div>
                                @if ($establecimientoProximo->direccion)
                                    <div class="text-muted small">
                                        <i class="bi bi-geo me-1"></i>
                                        {{ $establecimientoProximo->direccion }}
                                        @if ($establecimientoProximo->localidad)
                                            , {{ $establecimientoProximo->localidad }}
                                        @endif
                                    </div>
                                @endif
                                @if ($establecimientoProximo->telefono)
                                    <div class="text-muted small">
                                        <i class="bi bi-telephone me-1"></i>{{ $establecimientoProximo->telefono }}
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if ($materiaProxima)
                            <div class="mb-2">
                                <div class="text-muted small">Materia</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-book me-1 text-success"></i>
                                    {{ $materiaProxima->nombre }}
                                </div>
                            </div>
                        @endif
                        @if ($cursoProximo)
                            <div>
                                <div class="text-muted small">Curso</div>
                                <div class="fw-semibold">
                                    <i class="bi bi-people me-1 text-warning"></i>
                                    {{ $cursoProximo->nombre_completo }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            No hay próximas clases registradas.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Reloj, Calendario y Mapa --}}
        <div class="row g-3 mb-4">

            {{-- Card reloj --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                    <div class="card-body text-center py-4">
                        <div class="display-3 fw-bold" id="reloj">--:--:--</div>
                        <div class="fs-4 fw-semibold mt-1" id="diaSemana">-</div>
                        <div class="fs-5" id="fechaCompleta">-</div>
                    </div>
                </div>
            </div>

            {{-- Card calendario escolar --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">
                        <i class="bi bi-calendar-event me-1 text-primary"></i>Calendario escolar
                    </div>
                    <div class="card-body p-0">
                        @if ($proximosEventos->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                No hay eventos próximos.
                                <div class="mt-2">
                                    <a href="{{ route('calendarioescolar.create') }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Agregar evento
                                    </a>
                                </div>
                            </div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($proximosEventos as $index => $evento)
                                    @php
                                        $dias = [
                                            'Sunday' => 'Dom',
                                            'Monday' => 'Lun',
                                            'Tuesday' => 'Mar',
                                            'Wednesday' => 'Mié',
                                            'Thursday' => 'Jue',
                                            'Friday' => 'Vie',
                                            'Saturday' => 'Sáb',
                                        ];
                                        $diaEvento = $dias[$evento->fecha->format('l')] ?? '';
                                        $esHoy = $evento->fecha->isToday();
                                        $esManana = $evento->fecha->isTomorrow();
                                        $diasFaltan = now()
                                            ->startOfDay()
                                            ->diffInDays($evento->fecha->startOfDay());
                                    @endphp
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center
                                {{ $index === 0 ? 'border-start border-4 border-primary' : '' }}
                                {{ $evento->esferiado ? 'list-group-item-danger' : '' }}">
                                        <div>
                                            <div class="fw-semibold {{ $index === 0 ? 'text-primary' : '' }} small">
                                                @if ($evento->esferiado)
                                                    <i class="bi bi-calendar-x me-1 text-danger"></i>
                                                @else
                                                    <i class="bi bi-calendar-check me-1 text-primary"></i>
                                                @endif
                                                {{ $evento->denominacion }}
                                            </div>
                                            <div class="text-muted" style="font-size:0.75rem">
                                                {{ $diaEvento }} {{ $evento->fecha->format('d/m/Y') }}
                                                @if ($evento->periodo)
                                                    <span
                                                        class="badge bg-primary ms-1">{{ $evento->periodo->denominacion }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            @if ($esHoy)
                                                <span class="badge bg-danger">Hoy</span>
                                            @elseif($esManana)
                                                <span class="badge bg-warning text-dark">Mañana</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $diasFaltan }}d</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="p-2 text-end">
                                <a href="{{ route('calendarioescolar.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-calendar3 me-1"></i>Ver todo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card mapa del establecimiento actual --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">
                        <i class="bi bi-geo-alt me-1 text-danger"></i>Ubicación del establecimiento
                    </div>
                    <div class="card-body p-0">
                        @if ($establecimientoActual)
                            @php
                                $direccionMaps = urlencode(
                                    ($establecimientoActual->direccion ?? '') .
                                        ', ' .
                                        ($establecimientoActual->localidad ?? '') .
                                        ', ' .
                                        ($establecimientoActual->provincia ?? ''),
                                );
                            @endphp
                            <div class="p-3 border-bottom">
                                <div class="fw-semibold">{{ $establecimientoActual->nombre }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-geo me-1"></i>
                                    {{ $establecimientoActual->direccion ?? '—' }},
                                    {{ $establecimientoActual->localidad ?? '' }}
                                </div>
                                @if ($establecimientoActual->telefono)
                                    <div class="text-muted small">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $establecimientoActual->telefono }}
                                    </div>
                                @endif
                            </div>
                            <div style="height:200px;overflow:hidden">
                                <iframe width="100%" height="200" frameborder="0" style="border:0"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://maps.google.com/maps?q={{ $direccionMaps }}&output=embed&z=15"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="p-2 text-end">
                                <a href="https://maps.google.com/maps?q={{ $direccionMaps }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver en Google Maps
                                </a>
                            </div>
                        @elseif($establecimientoProximo)
                            @php
                                $direccionMaps = urlencode(
                                    ($establecimientoProximo->direccion ?? '') .
                                        ', ' .
                                        ($establecimientoProximo->localidad ?? '') .
                                        ', ' .
                                        ($establecimientoProximo->provincia ?? ''),
                                );
                            @endphp
                            <div class="p-3 border-bottom">
                                <div class="text-muted small mb-1">Próximo establecimiento</div>
                                <div class="fw-semibold">{{ $establecimientoProximo->nombre }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-geo me-1"></i>
                                    {{ $establecimientoProximo->direccion ?? '—' }},
                                    {{ $establecimientoProximo->localidad ?? '' }}
                                </div>
                                @if ($establecimientoProximo->telefono)
                                    <div class="text-muted small">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $establecimientoProximo->telefono }}
                                    </div>
                                @endif
                            </div>
                            <div style="height:200px;overflow:hidden">
                                <iframe width="100%" height="200" frameborder="0" style="border:0"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://maps.google.com/maps?q={{ $direccionMaps }}&output=embed&z=15"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="p-2 text-end">
                                <a href="https://maps.google.com/maps?q={{ $direccionMaps }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver en Google Maps
                                </a>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-geo-alt fs-2 d-block mb-2"></i>
                                No hay establecimiento activo ni próximo.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Fila 3a: Accesos rapidos --}}
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-person-check fs-4 text-primary"></i>
                            </div>
                            <h6 class="card-title mb-0">Asistencia</h6>
                        </div>
                        <p class="card-text text-muted small mb-2">Registra la asistencia diaria de tus alumnos.</p>
                        <button class="btn btn-sm btn-outline-primary" onclick="accesoRapidoModulo('asistencia')">
                            Ir al modulo
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-journal-text fs-4 text-success"></i>
                            </div>
                            <h6 class="card-title mb-0">Calificaciones</h6>
                        </div>
                        <p class="card-text text-muted small mb-2">Carga y consulta las notas de tus alumnos.</p>
                        <button class="btn btn-sm btn-outline-success" onclick="accesoRapidoModulo('calificaciones')">
                            Ir al modulo
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-clipboard-check fs-4 text-warning"></i>
                            </div>
                            <h6 class="card-title mb-0">Practicos</h6>
                        </div>
                        <p class="card-text text-muted small mb-2">Asigna practicos y revisa las entregas.</p>
                        <button class="btn btn-sm btn-outline-warning" onclick="accesoRapidoModulo('practicos')">
                            Ir al modulo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fila 3b --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-calendar3 fs-4 text-info"></i>
                            </div>
                            <h6 class="card-title mb-0">Horarios</h6>
                        </div>
                        <p class="card-text text-muted small mb-2">Planifica y consulta tu horario semanal.</p>
                        <a href="{{ route('horarios.index') }}" class="btn btn-sm btn-outline-info">Ir al modulo</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-secondary bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-file-earmark-text fs-4 text-secondary"></i>
                            </div>
                            <h6 class="card-title mb-0">Declaracion jurada</h6>
                        </div>
                        <p class="card-text text-muted small mb-2">Completa y envia tu declaracion jurada de horarios.</p>
                        <a href="{{ route('declaracion.index') }}" class="btn btn-sm btn-outline-secondary">Ir al
                            modulo</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-danger bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-chat-dots fs-4 text-danger"></i>
                            </div>
                            <h6 class="card-title mb-0">Comunicacion</h6>
                        </div>
                        <p class="card-text text-muted small mb-2">Envia mensajes a alumnos y familias.</p>
                        <a href="{{ route('comunicacion.index') }}" class="btn btn-sm btn-outline-danger">Ir al
                            modulo</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card clase actual con estadisticas --}}
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

            let statsCache = {};
            let graficosInstanciados = {};
            let claseActivaActual = null;
            let claveClaseAnterior = 'INICIAL';

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

                // Solo actualizar leyenda y stats si cambió la clase activa
                const claveActual = claseActivaActual
                    ? `${claseActivaActual.curso_id}_${claseActivaActual.materia_id}`
                    : 'ninguna';

                actualizarLeyenda(clases);

                if (claveActual !== claveClaseAnterior) {
                    claveClaseAnterior = claveActual;
                    renderClaseActual(clases);
                }
            }

            actualizarReloj();
            setInterval(actualizarReloj, 1000);

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

                clases.forEach(clase => {
                    const cacheKey = `${clase.curso_id}_${clase.materia_id}`;

                    if (statsCache[cacheKey]) {
                        renderTablaConStats(container, clase, statsCache[cacheKey]);
                        return;
                    }

                    // Mostrar loading
                    container.innerHTML = `
            <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
                <div class="card-body text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 text-muted">Cargando estadísticas...</div>
                </div>
            </div>`;

                    fetch(`/api/dashboard/stats/${clase.curso_id}/${clase.materia_id}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin'
                        })
                        .then(r => r.text())
                        .then(text => {
                            // Eliminar cualquier output de PHP antes del JSON
                            const jsonStart = text.indexOf('{');
                            if (jsonStart === -1) {
                                console.error('Respuesta invalida:', text.substring(0, 200));
                                throw new Error('Sin JSON en respuesta');
                            }
                            return JSON.parse(text.substring(jsonStart));
                        })
                        .then(data => {
                            statsCache[cacheKey] = data;
                            renderTablaConStats(container, clase, data);
                            graficosInstanciados[cacheKey] = true;
                        })
                        .catch(err => {
                            console.error('Error cargando estadisticas:', err);
                            container.innerHTML = `
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center py-3">
                <button class="btn btn-outline-primary btn-sm" onclick="statsCache={}; graficosInstanciados={}; claveClaseAnterior='INICIAL'; renderClaseActual(clasesActuales(diasSemana[new Date().getDay()], new Date().getHours()*60+new Date().getMinutes()))">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar cargar estadisticas
                </button>
            </div>
        </div>`;
                        });
                }); // fin clases.forEach
            } // fin renderClaseActual

            function renderTablaConStats(container, clase, data) {
                const alumnos = data.alumnos;
                const graficos = data.graficos;

                let html = `
        <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
            <div class="card-body">
                <div class="fw-semibold mb-3">
                    <i class="bi bi-people me-1"></i>Estudiantes — ${clase.materia ?? ''} ${clase.curso ?? ''}
                </div>`;

                if (alumnos.length > 0) {
                    html += `
            <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Alumno</th>
                        <th class="text-center" title="Presentes"><span class="badge bg-success">Pres.</span></th>
                        <th class="text-center" title="Ausentes"><span class="badge bg-danger">Aus.</span></th>
                        <th class="text-center" title="Justificados"><span class="badge bg-info">Just.</span></th>
                        <th class="text-center" title="Actividades asignadas"><span class="badge bg-secondary">Act.</span></th>
                        <th class="text-center" title="Entregadas a tiempo"><span class="badge bg-success">Entr.</span></th>
                        <th class="text-center" title="Entregadas vencidas"><span class="badge bg-warning text-dark">Venc.</span></th>
                        <th class="text-center">Última valoración</th>
                        <th class="text-center">Última nota</th>
                    </tr>
                </thead>
                <tbody>`;

                    alumnos.forEach((a, idx) => {
                        let notaColor = 'secondary';
                        if (a.ultimaNota !== null) {
                            notaColor = a.ultimaNota >= 7 ? 'success' : (a.ultimaNota >= 4 ? 'warning' : 'danger');
                        }

                        html += `
                <tr>
                    <td class="ps-3 text-muted">${idx + 1}</td>
                    <td class="fw-semibold">${a.nombre}</td>
                    <td class="text-center"><span class="badge bg-success">${a.presentes}</span></td>
                    <td class="text-center"><span class="badge bg-danger">${a.ausentes}</span></td>
                    <td class="text-center"><span class="badge bg-info">${a.justificados}</span></td>
                    <td class="text-center"><span class="badge bg-secondary">${a.asignadas}</span></td>
                    <td class="text-center"><span class="badge bg-success">${a.entregadas}</span></td>
                    <td class="text-center"><span class="badge bg-warning text-dark">${a.vencidas}</span></td>
                    <td class="text-center">
                        ${a.ultimaValoracion !== '—'
                            ? `<span class="badge bg-primary">${a.ultimaValoracion}</span>`
                            : '<span class="text-muted small">—</span>'
                        }
                    </td>
                    <td class="text-center">
                        ${a.ultimaNota !== null
                            ? `<span class="badge bg-${notaColor} fs-6">${parseFloat(a.ultimaNota).toFixed(2)}</span>`
                            : '<span class="text-muted small">—</span>'
                        }
                    </td>
                </tr>`;
                    });

                    html += `
                </tbody>
            </table>
            </div>`;
                } else {
                    html += `<div class="text-muted small">No hay alumnos registrados en este curso.</div>`;
                }

                html += `</div></div>`;

                // Gráficos
                html += `
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold small">
                    <i class="bi bi-pie-chart me-1 text-primary"></i>Distribución de notas
                </div>
                <div class="card-body" style="height:220px">
                    <canvas id="chartDistribucion"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold small">
                    <i class="bi bi-graph-up me-1 text-success"></i>Tendencia de asistencias
                </div>
                <div class="card-body" style="height:220px">
                    <canvas id="chartAsistencias"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold small">
                    <i class="bi bi-graph-up-arrow me-1 text-warning"></i>Tendencia de aprobación
                </div>
                <div class="card-body" style="height:220px">
                    <canvas id="chartCierres"></canvas>
                </div>
            </div>
        </div>
    </div>`;

                container.innerHTML = html;

                // Inicializar gráficos
                setTimeout(() => {
                    // 1. Distribución de notas (dona)
                    const dist = graficos.distribucion;
                    new Chart(document.getElementById('chartDistribucion'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Aprobados ≥7', 'Regulares 4-6', 'Reprobados <4', 'Sin nota'],
                            datasets: [{
                                data: [dist.aprobados, dist.regulares, dist.reprobados, dist.sinNota],
                                backgroundColor: ['#198754', '#ffc107', '#dc3545', '#adb5bd'],
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // 2. Tendencia de asistencias (línea)
                    const asist = graficos.asistencias;
                    new Chart(document.getElementById('chartAsistencias'), {
                        type: 'line',
                        data: {
                            labels: asist.map(a => a.fecha),
                            datasets: [{
                                label: '% Asistencia',
                                data: asist.map(a => a.porcentaje),
                                borderColor: '#198754',
                                backgroundColor: 'rgba(25,135,84,0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    min: 0,
                                    max: 100,
                                    ticks: {
                                        callback: v => v + '%'
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });

                    // 3. Tendencia de aprobación por cierre (barras)
                    const cierres = graficos.cierres;
                    new Chart(document.getElementById('chartCierres'), {
                        type: 'bar',
                        data: {
                            labels: cierres.map(c => c.label),
                            datasets: [{
                                label: 'Promedio',
                                data: cierres.map(c => c.promedio),
                                backgroundColor: cierres.map(c =>
                                    c.promedio >= 7 ? '#198754' :
                                    c.promedio >= 4 ? '#ffc107' : '#dc3545'
                                ),
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    min: 0,
                                    max: 10,
                                    ticks: {
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }, 100);
            }

            function accesoRapidoModulo(modulo) {
                if (modulo === 'asistencia') {
                    if (claseActivaActual) {
                        // Hay clase activa: ir directo al registro de asistencia con materia y curso
                        const fecha = new Date();
                        const hoy = `${fecha.getFullYear()}-${pad(fecha.getMonth()+1)}-${pad(fecha.getDate())}`;
                        window.location.href =
                            `/asistencia/registrar?curso_id=${claseActivaActual.curso_id}&materia_id=${claseActivaActual.materia_id}&fecha=${hoy}`;
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
                        'practicos': '/tareas',
                    };
                    window.location.href = rutas[modulo] ?? '/dashboard';
                }
            }

            function mostrarModalSinClase() {
                const modalEl = document.getElementById('modalSinClase');
                new bootstrap.Modal(modalEl).show();
            }

        </script>
    @endpush
