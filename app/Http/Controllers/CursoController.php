<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index()
    {
        $cursos = Curso::with('establecimiento.nivel')
            ->withCount(['alumnos', 'materias'])
            ->orderBy('nombre')
            ->paginate(15);

        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        $establecimientos = Establecimiento::with('nivel')->orderBy('nombre')->get();
        return view('cursos.create', compact('establecimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'             => 'required|string|max:100',
            'division'           => 'nullable|string|max:10',
            'turno'              => 'nullable|string|max:50',
            'nivel'              => 'nullable|string|max:50',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
        ]);

        Curso::create($request->only('nombre', 'division', 'turno', 'nivel', 'establecimiento_id'));

        return redirect()->route('cursos.index')
                         ->with('success', 'Curso creado correctamente.');
    }

   public function edit(Curso $curso)
    {
        $establecimientos = Establecimiento::with('nivel')->orderBy('nombre')->get();
        return view('cursos.edit', compact('curso', 'establecimientos'));
    }

    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'nombre'             => 'required|string|max:100',
            'division'           => 'nullable|string|max:10',
            'turno'              => 'nullable|string|max:50',
            'nivel'              => 'nullable|string|max:50',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
        ]);

        $curso->update($request->only('nombre', 'division', 'turno', 'nivel', 'establecimiento_id'));

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