<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::withCount('materias')->orderBy('nombre')->paginate(15);
        return view('especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        Especialidad::create($request->only('nombre', 'descripcion'));

        return redirect()->route('especialidades.index')
                         ->with('success', 'Especialidad creada correctamente.');
    }

    public function edit(Especialidad $especialidad)
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);

        $especialidad->update($request->only('nombre', 'descripcion'));

        return redirect()->route('especialidades.index')
                         ->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy(Especialidad $especialidad)
    {
        $especialidad->delete();
        return redirect()->route('especialidades.index')
                         ->with('success', 'Especialidad eliminada correctamente.');
    }
}