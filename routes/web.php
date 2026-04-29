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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:docente'])->group(function () {
    //  Asistencia
    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::post('/asistencia/registrar', [AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
    Route::post('/asistencia/guardar', [AsistenciaController::class, 'guardar'])->name('asistencia.guardar');
    Route::get('/asistencia/historial', [AsistenciaController::class, 'historial'])->name('asistencia.historial');
//  Calificaciones
    Route::get('/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::post('/calificaciones/cargar', [CalificacionController::class, 'cargar'])->name('calificaciones.cargar');
    Route::post('/calificaciones/guardar', [CalificacionController::class, 'guardar'])->name('calificaciones.guardar');
    Route::get('/calificaciones/historial', [CalificacionController::class, 'historial'])->name('calificaciones.historial');
//  Tareas
    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::get('/tareas/crear', [TareaController::class, 'create'])->name('tareas.create');
    Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::get('/tareas/{tarea}', [TareaController::class, 'show'])->name('tareas.show');
    Route::post('/tareas/{tarea}/entregas', [TareaController::class, 'actualizarentregas'])->name('tareas.entregas');
    Route::post('/tareas/{tarea}/cerrar', [TareaController::class, 'cerrar'])->name('tareas.cerrar');
//  
    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::get('/horarios/crear', [HorarioController::class, 'create'])->name('horarios.create');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');
//  Declaraciones
    Route::get('/declaracion', [DeclaracionController::class, 'index'])->name('declaracion.index');
    Route::get('/declaracion/crear', [DeclaracionController::class, 'create'])->name('declaracion.create');
    Route::post('/declaracion', [DeclaracionController::class, 'store'])->name('declaracion.store');
    Route::get('/declaracion/{declaracion}', [DeclaracionController::class, 'show'])->name('declaracion.show');
    Route::post('/declaracion/{declaracion}/presentar', [DeclaracionController::class, 'presentar'])->name('declaracion.presentar');
//  Gestión de cursos
    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/crear', [CursoController::class, 'create'])->name('cursos.create');
    Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
    Route::get('/cursos/{curso}/editar', [CursoController::class, 'edit'])->name('cursos.edit');
    Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');
//  Gestión de materias
    Route::get('/materias', [MateriaController::class, 'index'])->name('materias.index');
    Route::get('/materias/crear', [MateriaController::class, 'create'])->name('materias.create');
    Route::post('/materias', [MateriaController::class, 'store'])->name('materias.store');
    Route::get('/materias/{materia}/editar', [MateriaController::class, 'edit'])->name('materias.edit');
    Route::put('/materias/{materia}', [MateriaController::class, 'update'])->name('materias.update');
    Route::delete('/materias/{materia}', [MateriaController::class, 'destroy'])->name('materias.destroy');
//  Gestión de alumnos
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    Route::get('/alumnos/crear', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::get('/alumnos/{alumno}/editar', [AlumnoController::class, 'edit'])->name('alumnos.edit');
    Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
    Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');
//  Comunicación
    Route::get('/comunicacion', [MensajeController::class, 'index'])->name('comunicacion.index');
    Route::get('/comunicacion/crear', [MensajeController::class, 'create'])->name('comunicacion.create');
    Route::post('/comunicacion', [MensajeController::class, 'store'])->name('comunicacion.store');
    Route::get('/comunicacion/{mensaje}', [MensajeController::class, 'show'])->name('comunicacion.show');
    Route::delete('/comunicacion/{mensaje}', [MensajeController::class, 'destroy'])->name('comunicacion.destroy');
});

require __DIR__.'/auth.php';