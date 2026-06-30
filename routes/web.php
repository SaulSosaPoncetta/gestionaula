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
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\CicloLectivoController;
use App\Http\Controllers\CierreCuatriController;
use App\Http\Controllers\PrenotaController;
use App\Http\Controllers\ProyectoController;
use App\Http\Middleware\SeguridadWeb;
use App\Http\Controllers\PagoOnlineController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DesignacionController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SuscripcionController;
use App\Http\Controllers\Admin\PagoController;


require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('landing.index');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==========================================
// GRUPO DE DOCENTES
// ==========================================
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

    Route::get('/periodos', [PeriodoController::class, 'index'])->name('periodos.index');
    Route::get('/periodos/crear', [PeriodoController::class, 'create'])->name('periodos.create');
    Route::post('/periodos', [PeriodoController::class, 'store'])->name('periodos.store');
    Route::get('/periodos/{periodo}/editar', [PeriodoController::class, 'edit'])->name('periodos.edit');
    Route::put('/periodos/{periodo}', [PeriodoController::class, 'update'])->name('periodos.update');
    Route::delete('/periodos/{periodo}', [PeriodoController::class, 'destroy'])->name('periodos.destroy');

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

    Route::get('/designaciones', [DesignacionController::class, 'index'])->name('designaciones.index');
    Route::get('/designaciones/crear', [DesignacionController::class, 'create'])->name('designaciones.create');
    Route::post('/designaciones', [DesignacionController::class, 'store'])->name('designaciones.store');
    Route::get('/designaciones/{designacion}/editar', [DesignacionController::class, 'edit'])->name('designaciones.edit');
    Route::put('/designaciones/{designacion}', [DesignacionController::class, 'update'])->name('designaciones.update');
    Route::delete('/designaciones/{designacion}', [DesignacionController::class, 'destroy'])->name('designaciones.destroy');
    Route::get('/api/designaciones', [DesignacionController::class, 'listar'])->name('api.designaciones');

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

    // ── Impresiones PDF ────────────────────────────────────────────────
    Route::get('/pdf', [PdfController::class, 'index'])->name('pdf.index');
    Route::get('/pdf/alumnos',        [PdfController::class, 'alumnos'])->name('pdf.alumnos');
    Route::get('/pdf/asistencia',     [PdfController::class, 'asistencia'])->name('pdf.asistencia');
    Route::get('/pdf/calificaciones', [PdfController::class, 'calificaciones'])->name('pdf.calificaciones');
    Route::get('/pdf/boletin',        [PdfController::class, 'boletin'])->name('pdf.boletin');
    Route::get('/pdf/cierre',         [PdfController::class, 'cierre'])->name('pdf.cierre');
    Route::get('/pdf/declaracion',    [PdfController::class, 'declaracion'])->name('pdf.declaracion');
    Route::get('/pdf/planilla',       [PdfController::class, 'planilla'])->name('pdf.planilla');
    Route::get('/pdf/contenidos',     [PdfController::class, 'contenidos'])->name('pdf.contenidos');
    Route::get('/pdf/librotemas',     [PdfController::class, 'librotemas'])->name('pdf.librotemas');
    Route::get('/pdf/docente',        [PdfController::class, 'docente'])->name('pdf.docente');

    // ── Excel ──────────────────────────────────────────────────────────
    Route::get('/excel',                [ExcelController::class, 'index'])->name('excel.index');
    Route::get('/excel/alumnos',        [ExcelController::class, 'alumnos'])->name('excel.alumnos');
    Route::get('/excel/asistencia',     [ExcelController::class, 'asistencia'])->name('excel.asistencia');
    Route::get('/excel/calificaciones', [ExcelController::class, 'calificaciones'])->name('excel.calificaciones');
    Route::get('/excel/cierre',         [ExcelController::class, 'cierre'])->name('excel.cierre');
    Route::get('/excel/declaracion',    [ExcelController::class, 'declaracion'])->name('excel.declaracion');
    Route::get('/excel/contenidos',     [ExcelController::class, 'contenidos'])->name('excel.contenidos');
    Route::get('/excel/librotemas',     [ExcelController::class, 'librotemas'])->name('excel.librotemas');
    Route::get('/excel/docente',        [ExcelController::class, 'docente'])->name('excel.docente');

    Route::resource('ciclos-lectivos', CicloLectivoController::class)->names('ciclos_lectivos');
    Route::post('/ciclos-lectivos/{ciclosLectivo}/activar', [CicloLectivoController::class, 'activar'])->name('ciclos_lectivos.activar');
    Route::get('/ciclos-lectivos/{ciclosLectivo}/siguiente', [CicloLectivoController::class, 'generarSiguiente'])->name('ciclos_lectivos.siguiente');

    Route::get('/cierre-cuatri', [CierreCuatriController::class, 'index'])->name('cierre_cuatri.index');
    Route::post('/cierre-cuatri/calcular', [CierreCuatriController::class, 'calcular'])->name('cierre_cuatri.calcular');
    Route::post('/cierre-cuatri/guardar', [CierreCuatriController::class, 'guardar'])->name('cierre_cuatri.guardar');
    Route::get('/cierre-cuatri/historial', [CierreCuatriController::class, 'historial'])->name('cierre_cuatri.historial');
    Route::get('/prenotas', [PrenotaController::class, 'index'])->name('prenotas.index');
    Route::post('/prenotas/calcular', [PrenotaController::class, 'calcular'])->name('prenotas.calcular');
    Route::post('/prenotas/guardar', [PrenotaController::class, 'guardar'])->name('prenotas.guardar');
    Route::get('/prenotas/historial', [PrenotaController::class, 'historial'])->name('prenotas.historial');

    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::get('/proyectos/crear', [ProyectoController::class, 'create'])->name('proyectos.create');
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
    Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'show'])->name('proyectos.show');
    Route::get('/proyectos/{proyecto}/editar', [ProyectoController::class, 'edit'])->name('proyectos.edit');
    Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'update'])->name('proyectos.update');
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');
    Route::get('/carpetacampo/{carpeta}', [ProyectoController::class, 'carpeta'])->name('proyectos.carpeta');
    Route::post('/carpetacampo/{carpeta}/entrada', [ProyectoController::class, 'agregarEntrada'])->name('proyectos.entrada.store');
    Route::delete('/carpetacampo/entrada/{entrada}', [ProyectoController::class, 'eliminarEntrada'])->name('proyectos.entrada.destroy');

    Route::get('/api/cursos/{curso}/alumnos', function(\App\Models\Curso $curso) {
        abort_if($curso->user_id !== auth()->id(), 403);
        return $curso->alumnos()
            ->where('alumnos.user_id', auth()->id())
            ->orderBy('apellido')
            ->get()
            ->map(fn($a) => [
                'id'               => $a->id,
                'nombre_completo'  => $a->nombre_completo,
                'tipocursadalabel' => $a->tipocursadalabel,
                'tipocursadabadge' => $a->tipocursadabadge,
            ]);
    })->middleware(['web', 'auth'])->name('api.curso.alumnos');
});



