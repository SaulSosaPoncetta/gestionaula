<?php
namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadAsignacion;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\Horario;
use App\Models\MaterialTeoricoArchivo;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;

class AsignarActividadController extends Controller
{
    use DetectaHorarioActivo;

    public function seleccionar()
    {
        $horario       = $this->detectarHorarioActivo();
        $materiaActiva = $horario?->materia_id;
        $cursoActivo   = $horario?->curso_id;

        // Si no hay filtro, preseleccionar la materia activa
        $materiaId = request('materia_id') ?: $materiaActiva;

        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        // Cursos filtrados por materia vía horarios, activo primero
        $cursos = collect();
        if ($materiaId) {
            $cursos = Horario::with('curso')
                ->where('user_id', auth()->id())
                ->where('materia_id', $materiaId)
                ->get()
                ->pluck('curso')->filter()->unique('id')
                ->sortBy(fn($c) => $cursoActivo === $c->id ? '0' : '1_'.$c->nombre_completo)
                ->values();

            if ($cursos->isEmpty()) {
                $cursos = Curso::where('user_id', auth()->id())
                    ->orderBy('anio')->orderBy('division')->get();
            }
        }

        return view('asignaractividad.seleccionar', compact(
            'materias', 'cursos', 'materiaActiva', 'cursoActivo', 'materiaId'
        ));
    }

    public function ver(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

        // Asignaciones de actividades para este curso y materia
        $asignaciones = ActividadAsignacion::with([
                'actividad.tipoactividad',
                'actividad.items',
                'actividad.grupos.alumnos',
            ])
            ->where('user_id', auth()->id())
            ->where('curso_id', $request->curso_id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('fechainicio', 'desc')
            ->get();

        // Material teórico
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
            'materia', 'curso', 'asignaciones', 'materialteoricoarchivos'
        ));
    }

    public function detalle(Request $request, ActividadAsignacion $asignacion)
    {
        abort_if($asignacion->user_id !== auth()->id(), 403);

        $asignacion->load([
            'actividad.tipoactividad',
            'actividad.items',
            'actividad.grupos.alumnos',
            'curso',
            'materia',
        ]);

        return view('asignaractividad.detalle', compact('asignacion'));
    }
}