<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\DeclaracionController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\AreaFormacionController;
use App\Http\Controllers\CicloController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\ContenidoController;
use App\Http\Controllers\PlanificacionController;
use App\Http\Controllers\MaterialTeoricoController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\TipoEvaluacionController;
use App\Http\Controllers\TipoActividadController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\LibroTemaController;
use App\Http\Controllers\AsignarActividadController;
use App\Http\Controllers\CalificarActividadController;
use App\Http\Controllers\CeseController;
use App\Http\Controllers\TipoValoracionController;
use App\Http\Controllers\CalendarioEscolarController;
use App\Http\Controllers\AsignarActividadNuevoController;
use App\Http\Controllers\PrenotaController;

require __DIR__.'/auth.php';

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::middleware(['auth', 'role:docente'])->group(function () {
    
    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::get('/asistencia/accion', [AsistenciaController::class, 'accion'])->name('asistencia.accion');
    Route::get('/asistencia/registrar', [AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
    Route::post('/asistencia/guardar', [AsistenciaController::class, 'guardar'])->name('asistencia.guardar');
    Route::get('/asistencia/listado', [AsistenciaController::class, 'listado'])->name('asistencia.listado');
    Route::get('/asistencia/historial', [AsistenciaController::class, 'historial'])->name('asistencia.historial');
    Route::get('/asistencia/alumno', [AsistenciaController::class, 'alumno'])->name('asistencia.alumno');
    Route::get('/asistencia/{asistencia}/editar', [AsistenciaController::class, 'editarRegistro'])->name('asistencia.editar');
    Route::put('/asistencia/{asistencia}', [AsistenciaController::class, 'actualizarRegistro'])->name('asistencia.actualizar');

    Route::get('/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::get('/calificaciones/cargar', [CalificacionController::class, 'cargar'])->name('calificaciones.cargar');
    Route::post('/calificaciones/guardar', [CalificacionController::class, 'guardar'])->name('calificaciones.guardar');
    Route::get('/calificaciones/historial', [CalificacionController::class, 'historial'])->name('calificaciones.historial');
    Route::get('/asistencia/alumno', [AsistenciaController::class, 'alumno'])->name('asistencia.alumno');

    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::get('/tareas/crear', [TareaController::class, 'create'])->name('tareas.create');
    Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::get('/tareas/{tarea}', [TareaController::class, 'show'])->name('tareas.show');
    Route::post('/tareas/{tarea}/entregas', [TareaController::class, 'actualizarentregas'])->name('tareas.entregas');
    Route::post('/tareas/{tarea}/cerrar', [TareaController::class, 'cerrar'])->name('tareas.cerrar');

    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::get('/horarios/crear', [HorarioController::class, 'create'])->name('horarios.create');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

    Route::get('/declaracion', [DeclaracionController::class, 'index'])->name('declaracion.index');
    Route::get('/declaracion/crear', [DeclaracionController::class, 'create'])->name('declaracion.create');
    Route::post('/declaracion', [DeclaracionController::class, 'store'])->name('declaracion.store');
    Route::get('/declaracion/{declaracion}', [DeclaracionController::class, 'show'])->name('declaracion.show');
    Route::post('/declaracion/{declaracion}/presentar', [DeclaracionController::class, 'presentar'])->name('declaracion.presentar');

    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/crear', [CursoController::class, 'create'])->name('cursos.create');
    Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
    Route::get('/cursos/{curso}/editar', [CursoController::class, 'edit'])->name('cursos.edit');
    Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');

    Route::get('/materias', [MateriaController::class, 'index'])->name('materias.index');
    Route::get('/materias/crear', [MateriaController::class, 'create'])->name('materias.create');
    Route::post('/materias', [MateriaController::class, 'store'])->name('materias.store');
    Route::get('/materias/{materia}/editar', [MateriaController::class, 'edit'])->name('materias.edit');
    Route::put('/materias/{materia}', [MateriaController::class, 'update'])->name('materias.update');
    Route::delete('/materias/{materia}', [MateriaController::class, 'destroy'])->name('materias.destroy');


    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    Route::get('/alumnos/crear', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::get('/alumnos/{alumno}/editar', [AlumnoController::class, 'edit'])->name('alumnos.edit');
    Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
    Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');
    Route::get('/alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');

    Route::get('/comunicacion', [MensajeController::class, 'index'])->name('comunicacion.index');
    Route::get('/comunicacion/crear', [MensajeController::class, 'create'])->name('comunicacion.create');
    Route::post('/comunicacion', [MensajeController::class, 'store'])->name('comunicacion.store');
    Route::get('/comunicacion/{mensaje}', [MensajeController::class, 'show'])->name('comunicacion.show');
    Route::delete('/comunicacion/{mensaje}', [MensajeController::class, 'destroy'])->name('comunicacion.destroy');

    Route::get('/niveles', [NivelController::class, 'index'])->name('niveles.index');
    Route::get('/niveles/crear', [NivelController::class, 'create'])->name('niveles.create');
    Route::post('/niveles', [NivelController::class, 'store'])->name('niveles.store');
    Route::get('/niveles/{nivel}/editar', [NivelController::class, 'edit'])->name('niveles.edit');
    Route::put('/niveles/{nivel}', [NivelController::class, 'update'])->name('niveles.update');
    Route::delete('/niveles/{nivel}', [NivelController::class, 'destroy'])->name('niveles.destroy');

    Route::get('/establecimientos', [EstablecimientoController::class, 'index'])->name('establecimientos.index');
    Route::get('/establecimientos/crear', [EstablecimientoController::class, 'create'])->name('establecimientos.create');
    Route::post('/establecimientos', [EstablecimientoController::class, 'store'])->name('establecimientos.store');
    Route::get('/establecimientos/{establecimiento}', [EstablecimientoController::class, 'show'])->name('establecimientos.show');
    Route::get('/establecimientos/{establecimiento}/editar', [EstablecimientoController::class, 'edit'])->name('establecimientos.edit');
    Route::put('/establecimientos/{establecimiento}', [EstablecimientoController::class, 'update'])->name('establecimientos.update');
    Route::delete('/establecimientos/{establecimiento}', [EstablecimientoController::class, 'destroy'])->name('establecimientos.destroy');

    Route::get('/areasformacion', [AreaFormacionController::class, 'index'])->name('areasformacion.index');
Route::get('/areasformacion/crear', [AreaFormacionController::class, 'create'])->name('areasformacion.create');
Route::post('/areasformacion', [AreaFormacionController::class, 'store'])->name('areasformacion.store');
Route::get('/areasformacion/{areasformacion}/editar', [AreaFormacionController::class, 'edit'])->name('areasformacion.edit');
Route::put('/areasformacion/{areasformacion}', [AreaFormacionController::class, 'update'])->name('areasformacion.update');
Route::delete('/areasformacion/{areasformacion}', [AreaFormacionController::class, 'destroy'])->name('areasformacion.destroy');

Route::get('/ciclos', [CicloController::class, 'index'])->name('ciclos.index');
Route::get('/ciclos/crear', [CicloController::class, 'create'])->name('ciclos.create');
Route::post('/ciclos', [CicloController::class, 'store'])->name('ciclos.store');
Route::get('/ciclos/{ciclo}/editar', [CicloController::class, 'edit'])->name('ciclos.edit');
Route::put('/ciclos/{ciclo}', [CicloController::class, 'update'])->name('ciclos.update');
Route::delete('/ciclos/{ciclo}', [CicloController::class, 'destroy'])->name('ciclos.destroy');

Route::get('/especialidades', [EspecialidadController::class, 'index'])->name('especialidades.index');
Route::get('/especialidades/crear', [EspecialidadController::class, 'create'])->name('especialidades.create');
Route::post('/especialidades', [EspecialidadController::class, 'store'])->name('especialidades.store');
Route::get('/especialidades/{especialidad}/editar', [EspecialidadController::class, 'edit'])->name('especialidades.edit');
Route::put('/especialidades/{especialidad}', [EspecialidadController::class, 'update'])->name('especialidades.update');
Route::delete('/especialidades/{especialidad}', [EspecialidadController::class, 'destroy'])->name('especialidades.destroy');

Route::get('/contenidos', [ContenidoController::class, 'index'])->name('contenidos.index');
Route::get('/contenidos/crear', [ContenidoController::class, 'create'])->name('contenidos.create');
Route::post('/contenidos', [ContenidoController::class, 'store'])->name('contenidos.store');
Route::get('/contenidos/{contenido}/editar', [ContenidoController::class, 'edit'])->name('contenidos.edit');
Route::get('/contenidos/{contenido}', [ContenidoController::class, 'show'])->name('contenidos.show');
Route::put('/contenidos/{contenido}', [ContenidoController::class, 'update'])->name('contenidos.update');
Route::delete('/contenidos/{contenido}', [ContenidoController::class, 'destroy'])->name('contenidos.destroy');

Route::get('/planificaciones', [PlanificacionController::class, 'index'])->name('planificaciones.index');
Route::get('/planificaciones/crear', [PlanificacionController::class, 'create'])->name('planificaciones.create');
Route::post('/planificaciones', [PlanificacionController::class, 'store'])->name('planificaciones.store');
Route::get('/planificaciones/{planificacion}', [PlanificacionController::class, 'show'])->name('planificaciones.show');
Route::get('/planificaciones/{planificacion}/editar', [PlanificacionController::class, 'edit'])->name('planificaciones.edit');
Route::put('/planificaciones/{planificacion}', [PlanificacionController::class, 'update'])->name('planificaciones.update');
Route::delete('/planificaciones/{planificacion}', [PlanificacionController::class, 'destroy'])->name('planificaciones.destroy');
Route::post('/planificaciones/{planificacion}/unidades', [PlanificacionController::class, 'storeUnidad'])->name('planificaciones.unidades.store');
Route::delete('/planificaciones/{planificacion}/unidades/{unidad}', [PlanificacionController::class, 'destroyUnidad'])->name('planificaciones.unidades.destroy');

Route::get('/materialteoricoarchivos', [MaterialTeoricoController::class, 'index'])->name('materialteoricoarchivos.index');
Route::get('/materialteoricoarchivos/crear', [MaterialTeoricoController::class, 'create'])->name('materialteoricoarchivos.create');
Route::post('/materialteoricoarchivos', [MaterialTeoricoController::class, 'store'])->name('materialteoricoarchivos.store');
Route::delete('/materialteoricoarchivos/{materialteoricoarchivo}', [MaterialTeoricoController::class, 'destroy'])->name('materialteoricoarchivos.destroy');
Route::put('/materialteoricoarchivos/{materialteoricoarchivo}/asignar', [MaterialTeoricoController::class, 'asignar'])->name('materialteoricoarchivos.asignar');

// Períodos
Route::get('/periodos', [PeriodoController::class, 'index'])->name('periodos.index');
Route::get('/periodos/crear', [PeriodoController::class, 'create'])->name('periodos.create');
Route::post('/periodos', [PeriodoController::class, 'store'])->name('periodos.store');
Route::get('/periodos/{periodo}/editar', [PeriodoController::class, 'edit'])->name('periodos.edit');
Route::put('/periodos/{periodo}', [PeriodoController::class, 'update'])->name('periodos.update');
Route::delete('/periodos/{periodo}', [PeriodoController::class, 'destroy'])->name('periodos.destroy');

// Tipos de evaluación
Route::get('/tiposevaluacion', [TipoEvaluacionController::class, 'index'])->name('tiposevaluacion.index');
Route::get('/tiposevaluacion/crear', [TipoEvaluacionController::class, 'create'])->name('tiposevaluacion.create');
Route::post('/tiposevaluacion', [TipoEvaluacionController::class, 'store'])->name('tiposevaluacion.store');
Route::get('/tiposevaluacion/{tiposevaluacion}/editar', [TipoEvaluacionController::class, 'edit'])->name('tiposevaluacion.edit');
Route::put('/tiposevaluacion/{tiposevaluacion}', [TipoEvaluacionController::class, 'update'])->name('tiposevaluacion.update');
Route::delete('/tiposevaluacion/{tiposevaluacion}', [TipoEvaluacionController::class, 'destroy'])->name('tiposevaluacion.destroy');

Route::get('/tiposactividad', [TipoActividadController::class, 'index'])->name('tiposactividad.index');
Route::get('/tiposactividad/crear', [TipoActividadController::class, 'create'])->name('tiposactividad.create');
Route::post('/tiposactividad', [TipoActividadController::class, 'store'])->name('tiposactividad.store');
Route::get('/tiposactividad/{tiposactividad}/editar', [TipoActividadController::class, 'edit'])->name('tiposactividad.edit');
Route::put('/tiposactividad/{tiposactividad}', [TipoActividadController::class, 'update'])->name('tiposactividad.update');
Route::delete('/tiposactividad/{tiposactividad}', [TipoActividadController::class, 'destroy'])->name('tiposactividad.destroy');

Route::get('/actividades', [ActividadController::class, 'index'])->name('actividades.index');
Route::get('/actividades/seleccionar', [ActividadController::class, 'seleccionar'])->name('actividades.seleccionar');
Route::get('/actividades/crear', [ActividadController::class, 'create'])->name('actividades.create');
Route::post('/actividades', [ActividadController::class, 'store'])->name('actividades.store');
Route::get('/actividades/{actividad}', [ActividadController::class, 'show'])->name('actividades.show');
Route::delete('/actividades/{actividad}', [ActividadController::class, 'destroy'])->name('actividades.destroy');

Route::get('/librotemas', [LibroTemaController::class, 'index'])->name('librotemas.index');
Route::get('/librotemas/crear', [LibroTemaController::class, 'create'])->name('librotemas.create');
Route::post('/librotemas', [LibroTemaController::class, 'store'])->name('librotemas.store');
Route::delete('/librotemas/{librotema}', [LibroTemaController::class, 'destroy'])->name('librotemas.destroy');

Route::get('/asignaractividad', [AsignarActividadController::class, 'seleccionar'])->name('asignaractividad.seleccionar');
Route::get('/asignaractividad/ver', [AsignarActividadController::class, 'ver'])->name('asignaractividad.ver');
Route::get('/asignaractividad/{asignacion}/detalle', [AsignarActividadController::class, 'detalle'])->name('asignaractividad.detalle');

Route::get('/calificaractividad', [CalificarActividadController::class, 'index'])->name('calificaractividad.index');
Route::get('/calificaractividad/ver', [CalificarActividadController::class, 'ver'])->name('calificaractividad.ver');
Route::post('/calificaractividad/guardar', [CalificarActividadController::class, 'guardar'])->name('calificaractividad.guardar');
Route::get('/calificaractividad/historial', [CalificarActividadController::class, 'historial'])->name('calificaractividad.historial');
Route::get('/calificaractividad/incompletas', [CalificarActividadController::class, 'incompletas'])->name('calificaractividad.incompletas');
Route::put('/calificaractividad/{estado}/vencida', [CalificarActividadController::class, 'pasarAVencida'])->name('calificaractividad.vencida');
Route::get('/calificaractividad/calificadas', [CalificarActividadController::class, 'calificadas'])->name('calificaractividad.calificadas');
Route::get('/calificaractividad/{estado}/show', [CalificarActividadController::class, 'showCalificada'])->name('calificaractividad.show');
Route::get('/calificaractividad/{estado}/edit', [CalificarActividadController::class, 'editCalificada'])->name('calificaractividad.edit');
Route::put('/calificaractividad/{estado}/update', [CalificarActividadController::class, 'updateCalificada'])->name('calificaractividad.update');
Route::post('/calificaractividad/calificar', [CalificarActividadController::class, 'calificar'])->name('calificaractividad.calificar');

Route::get('/ceses', [CeseController::class, 'index'])->name('ceses.index');
Route::get('/ceses/crear', [CeseController::class, 'create'])->name('ceses.create');
Route::post('/ceses', [CeseController::class, 'store'])->name('ceses.store');
Route::delete('/ceses/{cese}', [CeseController::class, 'destroy'])->name('ceses.destroy');

Route::get('/tipovaloraciones', [TipoValoracionController::class, 'index'])->name('tipovaloraciones.index');
Route::get('/tipovaloraciones/crear', [TipoValoracionController::class, 'create'])->name('tipovaloraciones.create');
Route::post('/tipovaloraciones', [TipoValoracionController::class, 'store'])->name('tipovaloraciones.store');
Route::get('/tipovaloraciones/{tipovaloracion}/editar', [TipoValoracionController::class, 'edit'])->name('tipovaloraciones.edit');
Route::put('/tipovaloraciones/{tipovaloracion}', [TipoValoracionController::class, 'update'])->name('tipovaloraciones.update');
Route::delete('/tipovaloraciones/{tipovaloracion}', [TipoValoracionController::class, 'destroy'])->name('tipovaloraciones.destroy');

Route::get('/calendarioescolar', [CalendarioEscolarController::class, 'index'])->name('calendarioescolar.index');
Route::get('/calendarioescolar/crear', [CalendarioEscolarController::class, 'create'])->name('calendarioescolar.create');
Route::post('/calendarioescolar', [CalendarioEscolarController::class, 'store'])->name('calendarioescolar.store');
Route::get('/calendarioescolar/{calendarioescolar}/editar', [CalendarioEscolarController::class, 'edit'])->name('calendarioescolar.edit');
Route::put('/calendarioescolar/{calendarioescolar}', [CalendarioEscolarController::class, 'update'])->name('calendarioescolar.update');
Route::delete('/calendarioescolar/{calendarioescolar}', [CalendarioEscolarController::class, 'destroy'])->name('calendarioescolar.destroy');

Route::get('/asignarnuevo', [AsignarActividadNuevoController::class, 'index'])->name('asignarnuevo.index');
Route::get('/asignarnuevo/ver', [AsignarActividadNuevoController::class, 'ver'])->name('asignarnuevo.ver');
Route::post('/asignarnuevo/asignar', [AsignarActividadNuevoController::class, 'asignar'])->name('asignarnuevo.asignar');

Route::get('/prenotas', [PrenotaController::class, 'index'])->name('prenotas.index');
Route::post('/prenotas/calcular', [PrenotaController::class, 'calcular'])->name('prenotas.calcular');
Route::post('/prenotas/guardar', [PrenotaController::class, 'guardar'])->name('prenotas.guardar');
Route::get('/prenotas/historial', [PrenotaController::class, 'historial'])->name('prenotas.historial');

});

