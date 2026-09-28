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
                    @php
                        try {
                            $onbPaso = auth()->user()->hasRole('admin') ? 99 : auth()->user()->onboardingStep();
                        } catch (\Throwable $e) {
                            $onbPaso = 99;
                        }
                        $bloq = $onbPaso < 3;
                    @endphp
                        {{-- Actividad áulica --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('asistencia.*') || request()->routeIs('calificaciones.*') ? 'active' : '' }}"
                                href="#" {{ $bloq ? '' : 'data-bs-toggle=dropdown' }}>
                                <i class="bi bi-calendar2-check me-1"></i>Actividad áulica
                                @if($bloq)<i class="bi bi-lock-fill ms-1 small"></i>@endif
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
                                    <a class="dropdown-item {{ request()->routeIs('pdf.*') ? 'active' : '' }}"
                                        href="{{ route('pdf.index') }}">
                                        <i class="bi bi-calculator me-2"></i>Informes
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
                                {{-- Ciclo Lectivo: siempre visible (paso 0) --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('ciclos_lectivos.index') }}">
                                        <i class="bi bi-calendar2-range me-1"></i>Ciclos lectivos
                                        @if($onbPaso == 0)<span class="badge bg-primary ms-1">Comenzar aquí</span>@endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>

                                {{-- Items bloqueados en paso 0 --}}
                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('calendarioescolar.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('calendarioescolar.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Calendario escolar
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Académico</h6></li>
                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('periodos.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('periodos.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Períodos
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('cursos.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('cursos.index') }}">
                                        <i class="bi bi-building me-2"></i>Cursos
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('materias.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('materias.index') }}">
                                        <i class="bi bi-book me-2"></i>Materias
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('alumnos.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('alumnos.index') }}">
                                        <i class="bi bi-people me-2"></i>Alumnos
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('planificaciones.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('planificaciones.index') }}">
                                        <i class="bi bi-journal-bookmark me-2"></i>Planificación
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Horarios</h6></li>

                                {{-- Designaciones: habilitada desde paso 1 --}}
                                <li>
                                    @if($onbPaso >= 1)
                                    <a class="dropdown-item {{ request()->routeIs('designaciones.*') ? 'active' : '' }}"
                                        href="{{ route('designaciones.index') }}">
                                        <i class="bi bi-file-earmark-person me-2"></i>Designaciones
                                        @if($onbPaso == 1)<span class="badge bg-primary ms-1">Paso 2</span>@endif
                                    </a>
                                    @else
                                    <a class="dropdown-item disabled opacity-50" href="#">
                                        <i class="bi bi-file-earmark-person me-2"></i>Designaciones
                                        <i class="bi bi-lock-fill ms-1 small text-muted"></i>
                                    </a>
                                    @endif
                                </li>

                                {{-- Horarios: habilitada desde paso 2 --}}
                                <li>
                                    @if($onbPaso >= 2)
                                    <a class="dropdown-item {{ request()->routeIs('horarios.*') ? 'active' : '' }}"
                                        href="{{ route('horarios.index') }}">
                                        <i class="bi bi-calendar3 me-2"></i>Horarios
                                        @if($onbPaso == 2)<span class="badge bg-primary ms-1">Paso 3</span>@endif
                                    </a>
                                    @else
                                    <a class="dropdown-item disabled opacity-50" href="#">
                                        <i class="bi bi-calendar3 me-2"></i>Horarios
                                        <i class="bi bi-lock-fill ms-1 small text-muted"></i>
                                    </a>
                                    @endif
                                </li>

                                <li>
                                    <a class="dropdown-item {{ $bloq ? 'disabled opacity-50' : '' }} {{ request()->routeIs('declaracion.*') ? 'active' : '' }}"
                                        href="{{ $bloq ? '#' : route('declaracion.index') }}">
                                        <i class="bi bi-file-earmark-text me-2"></i>Declaración jurada
                                        @if($bloq)<i class="bi bi-lock-fill ms-1 small text-muted"></i>@endif
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

        {{-- Aviso de ruta bloqueada por onboarding --}}
        @if (session('onboarding_bloqueado'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-lock me-2"></i><strong>Sección no disponible:</strong>
                {{ session('onboarding_bloqueado') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Banner de onboarding para docentes nuevos --}}
        @auth
        @if(!auth()->user()->hasRole('admin'))
        @php
            try {
                $onbStepBanner = auth()->user()->onboardingStep();
                $onbCompleto   = $onbStepBanner >= 3;
            } catch (\Throwable $e) {
                $onbStepBanner = 3;
                $onbCompleto   = true;
            }
        @endphp
        @if(!$onbCompleto)
        @php $paso = $onbStepBanner; @endphp
        <div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #0d6efd!important">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="bi bi-rocket-takeoff-fill text-primary fs-3"></i>
                    <div>
                        <div class="fw-bold">Configuración inicial — Paso {{ $paso + 1 }} de 3</div>
                        <div class="text-muted small">Completá estos pasos para habilitar todas las funciones</div>
                    </div>
                </div>

                {{-- Barra de progreso --}}
                <div class="progress mb-3" style="height:8px">
                    <div class="progress-bar bg-primary" style="width:{{ ($paso / 3) * 100 }}%"></div>
                </div>

                <div class="row g-2">
                    {{-- Paso 1: Ciclo Lectivo --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2 p-2 rounded
                            {{ $paso >= 1 ? 'bg-success bg-opacity-10' : 'bg-primary bg-opacity-10' }}">
                            <span class="badge {{ $paso >= 1 ? 'bg-success' : 'bg-primary' }} rounded-circle"
                                  style="width:28px;height:28px;line-height:20px;text-align:center">
                                {{ $paso >= 1 ? '✓' : '1' }}
                            </span>
                            <div>
                                <div class="small fw-semibold">Ciclo Lectivo</div>
                                @if($paso == 0)
                                    <a href="{{ route('ciclos_lectivos.create') }}"
                                       class="btn btn-primary btn-sm py-0 px-2 mt-1">
                                        Crear ahora →
                                    </a>
                                @else
                                    <span class="text-success small">Completado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Paso 2: Designaciones --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2 p-2 rounded
                            {{ $paso >= 2 ? 'bg-success bg-opacity-10' : ($paso >= 1 ? 'bg-primary bg-opacity-10' : 'bg-light') }}">
                            <span class="badge {{ $paso >= 2 ? 'bg-success' : ($paso >= 1 ? 'bg-primary' : 'bg-secondary') }} rounded-circle"
                                  style="width:28px;height:28px;line-height:20px;text-align:center">
                                {{ $paso >= 2 ? '✓' : '2' }}
                            </span>
                            <div>
                                <div class="small fw-semibold {{ $paso < 1 ? 'text-muted' : '' }}">Designaciones SAD</div>
                                @if($paso == 1)
                                    <a href="{{ route('designaciones.create') }}"
                                       class="btn btn-primary btn-sm py-0 px-2 mt-1">
                                        Cargar ahora →
                                    </a>
                                @elseif($paso >= 2)
                                    <span class="text-success small">Completado</span>
                                @else
                                    <span class="text-muted small">Bloqueado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Paso 3: Horarios --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2 p-2 rounded
                            {{ $paso >= 3 ? 'bg-success bg-opacity-10' : ($paso >= 2 ? 'bg-primary bg-opacity-10' : 'bg-light') }}">
                            <span class="badge {{ $paso >= 3 ? 'bg-success' : ($paso >= 2 ? 'bg-primary' : 'bg-secondary') }} rounded-circle"
                                  style="width:28px;height:28px;line-height:20px;text-align:center">
                                {{ $paso >= 3 ? '✓' : '3' }}
                            </span>
                            <div>
                                <div class="small fw-semibold {{ $paso < 2 ? 'text-muted' : '' }}">Horarios</div>
                                @if($paso == 2)
                                    <a href="{{ route('horarios.create') }}"
                                       class="btn btn-primary btn-sm py-0 px-2 mt-1">
                                        Configurar →
                                    </a>
                                @elseif($paso >= 3)
                                    <span class="text-success small">Completado</span>
                                @else
                                    <span class="text-muted small">Bloqueado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
        @endauth

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
