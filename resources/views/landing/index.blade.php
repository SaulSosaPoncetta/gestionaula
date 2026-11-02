<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestiónAula — Sistema de gestión para docentes</title>
    <meta name="description" content="Asistencia, calificaciones, actividades, horarios y proyectos en un solo lugar. Web, app instalable y versión de escritorio para docentes.">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="GestiónAula">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" href="/icons/icon-192x192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --ga-azul: #0d6efd;
            --ga-azul-hover: #0a58ca;
            --ga-profundo: #0f3460;
            --ga-noche: #1a1a2e;
            --ga-claro: #74b9ff;
            --ga-suave: #e8f0fe;
            --ga-fondo: #f5f8ff;
            --ga-pizarra: #1e293b;
        }
        html { scroll-behavior: smooth; scroll-padding-top: 76px; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, Roboto, sans-serif; color: var(--ga-pizarra); background: #fff; }

        /* Navbar */
        .landing-nav { background: rgba(255,255,255,.96); backdrop-filter: blur(10px); box-shadow: 0 2px 20px rgba(0,0,0,.07); }
        .landing-nav .navbar-brand { color: var(--ga-azul); }
        .landing-nav .nav-link { color: var(--ga-pizarra); font-weight: 600; }
        .landing-nav .nav-link:hover { color: var(--ga-azul); }

        /* Botones */
        .btn-landing { background: var(--ga-azul); color: #fff; border: 0; border-radius: 50px; padding: 13px 30px; font-weight: 600; text-decoration: none; transition: all .25s; display: inline-block; }
        .btn-landing:hover { background: var(--ga-azul-hover); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,110,253,.35); }
        .btn-landing-outline { background: transparent; color: #fff; border: 2px solid rgba(255,255,255,.35); border-radius: 50px; padding: 12px 30px; font-weight: 600; text-decoration: none; transition: all .25s; display: inline-block; }
        .btn-landing-outline:hover { border-color: #fff; background: rgba(255,255,255,.1); color: #fff; }

        /* Hero */
        .landing-hero { background: linear-gradient(135deg, var(--ga-noche) 0%, #16213e 50%, var(--ga-profundo) 100%); padding: 130px 0 90px; position: relative; overflow: hidden; }
        .landing-hero::before { content: ''; position: absolute; width: 600px; height: 600px; top: -200px; right: -200px; background: radial-gradient(circle, rgba(13,110,253,.18) 0%, transparent 70%); }
        .landing-hero::after { content: ''; position: absolute; width: 400px; height: 400px; bottom: -120px; left: -100px; background: radial-gradient(circle, rgba(13,110,253,.12) 0%, transparent 70%); }
        .landing-badge { display: inline-block; background: rgba(13,110,253,.2); border: 1px solid rgba(13,110,253,.45); color: var(--ga-claro); padding: 6px 16px; border-radius: 20px; font-size: 14px; margin-bottom: 22px; }
        .landing-hero h1 { font-size: clamp(2.2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1.1; color: #fff; }
        .landing-hero h1 span { color: var(--ga-claro); }

        /* Mock del hero */
        .mock { background: #fff; border-radius: 16px; box-shadow: 0 30px 70px rgba(0,0,0,.45); overflow: hidden; text-align: left; color: var(--ga-pizarra); }
        .mock-bar { background: #f1f4f9; padding: 10px 14px; display: flex; gap: 6px; align-items: center; border-bottom: 1px solid #e3e8f0; }
        .mock-bar i { width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1; display: inline-block; }
        .mock-bar small { margin-left: 10px; color: #64748b; font-size: 12px; }
        .mock-body { padding: 18px; }
        .mock table { width: 100%; font-size: 13px; }
        .mock th { color: #64748b; font-weight: 600; font-size: 11px; text-transform: uppercase; padding: 6px 4px; }
        .mock td { padding: 7px 4px; border-top: 1px solid #eef1f6; }
        .pill { display: inline-block; min-width: 26px; text-align: center; border-radius: 6px; font-size: 11px; font-weight: 700; padding: 2px 6px; }
        .pill-p { background: #d1f5df; color: #146c43; }
        .pill-a { background: #fde0e0; color: #b02a37; }
        .pill-t { background: #fff0c7; color: #8a6100; }
        .pill-j { background: var(--ga-suave); color: #0a58ca; }
        .mock-caption { font-size: 12px; color: rgba(255,255,255,.55); margin-top: 12px; }

        /* Secciones */
        .landing-section { padding: 80px 0; }
        .section-tag { display: inline-block; background: rgba(13,110,253,.1); color: var(--ga-azul); padding: 7px 16px; border-radius: 50px; font-weight: 600; font-size: 14px; margin-bottom: 14px; }
        .bg-ga-fondo { background: var(--ga-fondo); }

        .persona-card { background: #fff; border: 1px solid #e3e9f5; border-radius: 16px; padding: 28px; height: 100%; }
        .persona-card i { font-size: 30px; color: var(--ga-azul); }

        .feature-card { padding: 30px; border-radius: 16px; border: 1px solid #e9ecef; background: #fff; height: 100%; transition: all .3s; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(13,110,253,.12); border-color: var(--ga-azul); }
        .feature-icon { width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg, var(--ga-suave), #d2e3fc); color: var(--ga-azul); display: flex; align-items: center; justify-content: center; font-size: 27px; margin-bottom: 16px; }

        .paso-num { width: 52px; height: 52px; border-radius: 50%; background: var(--ga-azul); color: #fff; font-weight: 800; font-size: 22px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }

        /* Sección oscura: instalar */
        .landing-dark { background: linear-gradient(135deg, var(--ga-noche) 0%, var(--ga-profundo) 100%); color: #fff; }
        .install-card { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); border-radius: 16px; padding: 24px; height: 100%; }
        .install-card i.big { font-size: 34px; color: var(--ga-claro); }

        /* Planes */
        .plan-card { border-radius: 20px; border: 2px solid #e3e9f5; background: #fff; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: all .3s; }
        .plan-card:hover { transform: translateY(-6px); box-shadow: 0 20px 50px rgba(15,52,96,.14); }
        .plan-card.featured { border-color: var(--ga-azul); }
        .plan-card.featured .plan-header { background: var(--ga-azul); color: #fff; }
        .plan-header { padding: 24px; background: var(--ga-fondo); }
        .plan-price { font-size: 2.6rem; font-weight: 800; line-height: 1; }

        /* FAQ */
        .accordion-button:not(.collapsed) { background: var(--ga-suave); color: var(--ga-profundo); box-shadow: none; }
        .accordion-button:focus { box-shadow: 0 0 0 .2rem rgba(13,110,253,.2); }

        /* Contacto */
        .contact-form { background: #fff; border-radius: 20px; padding: 36px; box-shadow: 0 4px 30px rgba(0,0,0,.06); }
        .contact-form .form-control { border-radius: 10px; border: 2px solid #e9ecef; }
        .contact-form .form-control:focus { border-color: var(--ga-azul); box-shadow: 0 0 0 4px rgba(13,110,253,.1); }

        /* CTA final y footer */
        .landing-cta { background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: #fff; }
        .landing-footer { background: var(--ga-noche); }

        @media (max-width: 575.98px) {
            .landing-section { padding: 56px 0; }
            .contact-form { padding: 22px; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg landing-nav fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="#inicio">
            <i class="bi bi-mortarboard-fill me-2"></i>GestiónAula
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLanding" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navLanding">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#para-quien">Para quién</a></li>
                <li class="nav-item"><a class="nav-link" href="#funciones">Funciones</a></li>
                <li class="nav-item"><a class="nav-link" href="#como-funciona">Cómo funciona</a></li>
                <li class="nav-item"><a class="nav-link" href="#instalar">Instalar</a></li>
                <li class="nav-item"><a class="nav-link" href="#planes">Planes</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">Preguntas</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
            </ul>
            <div class="d-flex gap-2 mt-3 mt-lg-0">
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                </a>
                <a href="#planes" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-rocket me-1"></i>Suscribirme
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- Alertas de sesión --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow"
     style="z-index:9999;width:min(92vw,520px)">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow"
     style="z-index:9999;width:min(92vw,520px)">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- HERO --}}
<section id="inicio" class="landing-hero">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="landing-badge"><i class="bi bi-mortarboard me-1"></i>Sistema de gestión para docentes</span>
                <h1 class="mb-4">Tu aula, <span>ordenada</span> en un solo lugar</h1>
                <p class="text-white-50 fs-5 mb-4">
                    Asistencia, calificaciones, actividades, horarios, prenotas y proyectos
                    sin planillas sueltas ni cuadernos. Desde el celular, la compu o la app de escritorio.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#planes" class="btn-landing"><i class="bi bi-rocket me-2"></i>Ver planes</a>
                    <a href="#funciones" class="btn-landing-outline"><i class="bi bi-grid me-2"></i>Ver funciones</a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4 text-white-50 small">
                    <span><i class="bi bi-phone me-1"></i>Instalable en el celular</span>
                    <span><i class="bi bi-wifi-off me-1"></i>Funciona sin conexión</span>
                    <span><i class="bi bi-windows me-1"></i>App para Windows</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mock">
                    <div class="mock-bar"><i></i><i></i><i></i><small>GestiónAula · Asistencia</small></div>
                    <div class="mock-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-bold">3.º B · Matemática</div>
                                <div class="text-muted small">Registro del día</div>
                            </div>
                            <span class="pill pill-j">4 de 5 cargados</span>
                        </div>
                        <table>
                            <thead>
                                <tr><th>Alumno</th><th class="text-center">Hoy</th><th class="text-center">Mes</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Alumno/a 1</td><td class="text-center"><span class="pill pill-p">P</span></td><td class="text-center text-muted">96%</td></tr>
                                <tr><td>Alumno/a 2</td><td class="text-center"><span class="pill pill-a">A</span></td><td class="text-center text-muted">81%</td></tr>
                                <tr><td>Alumno/a 3</td><td class="text-center"><span class="pill pill-t">T</span></td><td class="text-center text-muted">90%</td></tr>
                                <tr><td>Alumno/a 4</td><td class="text-center"><span class="pill pill-j">J</span></td><td class="text-center text-muted">88%</td></tr>
                                <tr><td>Alumno/a 5</td><td class="text-center"><span class="pill pill-p">P</span></td><td class="text-center text-muted">100%</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mock-caption text-center">Vista de ejemplo con datos ficticios.</div>
            </div>
        </div>
    </div>
</section>

{{-- PARA QUIÉN --}}
<section id="para-quien" class="landing-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Para quién es</span>
            <h2 class="fw-bold fs-1 mb-3">Pensado para el trabajo real del docente</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:640px">
                Si hoy manejás tu curso entre cuadernos, planillas y mensajes, GestiónAula lo junta todo.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-person-workspace', 'Docentes de varios cursos', 'Llevá cada curso y cada materia por separado, con sus alumnos, notas y asistencia.'],
                ['bi-buildings', 'Docentes en más de un establecimiento', 'Organizá tus horarios y el sistema te muestra en qué clase estás y cuál sigue.'],
                ['bi-journal-bookmark', 'Quienes planifican y evalúan', 'Actividades, proyectos y prenotas calculadas con las fuentes que vos elegís.'],
            ] as $p)
            <div class="col-md-4">
                <div class="persona-card">
                    <i class="bi {{ $p[0] }} d-block mb-3"></i>
                    <h5 class="fw-bold">{{ $p[1] }}</h5>
                    <p class="text-muted mb-0">{{ $p[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FUNCIONES --}}
<section id="funciones" class="landing-section bg-ga-fondo">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Funciones</span>
            <h2 class="fw-bold fs-1 mb-3">Todo lo que un docente necesita</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:640px">
                Reemplaza planillas en papel, cuadernos y hojas de cálculo con un sistema simple de usar.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-person-check', 'Asistencia digital', 'Registrá presente, ausente, tarde y justificado. El sistema calcula porcentajes y alertas automáticamente.'],
                ['bi-journal-text', 'Calificaciones', 'Notas por período y tipo de evaluación, con el historial completo de cada alumno.'],
                ['bi-clipboard2-check', 'Actividades pedagógicas', 'Creá actividades, asignalas por curso, armá grupos y calificá desde un solo lugar.'],
                ['bi-calendar3', 'Horarios inteligentes', 'Detecta en qué clase estás y te muestra la próxima, incluso entre establecimientos.'],
                ['bi-calculator', 'Prenotas automáticas', 'Calculá prenotas combinando calificaciones, actividades y asistencia con un clic.'],
                ['bi-folder2-open', 'Proyectos y carpeta de campo', 'Carpeta digital por alumno con notas, imágenes y documentos.'],
            ] as $f)
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi {{ $f[0] }}"></i></div>
                    <h5 class="fw-bold mb-2">{{ $f[1] }}</h5>
                    <p class="text-muted mb-0">{{ $f[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CÓMO FUNCIONA --}}
<section id="como-funciona" class="landing-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Cómo funciona</span>
            <h2 class="fw-bold fs-1 mb-3">Empezás en tres pasos</h2>
        </div>
        <div class="row g-4 text-center">
            @foreach([
                ['Elegís tu plan', 'Registrate con tu nombre y email desde la sección de planes.'],
                ['Activás tu cuenta', 'Te llega un mail con un link. Un clic y tu cuenta queda activa.'],
                ['Cargás tus cursos', 'Iniciás sesión, armás tus cursos y alumnos y empezás a registrar.'],
            ] as $i => $paso)
            <div class="col-md-4">
                <div class="paso-num">{{ $i + 1 }}</div>
                <h5 class="fw-bold">{{ $paso[0] }}</h5>
                <p class="text-muted mb-0">{{ $paso[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- INSTALAR (PWA + escritorio) --}}
<section id="instalar" class="landing-section landing-dark">
    <div class="container">
        <div class="text-center mb-5">
            <img src="/icons/icon-192x192.png" width="72" height="72" class="rounded-4 shadow mb-3" alt="GestiónAula">
            <h2 class="fw-bold fs-1 mb-3">Llevá GestiónAula siempre con vos</h2>
            <p class="fs-5 mx-auto" style="max-width:640px;opacity:.85">
                Instalala como app en tu dispositivo, sin tiendas. Se mantiene actualizada y funciona sin conexión.
            </p>
        </div>

        <div id="bloque-instalar" class="text-center mb-4">
            {{-- El navegador permite instalar ahora --}}
            <div id="estado-prompt" style="display:none">
                <button type="button" id="landing-install-btn" class="btn btn-light btn-lg px-5 fw-bold shadow" style="color:#0d47a1">
                    <i class="bi bi-download me-2"></i>Instalar en este dispositivo
                </button>
                <p class="mt-3 small" style="opacity:.75">Tu navegador detectó que podés instalarla ahora mismo.</p>
            </div>
            {{-- Ya instalada --}}
            <div id="estado-instalada" style="display:none">
                <div class="alert alert-success d-inline-flex align-items-center gap-2 px-4 py-3 mb-0">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    <span><strong>GestiónAula ya está instalada</strong> en este dispositivo.</span>
                </div>
            </div>
        </div>

        {{-- Instrucciones manuales --}}
        <div id="estado-manual">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="install-card">
                        <i class="bi bi-android2 big d-block mb-2"></i>
                        <h6 class="fw-bold">Android (Chrome)</h6>
                        <p class="small mb-0" style="opacity:.85">Menú <strong>⋮</strong> → <strong>“Agregar a pantalla de inicio”</strong>.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="install-card">
                        <i class="bi bi-apple big d-block mb-2"></i>
                        <h6 class="fw-bold">iPhone / iPad (Safari)</h6>
                        <p class="small mb-0" style="opacity:.85">Tocá <strong>Compartir</strong> → <strong>“Agregar a inicio”</strong>.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="install-card">
                        <i class="bi bi-laptop big d-block mb-2"></i>
                        <h6 class="fw-bold">PC o Mac (Chrome / Edge)</h6>
                        <p class="small mb-0" style="opacity:.85">Ícono de instalar en la barra de direcciones.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Escritorio --}}
        <div id="descarga-desktop" class="row justify-content-center mt-5">
            <div class="col-lg-9">
                <div class="install-card">
                    <div class="row align-items-center g-4">
                        <div class="col-md-8">
                            <span class="badge bg-primary mb-2">Versión de escritorio</span>
                            <h4 class="fw-bold">GestiónAula para Windows</h4>
                            <p class="mb-3" style="opacity:.85">
                                App nativa que funciona sin navegador. Guarda datos localmente cuando no hay internet
                                y los sincroniza al recuperar la conexión.
                            </p>
                            <p class="small mb-0" style="opacity:.65"><i class="bi bi-info-circle me-1"></i>Requiere Windows 10 o superior.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="/descarga/GestionAula-Setup.exe" id="btn-descarga-exe" class="btn btn-light btn-lg fw-bold" style="color:#0d47a1">
                                <i class="bi bi-windows me-2"></i>Descargar
                            </a>
                            <div class="small mt-2" style="opacity:.6">Mac: próximamente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PLANES --}}
<section id="planes" class="landing-section bg-ga-fondo">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Planes</span>
            <h2 class="fw-bold fs-1 mb-3">Elegí el plan que se adapta a vos</h2>
            <p class="text-muted fs-5">Todos los planes incluyen acceso completo al sistema.</p>
        </div>

        @if($planes->isEmpty())
            <div class="alert alert-info text-center">Pronto vamos a publicar los planes. Mientras tanto, escribinos desde la sección de contacto.</div>
        @else
        <div class="row g-4 justify-content-center">
            @foreach($planes as $index => $plan)
            @php $featured = $planes->count() > 1 ? $index === 1 : true; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="plan-card {{ $featured ? 'featured' : '' }}">
                    <div class="plan-header">
                        @if($featured)
                            <span class="badge bg-warning text-dark mb-2">Recomendado</span>
                        @endif
                        <h4 class="fw-bold mb-0">{{ $plan->nombre }}</h4>
                        @if($plan->descripcion)
                            <p class="mb-0 mt-1 small {{ $featured ? 'text-white-50' : 'text-muted' }}">{{ $plan->descripcion }}</p>
                        @endif
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-end gap-1 mb-4">
                            <span class="plan-price {{ $featured ? 'text-primary' : '' }}">${{ number_format($plan->precio, 0, ',', '.') }}</span>
                            <span class="text-muted small mb-1">/ {{ $plan->periodicidad }}</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            @foreach(['Acceso completo al sistema', 'Asistencia y calificaciones', 'Actividades y proyectos', 'Web, app instalable y escritorio'] as $item)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <button type="button"
                                class="btn {{ $featured ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill py-2 fw-semibold mt-auto js-elegir-plan"
                                data-id="{{ $plan->id }}"
                                data-nombre="{{ $plan->nombre }}"
                                data-precio="{{ (float) $plan->precio }}"
                                data-periodicidad="{{ $plan->periodicidad }}">
                            <i class="bi bi-rocket me-1"></i>Elegir este plan
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="landing-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Preguntas frecuentes</span>
            <h2 class="fw-bold fs-1">Antes de empezar</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAcc">
                    @foreach([
                        ['¿Necesito instalar algo?', 'No. Funciona desde el navegador. Si querés, podés instalarla como app en el celular o la compu, o usar la versión de escritorio para Windows.'],
                        ['¿Funciona sin internet?', 'La app instalada y la versión de escritorio permiten trabajar sin conexión y sincronizan cuando vuelve internet.'],
                        ['¿Cómo activo mi cuenta?', 'Después de registrarte te enviamos un mail con un link de activación. Al hacer clic, tu cuenta queda lista para iniciar sesión.'],
                        ['¿Puedo manejar varios cursos y establecimientos?', 'Sí. Podés organizar tus cursos, materias y horarios, y el sistema te muestra la clase actual y la siguiente.'],
                        ['¿Cómo me contacto si tengo dudas?', 'Escribinos desde el formulario de contacto de esta página y te respondemos a la brevedad.'],
                    ] as $i => $q)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i ? 'collapsed' : '' }} fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">{{ $q[0] }}</button>
                        </h2>
                        <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faqAcc">
                            <div class="accordion-body text-muted">{{ $q[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CONTACTO --}}
<section id="contacto" class="landing-section bg-ga-fondo">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <span class="section-tag">Contacto</span>
                    <h2 class="fw-bold fs-1 mb-3">¿Tenés alguna consulta?</h2>
                    <p class="text-muted fs-5">Completá el formulario y te respondemos a la brevedad.</p>
                </div>
                <div class="contact-form">
                    <form method="POST" action="{{ route('landing.contacto') }}">
                        @csrf
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Apellido y nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre') }}" placeholder="Tu nombre completo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control form-control-lg" value="{{ old('telefono') }}" placeholder="Ej: 2284-123456">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" placeholder="tu@email.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
                                <textarea name="mensaje" class="form-control form-control-lg" rows="5" placeholder="Escribí tu consulta..." required>{{ old('mensaje') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                                    <i class="bi bi-send me-2"></i>Enviar mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="landing-section landing-cta text-center" style="padding:64px 0">
    <div class="container">
        <h2 class="fw-bold mb-3">Dejá de perder tiempo con planillas</h2>
        <p class="fs-5 mb-4" style="opacity:.9">Elegí un plan y empezá a ordenar tu aula hoy.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="#planes" class="btn btn-light btn-lg px-5 fw-bold" style="color:#0d47a1"><i class="bi bi-rocket me-2"></i>Ver planes</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión</a>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="landing-footer py-4">
    <div class="container">
        <div class="row align-items-center gy-2">
            <div class="col-md-6 text-white">
                <i class="bi bi-mortarboard-fill me-2" style="color:var(--ga-claro)"></i>
                <strong>GestiónAula</strong> &copy; {{ date('Y') }}
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('login') }}" class="text-white-50 text-decoration-none me-3"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión</a>
                <a href="#contacto" class="text-white-50 text-decoration-none"><i class="bi bi-envelope me-1"></i>Contacto</a>
            </div>
        </div>
    </div>
</footer>

{{-- MODAL: registro --}}
<div class="modal fade" id="modalRegistro" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h4 class="fw-bold mb-1">Crear cuenta</h4>
                    <p class="text-muted mb-0" id="modalPlanNombre"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formRegistro" autocomplete="off">
                    <input type="hidden" id="reg_plan_id" name="plan_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="reg_name" class="form-control form-control-lg" placeholder="Tu nombre completo" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="reg_email" class="form-control form-control-lg" placeholder="tu@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="reg_pass" class="form-control form-control-lg" placeholder="Mínimo 8 caracteres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="reg_pass2" class="form-control form-control-lg" placeholder="Repetí la contraseña" required>
                        </div>
                        <div class="col-12">
                            <div id="planResumen" class="p-3 rounded-3" style="background:var(--ga-fondo);border:1px solid #d2e3fc"></div>
                        </div>
                        <div id="regError" class="col-12 d-none">
                            <div class="alert alert-danger mb-0" id="regErrorMsg"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-5" id="btnContinuar">
                    <i class="bi bi-check-circle me-1"></i>Continuar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: confirmación --}}
<div class="modal fade" id="modalConfirmacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-5">
                <i class="bi bi-clipboard2-check text-primary d-block mb-3" style="font-size:56px"></i>
                <h4 class="fw-bold mb-3">Confirmar registro</h4>
                <div id="resumenTransaccion" class="text-start mb-4"></div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg rounded-pill" id="btnConfirmar">
                        <i class="bi bi-check-circle me-2"></i>Confirmar y registrarme
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Modificar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: éxito --}}
<div class="modal fade" id="modalExito" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-5">
                <i class="bi bi-envelope-check text-success d-block mb-3" style="font-size:56px"></i>
                <h4 class="fw-bold mb-2">Registro exitoso</h4>
                <p class="text-muted mb-4">
                    Te enviamos un mail de activación a <strong id="exitoEmail"></strong>.
                    Revisá tu bandeja de entrada y hacé clic en el link para activar tu cuenta.
                </p>
                <a href="{{ route('landing.index') }}" class="btn btn-primary rounded-pill px-5">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const ruta = @json(route('landing.registrar'));
    const csrf = @json(csrf_token());
    let plan = null;

    const $ = id => document.getElementById(id);
    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const dinero = n => '$' + Number(n).toLocaleString('es-AR');
    const modal = id => bootstrap.Modal.getOrCreateInstance($(id));
    const mostrarError = msg => { $('regErrorMsg').textContent = msg; $('regError').classList.remove('d-none'); };

    document.querySelectorAll('.js-elegir-plan').forEach(btn => {
        btn.addEventListener('click', () => {
            plan = {
                id: btn.dataset.id,
                nombre: btn.dataset.nombre,
                precio: parseFloat(btn.dataset.precio),
                periodicidad: btn.dataset.periodicidad,
            };
            $('reg_plan_id').value = plan.id;
            $('modalPlanNombre').innerHTML =
                `<span class="badge bg-primary">${esc(plan.nombre)}</span> — ${dinero(plan.precio)} / ${esc(plan.periodicidad)}`;
            $('planResumen').innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Plan ${esc(plan.nombre)}</div>
                        <div class="text-muted small">${esc(plan.periodicidad)}</div>
                    </div>
                    <div class="fw-bold text-primary fs-5">${dinero(plan.precio)}</div>
                </div>`;
            $('regError').classList.add('d-none');
            modal('modalRegistro').show();
        });
    });

    $('btnContinuar').addEventListener('click', () => {
        const name = $('reg_name').value.trim();
        const email = $('reg_email').value.trim();
        const pass = $('reg_pass').value;
        const pass2 = $('reg_pass2').value;

        if (!name || !email || !pass) return mostrarError('Completá todos los campos obligatorios.');
        if (pass.length < 8) return mostrarError('La contraseña debe tener al menos 8 caracteres.');
        if (pass !== pass2) return mostrarError('Las contraseñas no coinciden.');
        $('regError').classList.add('d-none');

        $('resumenTransaccion').innerHTML = `
            <table class="table table-sm">
                <tr><td class="text-muted">Nombre</td><td class="fw-semibold">${esc(name)}</td></tr>
                <tr><td class="text-muted">Email</td><td class="fw-semibold">${esc(email)}</td></tr>
                <tr><td class="text-muted">Plan</td><td><span class="badge bg-primary">${esc(plan.nombre)}</span></td></tr>
                <tr><td class="text-muted">Monto</td><td class="fw-semibold text-primary">${dinero(plan.precio)} / ${esc(plan.periodicidad)}</td></tr>
            </table>
            <div class="alert alert-info small mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Vas a recibir un email de activación en <strong>${esc(email)}</strong>.
            </div>`;

        modal('modalRegistro').hide();
        setTimeout(() => modal('modalConfirmacion').show(), 400);
    });

    $('btnConfirmar').addEventListener('click', () => {
        const btn = $('btnConfirmar');
        btn.disabled = true;
        const formData = new FormData($('formRegistro'));

        fetch(ruta, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            btn.disabled = false;
            modal('modalConfirmacion').hide();
            if (ok && data.success) {
                $('exitoEmail').textContent = data.email;
                setTimeout(() => modal('modalExito').show(), 400);
            } else {
                mostrarError(data.message || 'No pudimos completar el registro. Intentá de nuevo.');
                setTimeout(() => modal('modalRegistro').show(), 400);
            }
        })
        .catch(() => {
            btn.disabled = false;
            modal('modalConfirmacion').hide();
            mostrarError('Error de conexión. Intentá de nuevo.');
            setTimeout(() => modal('modalRegistro').show(), 400);
        });
    });
})();
</script>

<script>
// PWA: service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(err => console.warn('[PWA] SW error:', err));
}

// Estados de instalación
(function () {
    const elPrompt = document.getElementById('estado-prompt');
    const elManual = document.getElementById('estado-manual');
    const elInstal = document.getElementById('estado-instalada');
    const installBtn = document.getElementById('landing-install-btn');
    let deferred = null;

    function mostrar(estado) {
        elPrompt.style.display = estado === 'prompt' ? 'block' : 'none';
        elInstal.style.display = estado === 'instalada' ? 'block' : 'none';
        elManual.style.display = estado === 'instalada' ? 'none' : 'block';
    }

    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        mostrar('instalada');
    } else {
        mostrar('manual');
    }

    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferred = e;
        mostrar('prompt');
    });

    installBtn.addEventListener('click', async () => {
        if (!deferred) return;
        installBtn.disabled = true;
        installBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Instalando...';
        deferred.prompt();
        const { outcome } = await deferred.userChoice;
        deferred = null;
        if (outcome === 'accepted') {
            mostrar('instalada');
        } else {
            installBtn.disabled = false;
            installBtn.innerHTML = '<i class="bi bi-download me-2"></i>Instalar en este dispositivo';
        }
    });

    window.addEventListener('appinstalled', () => mostrar('instalada'));
})();
</script>
</body>
</html>
