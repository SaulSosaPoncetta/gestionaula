<?php

namespace App\Http\Controllers;

use App\Models\Contenido;
use App\Models\ContenidoSubtema;
use App\Models\Materia;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;

class ContenidoController extends Controller
{
    use DetectaHorarioActivo;

    public function index(Request $request)
    {
        $horario       = $this->detectarHorarioActivo();
        $materiaActiva = $horario?->materia_id;

        // Preseleccionar materia activa si no hay filtro
        if (!$request->filled('materia_id') && $materiaActiva) {
            $request->merge(['materia_id' => $materiaActiva]);
        }

        // Obtener materias del horario del docente (ordenadas por día/hora)
        $materiasEnHorario = \App\Models\Horario::where('user_id', auth()->id())
            ->with('materia')->get()
            ->pluck('materia')->filter()->unique('id')
            ->keyBy('id');

        // Todas las materias: activa primero, luego las del horario, luego las demás
        $todasMaterias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $materias = $todasMaterias->sortBy(function ($m) use ($materiaActiva, $materiasEnHorario) {
            if ($m->id === $materiaActiva)              return '0_' . $m->nombre;
            if ($materiasEnHorario->has($m->id))        return '1_' . $m->nombre;
            return '2_' . $m->nombre;
        })->values();

        $query = Contenido::with(['materia', 'subtemas'])
            ->where('user_id', auth()->id())
            ->orderBy('numerounidad')
            ->orderBy('created_at');

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        $contenidos = $query->get();
        $porUnidad  = $contenidos->groupBy(fn($c) => $c->numerounidad ?? 'sin_unidad');

        return view('contenidos.index', compact(
            'contenidos', 'porUnidad', 'materias', 'materiaActiva'
        ));
    }

    public function create(Request $request)
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        // Cargar temas ya existentes de la unidad (para mostrarlos en modo lectura)
        $temasExistentes = collect();
        if ($request->filled('materia_id') && $request->filled('numerounidad')) {
            $temasExistentes = Contenido::with('subtemas')
                ->where('user_id', auth()->id())
                ->where('materia_id', $request->materia_id)
                ->where('numerounidad', $request->numerounidad)
                ->orderBy('created_at')
                ->get();
        }

        return view('contenidos.create', compact('materias', 'temasExistentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id'      => 'required|exists:materias,id',
            'numerounidad'    => 'nullable|integer|min:1',
            'observacion'     => 'nullable|string',
            'temas'           => 'required|array|min:1',
            'temas.*.tema'    => 'required|string|max:500',
            'temas.*.subtemas'=> 'nullable|array',
            'temas.*.subtemas.*' => 'nullable|string|max:500',
        ]);

        foreach ($request->temas as $temaData) {
            if (empty(trim($temaData['tema'] ?? ''))) continue;

            $contenido = Contenido::create([
                'user_id'      => auth()->id(),
                'materia_id'   => $request->materia_id,
                'numerounidad' => $request->numerounidad,
                'tema'         => $temaData['tema'],
                'fecha'        => now()->toDateString(),
                'observacion'  => $request->observacion,
            ]);

            foreach ($temaData['subtemas'] ?? [] as $orden => $subtema) {
                if (!empty(trim($subtema))) {
                    ContenidoSubtema::create([
                        'contenido_id' => $contenido->id,
                        'subtema'      => $subtema,
                        'orden'        => $orden + 1,
                    ]);
                }
            }
        }

        return redirect()->route('contenidos.index', ['materia_id' => $request->materia_id])
                         ->with('success', 'Contenidos registrados correctamente.');
    }

    public function edit(Contenido $contenido)
    {
        abort_if($contenido->user_id !== auth()->id(), 403);
        $contenido->load('subtemas', 'materia');
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        // Todos los temas de la misma unidad para editarlos juntos
        $temasUnidad = Contenido::with('subtemas')
            ->where('user_id', auth()->id())
            ->where('materia_id', $contenido->materia_id)
            ->where('numerounidad', $contenido->numerounidad)
            ->orderBy('created_at')
            ->get();

        return view('contenidos.edit', compact('contenido', 'materias', 'temasUnidad'));
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
            'materia_id'   => 'required|exists:materias,id',
            'numerounidad' => 'nullable|integer|min:1',
            'tema'         => 'required|string|max:500',
            'observacion'  => 'nullable|string',
            'subtemas'     => 'nullable|array',
            'subtemas.*'   => 'nullable|string|max:500',
        ]);

        $contenido->update([
            'materia_id'   => $request->materia_id,
            'numerounidad' => $request->numerounidad,
            'tema'         => $request->tema,
            'observacion'  => $request->observacion,
        ]);

        $contenido->subtemas()->delete();

        foreach ($request->subtemas ?? [] as $orden => $subtema) {
            if (!empty(trim($subtema))) {
                ContenidoSubtema::create([
                    'contenido_id' => $contenido->id,
                    'subtema'      => $subtema,
                    'orden'        => $orden + 1,
                ]);
            }
        }

        return redirect()->route('contenidos.index', ['materia_id' => $contenido->materia_id])
                         ->with('success', 'Contenido actualizado correctamente.');
    }

    public function destroy(Contenido $contenido)
    {
        abort_if($contenido->user_id !== auth()->id(), 403);
        $contenido->subtemas()->delete();
        $contenido->delete();
return redirect()->route('contenidos.index', ['materia_id' => $contenido->materia_id])
                 ->with('success', 'Contenido eliminado correctamente.');
    }
}