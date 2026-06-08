<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestiónAula — Sistema de gestión para docentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --dark: #1a1a2e;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Segoe UI', sans-serif; }

        /* Navbar */
        .navbar-landing { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 2px 20px rgba(0,0,0,0.08); }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(13,110,253,0.15) 0%, transparent 70%);
            top: -200px; right: -200px;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, transparent 70%);
            bottom: -100px; left: -100px;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(13,110,253,0.2);
            border: 1px solid rgba(13,110,253,0.4);
            color: #74b9ff;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .hero h1 { font-size: 3.5rem; font-weight: 800; line-height: 1.1; }
        .hero h1 span { color: #74b9ff; }
        .btn-hero-primary {
            background: #0d6efd;
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            transition: all 0.3s;
        }
        .btn-hero-primary:hover { background: #0a58ca; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,110,253,0.4); color: white; }
        .btn-hero-outline {
            background: transparent;
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s;
        }
        .btn-hero-outline:hover { border-color: white; background: rgba(255,255,255,0.1); color: white; }

        /* Stats */
        .stats-bar { background: #0d6efd; }
        .stat-item { text-align: center; padding: 24px; }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: white; }
        .stat-label { color: rgba(255,255,255,0.8); font-size: 14px; }

        /* Features */
        .feature-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #e8f0fe, #d2e3fc);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
        }
        .feature-card {
            padding: 32px;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            transition: all 0.3s;
            height: 100%;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.1); border-color: #0d6efd; }

        /* Plan cards */
        .plan-card {
            border-radius: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
            overflow: hidden;
        }
        .plan-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
        .plan-card.featured { border-color: #0d6efd; }
        .plan-card.featured .plan-header { background: #0d6efd; color: white; }
        .plan-price { font-size: 3rem; font-weight: 800; }
        .plan-period { font-size: 14px; color: #6c757d; }
        .btn-plan { padding: 12px 32px; border-radius: 50px; font-weight: 600; text-decoration: none; display: block; text-align: center; }

        /* Contact */
        .contact-section { background: #f8f9fa; }
        .contact-form { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 4px 30px rgba(0,0,0,0.06); }
        .form-control-lg { border-radius: 10px; border: 2px solid #e9ecef; }
        .form-control-lg:focus { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13,110,253,0.1); }

        /* Footer */
        footer { background: #1a1a2e; }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-landing fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-4" href="#inicio">
            <i class="bi bi-mortarboard-fill me-2"></i>GestiónAula
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLanding">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navLanding">
            <ul class="navbar-nav mx-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#inicio">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#producto">Producto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#planes">Planes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#contacto">Contacto</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                </a>
                <a href="#planes" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-rocket me-1"></i>Suscribirse
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- Alertas --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5"
     style="z-index:9999;min-width:400px">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- HERO --}}
<section id="inicio" class="hero">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-stars me-1"></i>Nuevo sistema para docentes
                </div>
                <h1 class="text-white mb-4">
                    Gestioná tu <span>aula</span> de forma inteligente
                </h1>
                <p class="text-white-50 fs-5 mb-5">
                    GestiónAula es el sistema integral que los docentes necesitaban.
                    Asistencia, calificaciones, actividades, planificaciones y mucho más
                    en un solo lugar.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#planes" class="btn-hero-primary">
                        <i class="bi bi-rocket me-2"></i>Empezar gratis
                    </a>
                    <a href="#producto" class="btn-hero-outline">
                        <i class="bi bi-play-circle me-2"></i>Ver características
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:20px;padding:32px">
                    <div class="row g-3">
                        @foreach([
                            ['bi-person-check','Asistencia','Registro en segundos'],
                            ['bi-journal-text','Calificaciones','Notas siempre a mano'],
                            ['bi-clipboard2-check','Actividades','Gestión completa'],
                            ['bi-calendar3','Horarios','Detección automática'],
                            ['bi-graph-up','Estadísticas','En tiempo real'],
                            ['bi-folder2-open','Proyectos','Con carpeta de campo'],
                        ] as $feat)
                        <div class="col-4">
                            <div style="background:rgba(255,255,255,0.08);border-radius:12px;padding:16px;color:white">
                                <i class="bi {{ $feat[0] }} fs-2 d-block mb-1 text-primary" style="color:#74b9ff!important"></i>
                                <div style="font-size:11px;font-weight:600">{{ $feat[1] }}</div>
                                <div style="font-size:10px;opacity:0.6">{{ $feat[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="stats-bar">
    <div class="container">
        <div class="row">
            @foreach([
                ['bi-people','Módulos completos','15+'],
                ['bi-shield-check','Datos seguros','100%'],
                ['bi-clock','Ahorro de tiempo','3hs/semana'],
                ['bi-star','Satisfacción','⭐⭐⭐⭐⭐'],
            ] as $stat)
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $stat[2] }}</div>
                    <div class="stat-label"><i class="bi {{ $stat[0] }} me-1"></i>{{ $stat[1] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Producto / Features --}}
<section id="producto" class="py-6" style="padding:80px 0">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                Características
            </span>
            <h2 class="fw-bold fs-1 mb-3">Todo lo que un docente necesita</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:600px">
                GestiónAula reemplaza las planillas en papel, los cuadernos y las hojas de cálculo
                con un sistema moderno y fácil de usar.
            </p>
        </div>

        <div class="row g-4">
            @foreach([
                ['bi-person-check','Asistencia digital','Registrá presente, ausente, tarde y justificado. El sistema calcula porcentajes y alertas automáticamente.','success'],
                ['bi-journal-text','Calificaciones','Notas por período y tipo de evaluación. Historial completo por alumno con todas las fuentes.','primary'],
                ['bi-clipboard2-check','Actividades pedagógicas','Creá actividades, asignalas por curso, gestioná grupos y calificá desde un solo lugar.','warning'],
                ['bi-calendar3','Horarios inteligentes','El sistema detecta automáticamente en qué clase estás y te muestra el próximo establecimiento.','info'],
                ['bi-calculator','Prenotas automáticas','Calculá prenotas combinando calificaciones, actividades y asistencia con un clic.','danger'],
                ['bi-folder2-open','Proyectos y carpeta de campo','Gestioná proyectos con carpeta digital individual por alumno con notas, imágenes y documentos.','secondary'],
            ] as $f)
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi {{ $f[0] }} text-{{ $f[3] }}"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $f[1] }}</h5>
                    <p class="text-muted mb-0">{{ $f[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Planes --}}
<section id="planes" class="py-5" style="background:#f8f9fa;padding:80px 0!important">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                Precios
            </span>
            <h2 class="fw-bold fs-1 mb-3">Elegí el plan que mejor se adapta</h2>
            <p class="text-muted fs-5">Todos los planes incluyen acceso completo al sistema.</p>
        </div>

        @if($planes->isEmpty())
        <div class="alert alert-info text-center">No hay planes disponibles en este momento.</div>
        @else
        <div class="row g-4 justify-content-center">
            @foreach($planes as $index => $plan)
            @php $featured = $index === 1 || $planes->count() === 1; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="plan-card {{ $featured ? 'featured' : '' }}">
                    <div class="plan-header p-4 {{ $featured ? '' : 'bg-light' }}">
                        @if($featured)
                        <span class="badge bg-warning text-dark mb-2">Más popular</span>
                        @endif
                        <h4 class="fw-bold mb-0 {{ $featured ? 'text-white' : '' }}">{{ $plan->nombre }}</h4>
                        @if($plan->descripcion)
                        <p class="mb-0 mt-1 small {{ $featured ? 'text-white-50' : 'text-muted' }}">
                            {{ $plan->descripcion }}
                        </p>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="d-flex align-items-end gap-1 mb-4">
                            <span class="plan-price text-{{ $featured ? 'primary' : 'dark' }}">
                                ${{ number_format($plan->precio, 0, ',', '.') }}
                            </span>
                            <span class="plan-period mb-2">/ {{ $plan->periodicidad }}</span>
                        </div>

                        <ul class="list-unstyled mb-4">
                            @foreach(['Acceso completo al sistema','Asistencia y calificaciones','Actividades y proyectos','Soporte incluido','Actualizaciones gratuitas'] as $item)
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>{{ $item }}
                            </li>
                            @endforeach
                        </ul>

                        <button class="btn btn-plan {{ $featured ? 'btn-primary' : 'btn-outline-primary' }}"
                                onclick="seleccionarPlan({{ $plan->id }}, '{{ $plan->nombre }}', {{ $plan->precio }}, '{{ $plan->periodicidad }}')">
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

{{-- Contacto --}}
<section id="contacto" class="contact-section" style="padding:80px 0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                        Contacto
                    </span>
                    <h2 class="fw-bold fs-1 mb-3">Tenes alguna consulta?</h2>
                    <p class="text-muted fs-5">Completá el formulario y te responderemos a la brevedad.</p>
                </div>

                <div class="contact-form">
                    <form method="POST" action="{{ route('landing.contacto') }}">
                        @csrf
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Apellido y nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg"
                                       value="{{ old('nombre') }}" placeholder="Tu nombre completo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control form-control-lg"
                                       value="{{ old('telefono') }}" placeholder="Ej: 2284-123456">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg"
                                       value="{{ old('email') }}" placeholder="tu@email.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
                                <textarea name="mensaje" class="form-control form-control-lg" rows="5"
                                          placeholder="Escribi tu consulta..." required>{{ old('mensaje') }}</textarea>
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

{{-- Footer --}}
<footer class="py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-white">
                <i class="bi bi-mortarboard-fill me-2 text-primary"></i>
                <strong>GestiónAula</strong> &copy; {{ date('Y') }}
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('login') }}" class="text-white-50 text-decoration-none me-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                </a>
                <a href="#contacto" class="text-white-50 text-decoration-none">
                    <i class="bi bi-envelope me-1"></i>Contacto
                </a>
            </div>
        </div>
    </div>
</footer>

{{-- Modal registro --}}
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
                <form id="formRegistro">
                    <input type="hidden" id="reg_plan_id" name="plan_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="reg_name" class="form-control form-control-lg"
                                   placeholder="Tu nombre completo" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="reg_email" class="form-control form-control-lg"
                                   placeholder="tu@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="reg_pass" class="form-control form-control-lg"
                                   placeholder="Minimo 8 caracteres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg"
                                   placeholder="Repeti la contraseña" required>
                        </div>
                        <div class="col-12">
                            <div id="planResumen" class="p-3 rounded-3"
                                 style="background:#f0f7ff;border:1px solid #d2e3fc">
                            </div>
                        </div>
                        <div id="regError" class="col-12 d-none">
                            <div class="alert alert-danger mb-0" id="regErrorMsg"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-5" onclick="confirmarRegistro()">
                    <i class="bi bi-check-circle me-1"></i>Continuar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal confirmacion --}}
<div class="modal fade" id="modalConfirmacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-5">
                <div class="mb-3" style="font-size:64px">📋</div>
                <h4 class="fw-bold mb-3">Confirmar registro</h4>
                <div id="resumenTransaccion" class="text-start mb-4"></div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg rounded-pill" onclick="procesarRegistro()">
                        <i class="bi bi-check-circle me-2"></i>Confirmar y registrarme
                    </button>
                    <button class="btn btn-outline-secondary rounded-pill"
                            data-bs-dismiss="modal">Modificar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal exito --}}