// ==========================================
// GRUPO DE ADMINISTRACIÓN (CORREGIDO)
// ==========================================
// Añadimos ->prefix('admin') para las URLs y ->as('admin.') para los nombres
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {

    // URL: /admin  -> Nombre de ruta: admin.dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Usuarios
    Route::get('/usuario/{user}', [AdminController::class, 'verUsuario'])->name('usuario');
    Route::post('/usuario/{user}/toggle', [AdminController::class, 'toggleActivo'])->name('toggle');

    // Planes (Ahora generará automáticamente: admin.planes.index, admin.planes.create, etc.)
    Route::get('/planes', [PlanController::class, 'index'])->name('planes.index');
    Route::get('/planes/crear', [PlanController::class, 'create'])->name('planes.create');
    Route::post('/planes', [PlanController::class, 'store'])->name('planes.store');
    Route::get('/planes/{plan}/editar', [PlanController::class, 'edit'])->name('planes.edit');
    Route::put('/planes/{plan}', [PlanController::class, 'update'])->name('planes.update');
    Route::delete('/planes/{plan}', [PlanController::class, 'destroy'])->name('planes.destroy');

    // Suscripciones (Nombres resultantes: admin.suscripciones.store, etc.)
    Route::post('/suscripciones', [SuscripcionController::class, 'store'])->name('suscripciones.store');
    Route::post('/suscripciones/{suscripcion}/suspender', [SuscripcionController::class, 'suspender'])->name('suscripciones.suspender');
    Route::post('/suscripciones/{suscripcion}/activar', [SuscripcionController::class, 'activar'])->name('suscripciones.activar');

    // Pagos (Nombres resultantes: admin.pagos.index, etc.)
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::post('/pagos/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
    Route::post('/pagos/generar', [PagoController::class, 'generarPago'])->name('pagos.generar');
    Route::post('/pagos/{pago}/vencido', [PagoController::class, 'marcarVencido'])->name('pagos.vencido');
});

