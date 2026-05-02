<?php

namespace App\Http\Controllers;

use App\Models\Declaracion;
use App\Models\DeclaracionItem;
use App\Models\Horario;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class DeclaracionController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    public function index()
    {
        $declaraciones = Declaracion::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('declaracion.index', compact('declaraciones'));
    }

    /**
     * Formulario de nueva declaración.
     * Pre-carga los ítems desde la tabla horarios del docente.
     */
    public function create()
    {
        $user = auth()->user();
        $dias = self::DIAS;

        // Cargar horarios del docente como base
        $horarios = Horario::with(['curso', 'materia', 'establecimiento'])
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(dia, 'lunes','martes','miercoles','jueves','viernes','sabado')")
            ->orderBy('horainicio')
            ->get();

        $establecimientos = Establecimiento::orderBy('nombre')->get();
        $cicloactual      = date('Y');

        return view('declaracion.create', compact(
            'horarios', 'dias', 'cicloactual', 'establecimientos'
        ));
    }

    /**
     * Guarda la declaración.
     * Los ítems vienen del formulario (pre-llenados desde horarios, editables).
     */
    public function store(Request $request)
    {
        $request->validate([
            'ciclo'                      => 'required|string|max:20',
            'fechadeclaracion'           => 'required|date',
            'items'                      => 'required|array|min:1',
            'items.*.dia'                => 'required|in:' . implode(',', self::DIAS),
            'items.*.horainicio'         => 'required|date_format:H:i',
            'items.*.horafin'            => 'required|date_format:H:i',
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
                'horainicio'         => $item['horainicio'],
                'horafin'            => $item['horafin'],
            ]);
        }

        return redirect()->route('declaracion.show', $declaracion)
                         ->with('success', 'Declaración guardada como borrador.');
    }

    public function show(Declaracion $declaracion)
    {
        $declaracion->load(['docente', 'items.curso', 'items.materia', 'items.establecimiento']);
        $dias = self::DIAS;

        $itemspordia = collect($dias)->mapWithKeys(fn($d) => [
            $d => $declaracion->items->where('dia', $d)->sortBy('horainicio')
        ]);

        return view('declaracion.show', compact('declaracion', 'itemspordia', 'dias'));
    }

    public function presentar(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) abort(403);

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
