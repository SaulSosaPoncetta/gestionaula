<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::withCount('calificaciones')
            ->orderBy('orden')
            ->get();

        return view('periodos.index', compact('periodos'));
    }

    public function create()
    {
        return view('periodos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
            'orden'        => 'required|integer|min:1',
        ]);

        Periodo::create($request->only('denominacion', 'orden'));

        return redirect()->route('periodos.index')
                         ->with('success', 'Período creado correctamente.');
    }

    public function edit(Periodo $periodo)
    {
        return view('periodos.edit', compact('periodo'));
    }

    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
            'orden'        => 'required|integer|min:1',
        ]);

        $periodo->update($request->only('denominacion', 'orden'));

        return redirect()->route('periodos.index')
                         ->with('success', 'Período actualizado correctamente.');
    }

    public function destroy(Periodo $periodo)
    {
        $periodo->delete();
        return redirect()->route('periodos.index')
                         ->with('success', 'Período eliminado correctamente.');
    }
}