<div class="modal fade" id="modalExito" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-5">
                <div class="mb-3" style="font-size:64px">✅</div>
                <h4 class="fw-bold mb-2">Registro exitoso</h4>
                <p class="text-muted mb-4">
                    Te enviamos un mail de activación a <strong id="exitoEmail"></strong>.
                    Revisá tu bandeja de entrada y hacé clic en el link para activar tu cuenta.
                </p>
                <a href="{{ route('landing.index') }}" class="btn btn-primary rounded-pill px-5">
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let planSeleccionado = null;

function seleccionarPlan(id, nombre, precio, periodicidad) {
    planSeleccionado = { id, nombre, precio, periodicidad };

    document.getElementById('reg_plan_id').value = id;
    document.getElementById('modalPlanNombre').innerHTML =
        `<span class="badge bg-primary">${nombre}</span> — $${precio.toLocaleString('es-AR')} / ${periodicidad}`;
    document.getElementById('planResumen').innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Plan ${nombre}</div>
                <div class="text-muted small">${periodicidad}</div>
            </div>
            <div class="fw-bold text-primary fs-5">$${precio.toLocaleString('es-AR')}</div>
        </div>`;

    document.getElementById('regError').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('modalRegistro')).show();
}

function confirmarRegistro() {
    const form    = document.getElementById('formRegistro');
    const name    = document.getElementById('reg_name').value.trim();
    const email   = document.getElementById('reg_email').value.trim();
    const pass    = document.getElementById('reg_pass').value;

    if (!name || !email || !pass) {
        document.getElementById('regError').classList.remove('d-none');
        document.getElementById('regErrorMsg').textContent = 'Completa todos los campos obligatorios.';
        return;
    }

    document.getElementById('resumenTransaccion').innerHTML = `
        <table class="table table-sm">
            <tr><td class="text-muted">Nombre</td><td class="fw-semibold">${name}</td></tr>
            <tr><td class="text-muted">Email</td><td class="fw-semibold">${email}</td></tr>
            <tr><td class="text-muted">Plan</td><td><span class="badge bg-primary">${planSeleccionado.nombre}</span></td></tr>
            <tr><td class="text-muted">Monto</td><td class="fw-semibold text-primary">$${planSeleccionado.precio.toLocaleString('es-AR')} / ${planSeleccionado.periodicidad}</td></tr>
        </table>
        <div class="alert alert-info small mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Recibirás un email de activación en <strong>${email}</strong> para activar tu cuenta.
        </div>`;

    bootstrap.Modal.getInstance(document.getElementById('modalRegistro')).hide();
    setTimeout(() => {
        new bootstrap.Modal(document.getElementById('modalConfirmacion')).show();
    }, 400);
}

function procesarRegistro() {
    const form = document.getElementById('formRegistro');
    const formData = new FormData(form);

    bootstrap.Modal.getInstance(document.getElementById('modalConfirmacion')).hide();

    fetch('{{ route("landing.registrar") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('exitoEmail').textContent = data.email;
            setTimeout(() => {
                new bootstrap.Modal(document.getElementById('modalExito')).show();
            }, 400);
        } else {
            alert(data.message || 'Error al registrar. Intentá de nuevo.');
        }
    })
    .catch(() => alert('Error de conexion. Intentá de nuevo.'));
}
</script>
</body>
</html>