<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index()
    {
        $cursos = Curso::withCount(['alumnos', 'materias'])->orderBy('nombre')->paginate(15);
        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('cursos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'division' => 'nullable|string|max:10',
            'turno'    => 'nullable|string|max:50',
            'nivel'    => 'nullable|string|max:50',
        ]);

        Curso::create($request->only('nombre', 'division', 'turno', 'nivel'));

        return redirect()->route('cursos.index')
                         ->with('success', 'Curso creado correctamente.');
    }

    public function edit(Curso $curso)
    {
        return view('cursos.edit', compact('curso'));
    }

    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'division' => 'nullable|string|max:10',
            'turno'    => 'nullable|string|max:50',
            'nivel'    => 'nullable|string|max:50',
        ]);

        $curso->update($request->only('nombre', 'division', 'turno', 'nivel'));

        return redirect()->route('cursos.index')
                         ->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();
        return redirect()->route('cursos.index')
                         ->with('success', 'Curso eliminado correctamente.');
    }
}