// Pagos online del docente
Route::middleware(['auth', 'role:docente'])->group(function () {
    Route::get('/mis-pagos', [PagoOnlineController::class, 'index'])->name('pagos.index');
    Route::post('/pagos/mp/iniciar', [PagoOnlineController::class, 'iniciarMP'])->name('pagos.mp.iniciar');
    Route::get('/pagos/mp/success', [PagoOnlineController::class, 'mpSuccess'])->name('pagos.mp.success');
    Route::get('/pagos/mp/failure', [PagoOnlineController::class, 'mpFailure'])->name('pagos.mp.failure');
    Route::get('/pagos/mp/pending', [PagoOnlineController::class, 'mpPending'])->name('pagos.mp.pending');
    Route::post('/pagos/paypal/iniciar', [PagoOnlineController::class, 'iniciarPaypal'])->name('pagos.paypal.iniciar');
    Route::get('/pagos/paypal/success', [PagoOnlineController::class, 'paypalSuccess'])->name('pagos.paypal.success');
    Route::get('/pagos/paypal/cancel', [PagoOnlineController::class, 'paypalCancel'])->name('pagos.paypal.cancel');
});

// Webhooks — sin auth (las plataformas los llaman directamente)
Route::post('/webhooks/mercadopago', [PagoOnlineController::class, 'webhookMP'])
    ->name('webhooks.mercadopago')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/webhooks/paypal', [PagoOnlineController::class, 'webhookPaypal'])
    ->name('webhooks.paypal')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
