<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\DeclaracionController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:docente|director'])->group(function () {
    // Rutas de Asistencia
    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::post('/asistencia/registrar', [AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
    Route::post('/asistencia/guardar', [AsistenciaController::class, 'guardar'])->name('asistencia.guardar');
    Route::get('/asistencia/historial', [AsistenciaController::class, 'historial'])->name('asistencia.historial');
    // Rutas de Calificaciones
    Route::get('/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::post('/calificaciones/cargar', [CalificacionController::class, 'cargar'])->name('calificaciones.cargar');
    Route::post('/calificaciones/guardar', [CalificacionController::class, 'guardar'])->name('calificaciones.guardar');
    Route::get('/calificaciones/historial', [CalificacionController::class, 'historial'])->name('calificaciones.historial');
    // Rutas de Tareas
    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::get('/tareas/crear', [TareaController::class, 'create'])->name('tareas.create');
    Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::get('/tareas/{tarea}', [TareaController::class, 'show'])->name('tareas.show');
    Route::post('/tareas/{tarea}/entregas', [TareaController::class, 'actualizarentregas'])->name('tareas.entregas');
    Route::post('/tareas/{tarea}/cerrar', [TareaController::class, 'cerrar'])->name('tareas.cerrar');
    // Rutas de Horarios
    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::get('/horarios/crear', [HorarioController::class, 'create'])->name('horarios.create');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');
    // Rutas de Declaraciones juradas
    Route::get('/declaracion', [DeclaracionController::class, 'index'])->name('declaracion.index');
    Route::get('/declaracion/crear', [DeclaracionController::class, 'create'])->name('declaracion.create');
    Route::post('/declaracion', [DeclaracionController::class, 'store'])->name('declaracion.store');
    Route::get('/declaracion/{declaracion}', [DeclaracionController::class, 'show'])->name('declaracion.show');
    Route::post('/declaracion/{declaracion}/presentar', [DeclaracionController::class, 'presentar'])->name('declaracion.presentar');
});

Route::middleware(['auth', 'role:director'])->group(function () {
    Route::post('/declaracion/{declaracion}/resolver', [DeclaracionController::class, 'resolver'])->name('declaracion.resolver');
});

require __DIR__.'/auth.php';