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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:docente'])->group(function () {
    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::post('/asistencia/registrar', [AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
    Route::post('/asistencia/guardar', [AsistenciaController::class, 'guardar'])->name('asistencia.guardar');
    Route::get('/asistencia/historial', [AsistenciaController::class, 'historial'])->name('asistencia.historial');

    Route::get('/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::post('/calificaciones/cargar', [CalificacionController::class, 'cargar'])->name('calificaciones.cargar');
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
    });

require __DIR__.'/auth.php';