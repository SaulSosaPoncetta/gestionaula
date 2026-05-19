<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadItem;
use App\Models\ActividadGrupo;
use App\Models\ActividadGrupoAlumno;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\TipoActividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        $query = Actividad::with(['materia', 'tipoactividad', 'items'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        $actividades = $query->paginate(15);

        return view('actividades.index', compact('actividades', 'materias'));
    }

    public function seleccionar()
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        return view('actividades.seleccionar', compact('materias'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $materia        = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $tiposactividad = TipoActividad::orderBy('denominacion')->get();

        $contenidos = \App\Models\Contenido::where('user_id', auth()->id())
            ->where('materia_id', $materia->id)
            ->with('subtemas')
            ->orderBy('numerounidad')
            ->orderBy('tema')
            ->get();

        return view('actividades.create', compact('materia', 'tiposactividad', 'contenidos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id'       => 'required|exists:materias,id',
            'tipoactividad_id' => 'required|exists:tiposactividad,id',
            'numerounidad'     => 'required|integer|min:1',
            'tema'             => 'required|string|max:300',
            'subtema'          => 'nullable|string|max:300',
            'numeroactividad'  => 'nullable|integer|min:1',
            'descripcion'      => 'nullable|string',
            'items'            => 'nullable|array',
            'items.*.numero'   => 'required|integer|min:1',
            'items.*.texto'    => 'required|string',
        ]);

        $actividad = Actividad::create([
            'user_id'          => auth()->id(),
            'materia_id'       => $request->materia_id,
            'tipoactividad_id' => $request->tipoactividad_id,
            'titulo'           => $request->tema,
            'numerounidad'     => $request->numerounidad,
            'numeroactividad'  => $request->numeroactividad,
            'tema'             => $request->tema,
            'subtema'          => $request->subtema,
            'descripcion'      => $request->descripcion,
            'estado'           => 'activa',
        ]);

        // Guardar items/consignas
        if ($request->items) {
            foreach ($request->items as $orden => $item) {
                if (empty(trim($item['texto']))) continue;
                ActividadItem::create([
                    'actividad_id' => $actividad->id,
                    'numeroitem'   => $item['numero'],
                    'texto'        => $item['texto'],
                    'orden'        => $orden + 1,
                ]);
            }
        }

        return redirect()->route('actividades.show', $actividad)
                         ->with('success', 'Actividad creada correctamente.');
    }

    public function show(Actividad $actividad)
    {
        abort_if($actividad->user_id !== auth()->id(), 403);
        $actividad->load(['materia', 'tipoactividad', 'items']);
        return view('actividades.show', compact('actividad'));
    }

    public function destroy(Actividad $actividad)
    {
        abort_if($actividad->user_id !== auth()->id(), 403);
        $actividad->delete();
        return redirect()->route('actividades.index')
                         ->with('success', 'Actividad eliminada correctamente.');
    }
}