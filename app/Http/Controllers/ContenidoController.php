<?php

namespace App\Http\Controllers;

use App\Models\Contenido;
use App\Models\ContenidoSubtema;
use App\Models\Materia;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ContenidoController extends Controller
{
    use DetectaHorarioActivo;

    public function index(Request $request)
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

            // Preseleccionar materia del horario activo si no hay filtro
            if (!$request->filled('materia_id')) {
                $horario = $this->detectarHorarioActivo();
                if ($horario?->materia_id) {
                    $request->merge(['materia_id' => $horario->materia_id]);
                }
            }

            $query = Contenido::with(['materia', 'subtemas'])
                ->where('user_id', auth()->id())
                ->orderBy('numerounidad')
                ->orderBy('created_at');

            if ($request->filled('materia_id')) {
                $query->where('materia_id', $request->materia_id);
            }

            $contenidos = $query->get();
            $porUnidad  = $contenidos->groupBy(fn($c) => $c->numerounidad ?? 'sin_unidad');

            return view('contenidos.index', compact('contenidos', 'porUnidad', 'materias'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create(Request $request)
    {
        try {
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

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(Contenido $contenido)
    {
        try {
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

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
    
    public function show(Contenido $contenido)
    {
        try {
            abort_if($contenido->user_id !== auth()->id(), 403);
            $contenido->load('subtemas', 'materia');
            return view('contenidos.show', compact('contenido'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
    
    public function update(Request $request, Contenido $contenido)
    {
        try {
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('Controllers.update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function destroy(Contenido $contenido)
    {
        try {
            abort_if($contenido->user_id !== auth()->id(), 403);
            $contenido->subtemas()->delete();
            $contenido->delete();
        return redirect()->route('contenidos.index', ['materia_id' => $contenido->materia_id])
                     ->with('success', 'Contenido eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('Controllers.destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }
}