<?php

namespace App\Http\Controllers;

use App\Models\AreaFormacion;
use Illuminate\Http\Request;

class AreaFormacionController extends Controller
{
    public function index()
    {
        $areas = AreaFormacion::withCount('materias')->orderBy('nombre')->paginate(15);
        return view('areasformacion.index', compact('areas'));
    }

    public function create()
    {
        return view('areasformacion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        AreaFormacion::create($request->only('nombre', 'descripcion'));

        return redirect()->route('areasformacion.index')
                         ->with('success', 'Área de formación creada correctamente.');
    }

    public function edit(AreaFormacion $areasformacion)
    {
        return view('areasformacion.edit', ['area' => $areasformacion]);
    }

    public function update(Request $request, AreaFormacion $areasformacion)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        $areasformacion->update($request->only('nombre', 'descripcion'));

        return redirect()->route('areasformacion.index')
                         ->with('success', 'Área de formación actualizada correctamente.');
    }

    public function destroy(AreaFormacion $areasformacion)
    {
        $areasformacion->delete();
        return redirect()->route('areasformacion.index')
                         ->with('success', 'Área de formación eliminada correctamente.');
    }
}