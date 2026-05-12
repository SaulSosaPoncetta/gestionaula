<?php

namespace App\Http\Controllers;

use App\Models\Contenido;
use App\Models\ContenidoSubtema;
use App\Models\Materia;
use Illuminate\Http\Request;

class ContenidoController extends Controller
{
    public function index(Request $request)
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        $query = Contenido::with(['materia', 'subtemas'])
            ->where('user_id', auth()->id())
            ->orderBy('fecha', 'desc');

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        $contenidos = $query->paginate(20);

        return view('contenidos.index', compact('contenidos', 'materias'));
    }

    public function create()
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        return view('contenidos.create', compact('materias'));
    }

public function store(Request $request)
{
    $request->validate([
        'materia_id'   => 'required|exists:materias,id',
        'numerounidad' => 'nullable|integer|min:1',
        'tema'         => 'required|string|max:500',
        'observacion'  => 'nullable|string',
        'subtemas'     => 'nullable|array|max:3',
        'subtemas.*'   => 'nullable|string|max:500',
    ]);

    $contenido = Contenido::create([
        'user_id'      => auth()->id(),
        'materia_id'   => $request->materia_id,
        'numerounidad' => $request->numerounidad,
        'tema'         => $request->tema,
        'fecha'        => now()->toDateString(),
        'observacion'  => $request->observacion,
    ]);

    if ($request->subtemas) {
        foreach ($request->subtemas as $orden => $subtema) {
            if (!empty(trim($subtema))) {
                ContenidoSubtema::create([
                    'contenido_id' => $contenido->id,
                    'subtema'      => $subtema,
                    'orden'        => $orden + 1,
                ]);
            }
        }
    }

    return redirect()->route('contenidos.index')
                     ->with('success', 'Contenido registrado correctamente.');
}

    public function edit(Contenido $contenido)
    {
        abort_if($contenido->user_id !== auth()->id(), 403);

        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $subtemas = $contenido->subtemas->pluck('subtema')->toArray();
        while (count($subtemas) < 3) {
            $subtemas[] = '';
        }
        return view('contenidos.edit', compact('contenido', 'materias', 'subtemas'));
    }
    
    public function show(Contenido $contenido)
    {
        abort_if($contenido->user_id !== auth()->id(), 403);
        $contenido->load('subtemas', 'materia');
        return view('contenidos.show', compact('contenido'));
    }
    
    public function update(Request $request, Contenido $contenido)
    {
        abort_if($contenido->user_id !== auth()->id(), 403);

        $request->validate([
            'materia_id'  => 'required|exists:materias,id',
            'tema'        => 'required|string|max:500',
            'observacion' => 'nullable|string',
            'subtemas'    => 'nullable|array|max:3',
            'subtemas.*'  => 'nullable|string|max:500',
        ]);

        $contenido->update([
            'materia_id'  => $request->materia_id,
            'tema'        => $request->tema,
            'fecha'       => now()->toDateString(),
            'observacion' => $request->observacion,
        ]);

        $contenido->subtemas()->delete();

        if ($request->subtemas) {
            foreach ($request->subtemas as $orden => $subtema) {
                if (!empty(trim($subtema))) {
                    ContenidoSubtema::create([
                        'contenido_id' => $contenido->id,
                        'subtema'      => $subtema,
                        'orden'        => $orden + 1,
                    ]);
                }
            }
        }

        return redirect()->route('contenidos.index')
                         ->with('success', 'Contenido actualizado correctamente.');
    }

    public function destroy(Contenido $contenido)
    {
        abort_if($contenido->user_id !== auth()->id(), 403);
        $contenido->subtemas()->delete();
        $contenido->delete();
        return redirect()->route('contenidos.index')
                         ->with('success', 'Contenido eliminado correctamente.');
    }
}