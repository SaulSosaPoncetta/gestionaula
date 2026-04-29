<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Curso;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::with('curso')->orderBy('nombre')->paginate(15);
        return view('materias.index', compact('materias'));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();
        return view('materias.create', compact('cursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        Materia::create($request->only('nombre', 'curso_id'));

        return redirect()->route('materias.index')
                         ->with('success', 'Materia creada correctamente.');
    }

    public function edit(Materia $materia)
    {
        $cursos = Curso::orderBy('nombre')->get();
        return view('materias.edit', compact('materia', 'cursos'));
    }

    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        $materia->update($request->only('nombre', 'curso_id'));

        return redirect()->route('materias.index')
                         ->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();
        return redirect()->route('materias.index')
                         ->with('success', 'Materia eliminada correctamente.');
    }
}