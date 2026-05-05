<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\MaterialTeoricoArchivo;
use Illuminate\Http\Request;

class AsignarActividadController extends Controller
{
    /**
     * Paso 1: Seleccionar materia y curso
     */
    public function seleccionar()
    {
        $materias = Materia::orderBy('nombre')->get();
        $cursos   = collect();

        if (request()->filled('materia_id')) {
            $cursos = Curso::whereHas('materias', fn($q) =>
                $q->where('materias.id', request('materia_id'))
            )->orderBy('anio')->orderBy('division')->get();

            if ($cursos->isEmpty()) {
                $cursos = Curso::orderBy('anio')->orderBy('division')->get();
            }
        }

        return view('asignaractividad.seleccionar', compact('materias', 'cursos'));
    }

    /**
     * Paso 2: Mostrar actividades y material teórico
     */
    public function ver(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $materia = Materia::findOrFail($request->materia_id);
        $curso   = Curso::with('alumnos')->findOrFail($request->curso_id);

        // Actividades para esta materia y curso
        $actividades = Actividad::with(['tipoactividad', 'grupos.alumnos'])
            ->where('materia_id', $request->materia_id)
            ->where('curso_id', $request->curso_id)
            ->where('user_id', auth()->id())
            ->orderBy('fechainicio', 'desc')
            ->get();

        // Material teórico subido para estas actividades
        $materialteoricoarchivos = MaterialTeoricoArchivo::with('tarea')
            ->where('user_id', auth()->id())
            ->whereIn('tarea_id', function($q) use ($request) {
                // Material asociado a prácticos del curso
                $q->select('id')->from('tareas')
                  ->where('curso_id', $request->curso_id);
            })
            ->orWhere(function($q) use ($request) {
                // Material sin práctico asociado pero de este docente
                $q->where('user_id', auth()->id())
                  ->whereNull('tarea_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('asignaractividad.ver', compact(
            'materia', 'curso', 'actividades', 'materialteoricoarchivos'
        ));
    }
}