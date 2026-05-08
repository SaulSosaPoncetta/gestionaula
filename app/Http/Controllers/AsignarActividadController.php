<?php
namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\MaterialTeoricoArchivo;
use Illuminate\Http\Request;

class AsignarActividadController extends Controller
{
    public function seleccionar()
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos   = collect();

        if (request()->filled('materia_id')) {
            $cursos = Curso::where('user_id', auth()->id())
                ->whereHas('materias', fn($q) =>
                    $q->where('materias.id', request('materia_id'))
                )->orderBy('anio')->orderBy('division')->get();

            if ($cursos->isEmpty()) {
                $cursos = Curso::where('user_id', auth()->id())
                    ->orderBy('anio')->orderBy('division')->get();
            }
        }

        return view('asignaractividad.seleccionar', compact('materias', 'cursos'));
    }

    public function ver(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

        $actividades = Actividad::with(['tipoactividad', 'grupos.alumnos'])
            ->where('user_id', auth()->id())
            ->where('materia_id', $request->materia_id)
            ->where('curso_id', $request->curso_id)
            ->orderBy('fechainicio', 'desc')
            ->get();

        $materialteoricoarchivos = MaterialTeoricoArchivo::with('tarea')
            ->where('user_id', auth()->id())
            ->where(function($q) use ($request) {
                $q->whereIn('tarea_id', function($sub) use ($request) {
                    $sub->select('id')->from('tareas')
                        ->where('curso_id', $request->curso_id)
                        ->where('user_id', auth()->id());
                })->orWhereNull('tarea_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('asignaractividad.ver', compact(
            'materia', 'curso', 'actividades', 'materialteoricoarchivos'
        ));
    }
}