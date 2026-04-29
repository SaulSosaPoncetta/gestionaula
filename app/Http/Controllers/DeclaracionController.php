<?php

namespace App\Http\Controllers;

use App\Models\Declaracion;
use App\Models\DeclaracionItem;
use App\Models\Curso;
use Illuminate\Http\Request;

class DeclaracionController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('director')) {
            $declaraciones = Declaracion::with('docente')
                ->orderBy('created_at', 'desc')->paginate(15);
        } else {
            $declaraciones = Declaracion::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')->paginate(15);
        }

        return view('declaracion.index', compact('declaraciones'));
    }

    public function create()
    {
        $user = auth()->user();
        $dias = self::DIAS;

        $cursos = Curso::whereHas('docentes', fn($q) => $q->where('users.id', $user->id))
                       ->with('materias')->orderBy('nombre')->get();

        $cicloactual = date('Y');

        return view('declaracion.create', compact('cursos', 'dias', 'cicloactual'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ciclo'              => 'required|string',
            'items'              => 'required|array|min:1',
            'items.*.dia'        => 'required|in:' . implode(',', self::DIAS),
            'items.*.horainicio' => 'required|date_format:H:i',
            'items.*.horafin'    => 'required|date_format:H:i',
        ]);

        $declaracion = Declaracion::create([
            'user_id' => auth()->id(),
            'ciclo'   => $request->ciclo,
            'estado'  => 'borrador',
        ]);

        foreach ($request->items as $item) {
            if (empty($item['dia'])) continue;

            DeclaracionItem::create([
                'declaracion_id' => $declaracion->id,
                'curso_id'       => $item['curso_id'] ?? null,
                'materia_id'     => $item['materia_id'] ?? null,
                'dia'            => $item['dia'],
                'horainicio'     => $item['horainicio'],
                'horafin'        => $item['horafin'],
                'actividad'      => $item['actividad'] ?? null,
            ]);
        }

        return redirect()->route('declaracion.show', $declaracion)
                         ->with('success', 'Declaración guardada como borrador.');
    }

    public function show(Declaracion $declaracion)
    {
        $declaracion->load(['docente', 'items.curso', 'items.materia', 'resolutor']);
        $dias = self::DIAS;

        $itemspordía = collect($dias)->mapWithKeys(fn($d) => [
            $d => $declaracion->items->where('dia', $d)->sortBy('horainicio')
        ]);

        return view('declaracion.show', compact('declaracion', 'itemspordía', 'dias'));
    }

    public function presentar(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) abort(403);
        if ($declaracion->estado !== 'borrador') {
            return redirect()->back()->with('error', 'Solo se pueden presentar declaraciones en borrador.');
        }

        $declaracion->update([
            'estado'             => 'presentada',
            'fechapresentacion'  => now(),
        ]);

        return redirect()->route('declaracion.show', $declaracion)
                         ->with('success', 'Declaración presentada correctamente.');
    }

    public function resolver(Request $request, Declaracion $declaracion)
    {
        $request->validate([
            'estado'      => 'required|in:aprobada,rechazada',
            'observacion' => 'nullable|string',
        ]);

        $declaracion->update([
            'estado'           => $request->estado,
            'observacion'      => $request->observacion,
            'fecharesolucion'  => now(),
            'resueltopor'      => auth()->id(),
        ]);

        return redirect()->route('declaracion.show', $declaracion)
                         ->with('success', 'Declaración ' . $request->estado . ' correctamente.');
    }
}