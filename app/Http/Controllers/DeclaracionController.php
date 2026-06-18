<?php

namespace App\Http\Controllers;

use App\Models\Declaracion;
use App\Models\DeclaracionItem;
use App\Models\Horario;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class DeclaracionController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    public function index()
    {
        $declaraciones = Declaracion::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('declaracion.index', compact('declaraciones'));
    }

    public function create()
    {
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
    }

    public function store(Request $request)
    {
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
    }

    public function edit(Declaracion $declaracion)
    {
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
    }

    public function update(Request $request, Declaracion $declaracion)
    {
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
    }

    public function show(Declaracion $declaracion)
    {
        abort_if($declaracion->user_id !== auth()->id(), 403);

        $declaracion->load(['docente', 'items.curso', 'items.materia', 'items.establecimiento']);
        $dias = self::DIAS;

        $itemspordia = collect($dias)->mapWithKeys(fn($d) => [
            $d => $declaracion->items->where('dia', $d)->sortBy('horainicio')
        ]);

        return view('declaracion.show', compact('declaracion', 'itemspordia', 'dias'));
    }

    public function presentar(Declaracion $declaracion)
    {
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
    }
}