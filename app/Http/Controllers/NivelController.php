<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function index()
    {
        $niveles = Nivel::withCount('establecimientos')->orderBy('tipo')->paginate(15);
        return view('niveles.index', compact('niveles'));
    }

    public function create()
    {
        $tipos = Nivel::TIPOS;
        return view('niveles.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo'   => 'required|in:' . implode(',', Nivel::TIPOS),
        ]);

        Nivel::create($request->only('nombre', 'tipo'));

        return redirect()->route('niveles.index')
                         ->with('success', 'Nivel creado correctamente.');
    }

    public function edit(Nivel $nivel)
    {
        $tipos = Nivel::TIPOS;
        return view('niveles.edit', compact('nivel', 'tipos'));
    }

    public function update(Request $request, Nivel $nivel)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo'   => 'required|in:' . implode(',', Nivel::TIPOS),
        ]);

        $nivel->update($request->only('nombre', 'tipo'));

        return redirect()->route('niveles.index')
                         ->with('success', 'Nivel actualizado correctamente.');
    }

    public function destroy(Nivel $nivel)
    {
        $nivel->delete();
        return redirect()->route('niveles.index')
                         ->with('success', 'Nivel eliminado correctamente.');
    }
}