// API stats dashboard
Route::get('/api/dashboard/stats/{cursoId}/{materiaId}', function(int $cursoId, int $materiaId) {
    $curso   = \App\Models\Curso::where('id', $cursoId)->where('user_id', auth()->id())->first();
    $materia = \App\Models\Materia::where('id', $materiaId)->where('user_id', auth()->id())->first();

    if (!$curso || !$materia) {
        return response()->json(['error' => 'No encontrado', 'cursoId' => $cursoId, 'materiaId' => $materiaId], 404);
    }

    $alumnos = $curso->alumnos()->where('alumnos.user_id', auth()->id())->get();
    $asignaciones = \App\Models\ActividadAsignacion::where('materia_id', $materia->id)
        ->where('curso_id', $curso->id)
        ->where('user_id', auth()->id())
        ->pluck('id');

    $stats = $alumnos->map(function($alumno) use ($materia, $asignaciones) {
        $asistencias  = \App\Models\Asistencia::where('alumno_id', $alumno->id)
            ->where('materia_id', $materia->id)
            ->where('user_id', auth()->id())
            ->get();

        $presentes    = $asistencias->whereIn('estado', ['presente', 'tarde'])->count();
        $ausentes     = $asistencias->where('estado', 'ausente')->count();
        $justificados = $asistencias->where('estado', 'justificado')->count();
        $totalAsignadas = $asignaciones->count();

        $notas = \App\Models\ActividadNota::where('alumno_id', $alumno->id)
            ->whereIn('asignacion_id', $asignaciones)
            ->where('user_id', auth()->id())
            ->get();

        $entregadas = $notas->where('estado', 'entregado')->count();
        $vencidas   = $notas->where('estado', 'vencido')->count();

        $ultimoCierre = \App\Models\CierreNota::where('alumno_id', $alumno->id)
            ->where('materia_id', $materia->id)
            ->where('user_id', auth()->id())
            ->orderBy('fecharegistro', 'desc')
            ->first();

        return [
            'id'              => $alumno->id,
            'nombre'          => $alumno->nombre_completo,
            'presentes'       => $presentes,
            'ausentes'        => $ausentes,
            'justificados'    => $justificados,
            'asignadas'       => $totalAsignadas,
            'entregadas'      => $entregadas,
            'vencidas'        => $vencidas,
            'ultimaValoracion'=> $ultimoCierre?->notavalorativa ?? '-',
            'ultimaNota'      => $ultimoCierre?->notanumerica ?? null,
        ];
    });

    $totalAlumnos = $alumnos->count();

    $cierres = \App\Models\CierreNota::where('materia_id', $materia->id)
        ->where('curso_id', $curso->id)
        ->where('user_id', auth()->id())
        ->orderBy('fecharegistro', 'desc')
        ->get()
        ->groupBy('alumno_id')
        ->map(fn($g) => $g->first());

    $aprobados  = $cierres->where('notanumerica', '>=', 7)->count();
    $regulares  = $cierres->where('notanumerica', '>=', 4)->where('notanumerica', '<', 7)->count();
    $reprobados = $cierres->where('notanumerica', '<', 4)->count();
    $sinNota    = $totalAlumnos - $cierres->count();

    $tendenciaAsistencias = \App\Models\Asistencia::where('materia_id', $materia->id)
        ->where('curso_id', $curso->id)
        ->where('user_id', auth()->id())
        ->selectRaw('fecha, COUNT(*) as total,
            SUM(CASE WHEN estado IN ("presente","tarde","justificado") THEN 1 ELSE 0 END) as presentes')
        ->groupBy('fecha')
        ->orderBy('fecha', 'desc')
        ->limit(10)
        ->get()
        ->reverse()
        ->values()
        ->map(fn($r) => [
            'fecha'      => \Carbon\Carbon::parse($r->fecha)->format('d/m'),
            'porcentaje' => $r->total > 0 ? round(($r->presentes / $r->total) * 100, 1) : 0,
        ]);

    $tendenciaCierres = \App\Models\CierreNota::where('materia_id', $materia->id)
        ->where('curso_id', $curso->id)
        ->where('user_id', auth()->id())
        ->selectRaw('tipocierre, fecharegistro, AVG(notanumerica) as promedio')
        ->groupBy('tipocierre', 'fecharegistro')
        ->orderBy('fecharegistro')
        ->get()
        ->map(fn($r) => [
            'label'    => $r->tipocierre,
            'promedio' => round($r->promedio, 2),
            'fecha'    => \Carbon\Carbon::parse($r->fecharegistro)->format('d/m/Y'),
        ]);

    return response()->json([
        'alumnos'  => $stats,
        'graficos' => [
            'distribucion' => compact('aprobados', 'regulares', 'reprobados', 'sinNota'),
            'asistencias'  => $tendenciaAsistencias,
            'cierres'      => $tendenciaCierres,
        ],
    ]);
})->middleware(['web', 'auth'])->name('api.dashboard.stats');

// Landing publica
Route::get('/landing', [LandingController::class, 'index'])->name('landing.index');
Route::get('/landing/planes', [LandingController::class, 'planes'])->name('landing.planes');
Route::get('/landing/registro', [LandingController::class, 'registroPlan'])->name('landing.registro');
Route::post('/landing/contacto', [LandingController::class, 'contacto'])->name('landing.contacto');
Route::post('/landing/registrar', [LandingController::class, 'registrarDocente'])->name('landing.registrar');
Route::get('/activar/{token}', [LandingController::class, 'activar'])->name('landing.activar');