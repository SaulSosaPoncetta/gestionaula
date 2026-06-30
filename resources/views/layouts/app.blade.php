<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gestión Aula') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>Gestión Aula
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        {{-- Actividad áulica --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('asistencia.*') || request()->routeIs('calificaciones.*') ? 'active' : '' }}"
                                href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar2-check me-1"></i>Actividad áulica
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('asistencia.*') ? 'active' : '' }}"
                                        href="{{ route('asistencia.index') }}">
                                        <i class="bi bi-person-check me-2"></i>Asistencia
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('asignaractividad.*') ? 'active' : '' }}"
                                        href="{{ route('asignaractividad.seleccionar') }}">
                                        <i class="bi bi-journal me-2"></i>Act. Asignadas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('calificaractividad.*') ? 'active' : '' }}"
                                        href="{{ route('calificaractividad.index') }}">
                                        <i class="bi bi-journal-check me-2"></i>Calificar actividades
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('calificaciones.*') ? 'active' : '' }}"
                                        href="{{ route('calificaciones.index') }}">
                                        <i class="bi bi-journal-text me-2"></i>Calificar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('prenotas.*') ? 'active' : '' }}"
                                        href="{{ route('prenotas.index') }}">
                                        <i class="bi bi-calculator me-2"></i>Prenotas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('cierre_cuatri.*') ? 'active' : '' }}"
                                        href="{{ route('cierre_cuatri.index') }}">
                                        <i class="bi bi-calculator me-2"></i>Cierre de notas
                                    </a>
                                </li>


                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('librotemas.*') ? 'active' : '' }}"
                                        href="{{ route('librotemas.index') }}">
                                        <i class="bi bi-journal-bookmark-fill me-2"></i>Libro de temas
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pdf.index') }}">
                                        <i class="bi bi-printer me-1"></i>Informes
                                    </a>
                                    <a class="dropdown-item" href="{{ route('excel.index') }}">
                                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Contenidos --}}


                        {{-- Material Pedagógico --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('tareas.*') || request()->routeIs('materialteoricoarchivos.*') ? 'active' : '' }}"
                                href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-folder2-open me-1"></i>Material pedagógico
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('contenidos.*') ? 'active' : '' }}"
                                        href="{{ route('contenidos.index') }}">
                                        <i class="bi bi-journal-richtext me-1"></i>Contenidos
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('materialteoricoarchivos.*') ? 'active' : '' }}"
                                        href="{{ route('materialteoricoarchivos.index') }}">
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Material teórico
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('tiposactividad.*') ? 'active' : '' }}"
                                        href="{{ route('tiposactividad.index') }}">
                                        <i class="bi bi-activity me-2"></i>Tipos de actividad
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('actividades.*') ? 'active' : '' }}"
                                        href="{{ route('actividades.index') }}">
                                        <i class="bi bi-clipboard2-check me-2"></i>Actividades
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('asignarnuevo.*') ? 'active' : '' }}"
                                        href="{{ route('asignarnuevo.index') }}">
                                        <i class="bi bi-journal me-2"></i>Asignar Act.
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('proyectos.*') ? 'active' : '' }}"
                                        href="{{ route('proyectos.index') }}">
                                        <i class="bi bi-folder2-open me-2"></i>Proyectos
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Administración --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('cursos.*') ||
                            request()->routeIs('materias.*') ||
                            request()->routeIs('alumnos.*') ||
                            request()->routeIs('niveles.*') ||
                            request()->routeIs('establecimientos.*') ||
                            request()->routeIs('ciclos.*') ||
                            request()->routeIs('areasformacion.*') ||
                            request()->routeIs('especialidades.*') ||
                            request()->routeIs('horarios.*') ||
                            request()->routeIs('declaracion.*') ||
                            request()->routeIs('planificaciones.*')
                                ? 'active'
                                : '' }}"
                                href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i>Administración
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('ciclos_lectivos.index') }}">
                                        <i class="bi bi-calendar2-range me-1"></i>Ciclos lectivos
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('calendarioescolar.*') ? 'active' : '' }}"
                                        href="{{ route('calendarioescolar.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Calendario escolar
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Académico</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('periodos.*') ? 'active' : '' }}"
                                        href="{{ route('periodos.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Períodos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('cursos.*') ? 'active' : '' }}"
                                        href="{{ route('cursos.index') }}">
                                        <i class="bi bi-building me-2"></i>Cursos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('materias.*') ? 'active' : '' }}"
                                        href="{{ route('materias.index') }}">
                                        <i class="bi bi-book me-2"></i>Materias
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('alumnos.*') ? 'active' : '' }}"
                                        href="{{ route('alumnos.index') }}">
                                        <i class="bi bi-people me-2"></i>Alumnos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('planificaciones.*') ? 'active' : '' }}"
                                        href="{{ route('planificaciones.index') }}">
                                        <i class="bi bi-journal-bookmark me-2"></i>Planificación
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Horarios</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('designaciones.*') ? 'active' : '' }}"
                                        href="{{ route('designaciones.index') }}">
                                        <i class="bi bi-file-earmark-person me-2"></i>Designaciones
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('horarios.*') ? 'active' : '' }}"
                                        href="{{ route('horarios.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Horarios
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('declaracion.*') ? 'active' : '' }}"
                                        href="{{ route('declaracion.index') }}">
                                        <i class="bi bi-file-earmark-text me-2"></i>Declaración jurada
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('ceses.*') ? 'active' : '' }}"
                                        href="{{ route('ceses.index') }}">
                                        <i class="bi bi-calendar-x me-2"></i>Ceses
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Institucional</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('niveles.*') ? 'active' : '' }}"
                                        href="{{ route('niveles.index') }}">
                                        <i class="bi bi-diagram-3 me-2"></i>Niveles educativos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('establecimientos.*') ? 'active' : '' }}"
                                        href="{{ route('establecimientos.index') }}">
                                        <i class="bi bi-building me-2"></i>Establecimientos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('periodos.*') ? 'active' : '' }}"
                                        href="{{ route('periodos.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Períodos
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <h6 class="dropdown-header">Evaluación</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('tiposevaluacion.*') ? 'active' : '' }}"
                                        href="{{ route('tiposevaluacion.index') }}">
                                        <i class="bi bi-card-checklist me-2"></i>Tipos de evaluación
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('tipovaloraciones.*') ? 'active' : '' }}"
                                        href="{{ route('tipovaloraciones.index') }}">
                                        <i class="bi bi-star me-2"></i>Tipos de valoración
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Clasificación</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('ciclos.*') ? 'active' : '' }}"
                                        href="{{ route('ciclos.index') }}">
                                        <i class="bi bi-arrow-repeat me-2"></i>Ciclos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('areasformacion.*') ? 'active' : '' }}"
                                        href="{{ route('areasformacion.index') }}">
                                        <i class="bi bi-collection me-2"></i>Áreas de formación
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('especialidades.*') ? 'active' : '' }}"
                                        href="{{ route('especialidades.index') }}">
                                        <i class="bi bi-star me-2"></i>Especialidades
                                    </a>
                                </li>

                            </ul>

                        </li>


                        {{-- Comunicación --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('comunicacion.*') ? 'active' : '' }}"
                                href="{{ route('comunicacion.index') }}">
                                <i class="bi bi-chat-dots me-1"></i>Comunicación
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}"
                                href="{{ route('pagos.index') }}">
                                <i class="bi bi-credit-card me-2"></i>Suscripciones
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text text-muted small">
                                        <i class="bi bi-envelope me-1"></i>{{ auth()->user()->email }}
                                    </span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-top mt-auto py-3">
        <div class="container text-center text-muted small">
            <i class="bi bi-mortarboard me-1"></i>Sistema de Gestión de Aula &copy; {{ date('Y') }}
        </div>
    </footer>

    @include('partials.modal-confirm')

    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</body>

</html>
