<?php

namespace App\Http\Controllers;

use App\Models\CalendarioEscolar;
use App\Models\Periodo;
use Illuminate\Http\Request;

class CalendarioEscolarController extends Controller
{
    public function index()
    {
        $eventos = CalendarioEscolar::with('periodo')
            ->where('user_id', auth()->id())
            ->orderBy('fecha')
            ->paginate(20);

        return view('calendarioescolar.index', compact('eventos'));
    }

    public function create()
    {
        $periodos = Periodo::orderBy('orden')->get();
        return view('calendarioescolar.create', compact('periodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'        => 'required|date',
            'denominacion' => 'required|string|max:300',
            'periodo_id'   => 'nullable|exists:periodos,id',
            'esferiado'    => 'nullable|boolean',
            'fechainicio'  => 'nullable|date',
            'fechafin'     => 'nullable|date|after_or_equal:fechainicio',
        ]);

        CalendarioEscolar::create([
            'user_id'      => auth()->id(),
            'fecha'        => $request->fecha,
            'denominacion' => $request->denominacion,
            'periodo_id'   => $request->periodo_id,
            'esferiado'    => $request->boolean('esferiado'),
            'fechainicio'  => $request->fechainicio,
            'fechafin'     => $request->fechafin,
        ]);

        return redirect()->route('calendarioescolar.index')
                         ->with('success', 'Evento creado correctamente.');
    }

    public function edit(CalendarioEscolar $calendarioescolar)
    {
        abort_if($calendarioescolar->user_id !== auth()->id(), 403);
        $periodos = Periodo::orderBy('orden')->get();
        return view('calendarioescolar.edit', compact('calendarioescolar', 'periodos'));
    }

    public function update(Request $request, CalendarioEscolar $calendarioescolar)
    {
        abort_if($calendarioescolar->user_id !== auth()->id(), 403);

        $request->validate([
            'fecha'        => 'required|date',
            'denominacion' => 'required|string|max:300',
            'periodo_id'   => 'nullable|exists:periodos,id',
            'esferiado'    => 'nullable|boolean',
            'fechainicio'  => 'nullable|date',
            'fechafin'     => 'nullable|date|after_or_equal:fechainicio',
        ]);

        $calendarioescolar->update([
            'fecha'        => $request->fecha,
            'denominacion' => $request->denominacion,
            'periodo_id'   => $request->periodo_id,
            'esferiado'    => $request->boolean('esferiado'),
            'fechainicio'  => $request->fechainicio,
            'fechafin'     => $request->fechafin,
        ]);

        return redirect()->route('calendarioescolar.index')
                         ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(CalendarioEscolar $calendarioescolar)
    {
        abort_if($calendarioescolar->user_id !== auth()->id(), 403);
        $calendarioescolar->delete();
        return redirect()->route('calendarioescolar.index')
                         ->with('success', 'Evento eliminado correctamente.');
    }
}