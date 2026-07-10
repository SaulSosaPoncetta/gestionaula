<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DesignacionController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\AsistenciaController;
use App\Http\Controllers\Api\ContenidoController;
use App\Http\Controllers\Api\PrenotaController;
use App\Http\Controllers\Api\ActividadController;
use App\Http\Controllers\Api\LibroTemaController;
use App\Http\Controllers\Api\CalendarioEscolarController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
        Route::get('/dashboard/stats/{cursoId}/{materiaId}', [DashboardController::class, 'stats']);

        Route::get('/designaciones', [DesignacionController::class, 'index']);
        Route::get('/designaciones/{designacion}', [DesignacionController::class, 'show']);

        Route::get('/cursos', [CursoController::class, 'index']);
        Route::get('/cursos/{curso}/alumnos', [CursoController::class, 'alumnos']);

        Route::get('/materias', [MateriaController::class, 'index']);

        Route::get('/contenidos', [ContenidoController::class, 'index']);

        Route::get('/librotemas', [LibroTemaController::class, 'index']);

        Route::get('/calendarioescolar', [CalendarioEscolarController::class, 'index']);

        Route::get('/asistencias', [AsistenciaController::class, 'index']);
        Route::post('/asistencias', [AsistenciaController::class, 'guardar']);
        Route::get('/asistencias/resumen', [AsistenciaController::class, 'resumen']);
        Route::get('/asistencias/alumno/{alumnoId}', [AsistenciaController::class, 'historialAlumno']);

        Route::get('/prenotas/calcular', [PrenotaController::class, 'calcular']);
        Route::post('/prenotas/guardar', [PrenotaController::class, 'guardar']);
        Route::get('/prenotas/historial', [PrenotaController::class, 'historial']);

        Route::get('/actividades', [ActividadController::class, 'index']);

    });

});