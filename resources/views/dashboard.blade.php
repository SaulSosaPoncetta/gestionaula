@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold">
            <i class="bi bi-house me-2"></i>Panel principal
        </h4>
        <p class="text-muted">
            Bienvenido, <strong>{{ auth()->user()->name }}</strong>.
            Estás ingresado como <span class="badge bg-primary">{{ auth()->user()->getRoleNames()->first() }}</span>
        </p>
    </div>
</div>

<div class="row g-3">

    @if(auth()->user()->hasRole('docente') || auth()->user()->hasRole('director'))
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
                    <a href="#" class="btn btn-sm btn-outline-warning">Ir al módulo</a>
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
                    <a href="#" class="btn btn-sm btn-outline-info">Ir al módulo</a>
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
                    <a href="#" class="btn btn-sm btn-outline-secondary">Ir al módulo</a>
                </div>
            </div>
        </div>
    @endif

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
                <a href="#" class="btn btn-sm btn-outline-danger">Ir al módulo</a>
            </div>
        </div>
    </div>

</div>
@endsection