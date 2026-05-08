<?php
namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Entrega;
use App\Models\Curso;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index()
    {
        $tareas = Tarea::with(['curso', 'materia'])
            ->where('user_id', auth()->id())
            ->orderBy('fechavencimiento', 'desc')
            ->paginate(15);

        return view('tareas.index', compact('tareas'));
    }

    public function create()
    {
        $cursos = Curso::where('user_id', auth()->id())
            ->with('materias')
            ->orderBy('anio')->orderBy('division')->get();

        return view('tareas.create', compact('cursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'curso_id'         => 'required|exists:cursos,id',
            'materia_id'       => 'nullable|exists:materias,id',
            'titulo'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'fechavencimiento' => 'required|date',
        ]);

        $tarea = Tarea::create([
            'curso_id'         => $request->curso_id,
            'materia_id'       => $request->materia_id,
            'user_id'          => auth()->id(),
            'titulo'           => $request->titulo,
            'descripcion'      => $request->descripcion,
            'fechavencimiento' => $request->fechavencimiento,
            'estado'           => 'activa',
        ]);

        $curso = Curso::where('user_id', auth()->id())->with('alumnos')->find($request->curso_id);
        foreach ($curso->alumnos as $alumno) {
            Entrega::create([
                'tarea_id'  => $tarea->id,
                'alumno_id' => $alumno->id,
                'estado'    => 'pendiente',
            ]);
        }

        return redirect()->route('tareas.index')
                         ->with('success', 'Práctico creado correctamente.');
    }

    public function show(Tarea $tarea)
    {
        abort_if($tarea->user_id !== auth()->id(), 403);
        $tarea->load(['curso', 'materia', 'entregas.alumno']);
        $entregas = $tarea->entregas->sortBy(fn($e) => $e->alumno->apellido);
        return view('tareas.show', compact('tarea', 'entregas'));
    }

    public function actualizarentregas(Request $request, Tarea $tarea)
    {
        abort_if($tarea->user_id !== auth()->id(), 403);

        foreach ($request->entregas ?? [] as $entregaId => $datos) {
            Entrega::where('id', $entregaId)->update([
                'estado'       => $datos['estado'] ?? 'pendiente',
                'observacion'  => $datos['observacion'] ?? null,
                'fechaentrega' => $datos['estado'] !== 'pendiente' ? now()->toDateString() : null,
            ]);
        }

        return redirect()->route('tareas.show', $tarea)
                         ->with('success', 'Entregas actualizadas correctamente.');
    }

    public function cerrar(Tarea $tarea)
    {
        abort_if($tarea->user_id !== auth()->id(), 403);
        $tarea->update(['estado' => 'cerrada']);
        return redirect()->route('tareas.index')
                         ->with('success', 'Práctico cerrado correctamente.');
    }
}