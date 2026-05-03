<?php

namespace App\Http\Controllers;

use App\Models\TipoEvaluacion;
use Illuminate\Http\Request;

class TipoEvaluacionController extends Controller
{
    public function index()
    {
        $tipos = TipoEvaluacion::withCount('calificaciones')
            ->orderBy('denominacion')
            ->get();

        return view('tiposevaluacion.index', compact('tipos'));
    }

    public function create()
    {
        return view('tiposevaluacion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
        ]);

        TipoEvaluacion::create($request->only('denominacion'));

        return redirect()->route('tiposevaluacion.index')
                         ->with('success', 'Tipo de evaluación creado correctamente.');
    }

    public function edit(TipoEvaluacion $tiposevaluacion)
    {
        return view('tiposevaluacion.edit', compact('tiposevaluacion'));
    }

    public function update(Request $request, TipoEvaluacion $tiposevaluacion)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
        ]);

        $tiposevaluacion->update($request->only('denominacion'));

        return redirect()->route('tiposevaluacion.index')
                         ->with('success', 'Tipo de evaluación actualizado correctamente.');
    }

    public function destroy(TipoEvaluacion $tiposevaluacion)
    {
        $tiposevaluacion->delete();
        return redirect()->route('tiposevaluacion.index')
                         ->with('success', 'Tipo de evaluación eliminado correctamente.');
    }
}