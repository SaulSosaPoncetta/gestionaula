<?php

namespace App\Http\Controllers;

use App\Models\Declaracion;
use App\Models\DeclaracionItem;
use App\Models\Horario;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DeclaracionController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    public function index()
    {
        try {
            $declaraciones = Declaracion::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('declaracion.index', compact('declaraciones'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $user = auth()->user();
            $dias = self::DIAS;

            $horarios = Horario::with(['curso', 'materia', 'establecimiento'])
                ->where('user_id', $user->id)
                ->orderByRaw("CASE dia
                    WHEN 'lunes'     THEN 1
                    WHEN 'martes'    THEN 2
                    WHEN 'miercoles' THEN 3
                    WHEN 'jueves'    THEN 4
                    WHEN 'viernes'   THEN 5
                    WHEN 'sabado'    THEN 6
                    WHEN 'domingo'   THEN 7
                    ELSE 8 END")
                ->orderBy('horainicio')
                ->get();

            $establecimientos = Establecimiento::where('user_id', auth()->id())
                ->orderBy('nombre')->get();
            $cicloactual      = date('Y');

            return view('declaracion.create', compact(
                'horarios', 'dias', 'cicloactual', 'establecimientos'
            ));

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
                'ciclo'                      => 'required|string|max:20',
                'fechadeclaracion'           => 'required|date',
                'items'                      => 'required|array|min:1',
                'items.*.dia'                => 'required|in:' . implode(',', self::DIAS),
                'items.*.horainicio'         => 'required|date_format:H:i,H:i:s',
                'items.*.horafin'            => 'required|date_format:H:i,H:i:s',
                'items.*.establecimiento_id' => 'nullable|exists:establecimientos,id',
                'items.*.curso_id'           => 'nullable|exists:cursos,id',
                'items.*.materia_id'         => 'nullable|exists:materias,id',
            ]);

            $declaracion = Declaracion::create([
                'user_id'          => auth()->id(),
                'ciclo'            => $request->ciclo,
                'fechadeclaracion' => $request->fechadeclaracion,
                'estado'           => 'borrador',
            ]);

            foreach ($request->items as $item) {
                if (empty($item['dia'])) continue;

                DeclaracionItem::create([
                    'declaracion_id'     => $declaracion->id,
                    'establecimiento_id' => $item['establecimiento_id'] ?? null,
                    'curso_id'           => $item['curso_id'] ?? null,
                    'materia_id'         => $item['materia_id'] ?? null,
                    'dia'                => $item['dia'],
                    'horainicio'         => substr($item['horainicio'], 0, 5),
                    'horafin'            => substr($item['horafin'], 0, 5),
                ]);
            }

            return redirect()->route('declaracion.show', $declaracion)
                             ->with('success', 'Declaración guardada como borrador.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(Declaracion $declaracion)
    {
        try {
            abort_if($declaracion->user_id !== auth()->id(), 403);
            abort_if($declaracion->estado !== 'borrador', 403,
                'Solo se pueden editar declaraciones en borrador.');

            $user  = auth()->user();
            $dias  = self::DIAS;

            // Cargar items existentes como si fueran horarios para reutilizar el form
            $horarios = $declaracion->items->map(function($item) {
                return (object)[
                    'dia'                => $item->dia,
                    'horainicio'         => $item->horainicio,
                    'horafin'            => $item->horafin,
                    'establecimiento_id' => $item->establecimiento_id,
                    'curso_id'           => $item->curso_id,
                    'materia_id'         => $item->materia_id,
                ];
            });

            $establecimientos = \App\Models\Establecimiento::where('user_id', auth()->id())
                ->orderBy('nombre')->get();
            $cicloactual = $declaracion->ciclo;

            return view('declaracion.edit', compact(
                'declaracion', 'horarios', 'dias', 'cicloactual', 'establecimientos'
            ));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, Declaracion $declaracion)
    {
        try {
            abort_if($declaracion->user_id !== auth()->id(), 403);
            abort_if($declaracion->estado !== 'borrador', 403,
                'Solo se pueden editar declaraciones en borrador.');

            $request->validate([
                'ciclo'                      => 'required|string|max:20',
                'fechadeclaracion'           => 'required|date',
                'items'                      => 'required|array|min:1',
                'items.*.dia'                => 'required|in:' . implode(',', self::DIAS),
                'items.*.horainicio'         => 'required|date_format:H:i,H:i:s',
                'items.*.horafin'            => 'required|date_format:H:i,H:i:s',
                'items.*.establecimiento_id' => 'nullable|exists:establecimientos,id',
                'items.*.curso_id'           => 'nullable|exists:cursos,id',
                'items.*.materia_id'         => 'nullable|exists:materias,id',
            ]);

            $declaracion->update([
                'ciclo'            => $request->ciclo,
                'fechadeclaracion' => $request->fechadeclaracion,
            ]);

            // Reemplazar todos los items
            $declaracion->items()->delete();

            foreach ($request->items as $item) {
                if (empty($item['dia'])) continue;

                DeclaracionItem::create([
                    'declaracion_id'     => $declaracion->id,
                    'establecimiento_id' => $item['establecimiento_id'] ?? null,
                    'curso_id'           => $item['curso_id'] ?? null,
                    'materia_id'         => $item['materia_id'] ?? null,
                    'dia'                => $item['dia'],
                    'horainicio'         => substr($item['horainicio'], 0, 5),
                    'horafin'            => substr($item['horafin'], 0, 5),
                ]);
            }

            return redirect()->route('declaracion.show', $declaracion)
                             ->with('success', 'Declaración actualizada correctamente.');

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

    public function show(Declaracion $declaracion)
    {
        try {
            abort_if($declaracion->user_id !== auth()->id(), 403);

            $declaracion->load(['docente', 'items.curso', 'items.materia', 'items.establecimiento']);
            $dias = self::DIAS;

            $itemspordia = collect($dias)->mapWithKeys(fn($d) => [
                $d => $declaracion->items->where('dia', $d)->sortBy('horainicio')
            ]);

            return view('declaracion.show', compact('declaracion', 'itemspordia', 'dias'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function presentar(Declaracion $declaracion)
    {
        try {
            abort_if($declaracion->user_id !== auth()->id(), 403);

            if ($declaracion->estado !== 'borrador') {
                return redirect()->back()->with('error', 'Solo se pueden presentar declaraciones en borrador.');
            }

            $declaracion->update([
                'estado'            => 'presentada',
                'fechapresentacion' => now(),
            ]);

            return redirect()->route('declaracion.show', $declaracion)
                             ->with('success', 'Declaración presentada correctamente.');

        } catch (QueryException $e) {
            Log::error('Controllers.presentar - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.presentar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }
}