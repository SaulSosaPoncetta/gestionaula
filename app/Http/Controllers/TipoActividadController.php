<?php

namespace App\Http\Controllers;

use App\Models\TipoActividad;
use Illuminate\Http\Request;

class TipoActividadController extends Controller
{
    public function index()
    {
        $tipos = TipoActividad::orderBy('denominacion')->paginate(15);
        return view('tiposactividad.index', compact('tipos'));
    }

    public function create()
    {
        return view('tiposactividad.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
            'descripcion'  => 'nullable|string',
        ]);

        TipoActividad::create($request->only('denominacion', 'descripcion'));

        return redirect()->route('tiposactividad.index')
                         ->with('success', 'Tipo de actividad creado correctamente.');
    }

    public function edit(TipoActividad $tiposactividad)
    {
        return view('tiposactividad.edit', compact('tiposactividad'));
    }

    public function update(Request $request, TipoActividad $tiposactividad)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
            'descripcion'  => 'nullable|string',
        ]);

        $tiposactividad->update($request->only('denominacion', 'descripcion'));

        return redirect()->route('tiposactividad.index')
                         ->with('success', 'Tipo de actividad actualizado correctamente.');
    }

    public function destroy(TipoActividad $tiposactividad)
    {
        $tiposactividad->delete();
        return redirect()->route('tiposactividad.index')
                         ->with('success', 'Tipo de actividad eliminado correctamente.');
    }
}