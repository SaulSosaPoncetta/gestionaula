<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Curso;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $query = Alumno::with('curso')->orderBy('apellido');

        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('apellido', 'like', '%' . $request->buscar . '%')
                  ->orWhere('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('dni', 'like', '%' . $request->buscar . '%');
            });
        }

        $alumnos = $query->paginate(20);

        return view('alumnos.index', compact('alumnos', 'cursos'));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();
        return view('alumnos.create', compact('cursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'dni'             => 'nullable|string|max:20|unique:alumnos,dni',
            'fechanacimiento' => 'nullable|date',
            'curso_id'        => 'required|exists:cursos,id',
        ]);

        Alumno::create($request->only('nombre', 'apellido', 'dni', 'fechanacimiento', 'curso_id'));

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno creado correctamente.');
    }

    public function edit(Alumno $alumno)
    {
        $cursos = Curso::orderBy('nombre')->get();
        return view('alumnos.edit', compact('alumno', 'cursos'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'dni'             => 'nullable|string|max:20|unique:alumnos,dni,' . $alumno->id,
            'fechanacimiento' => 'nullable|date',
            'curso_id'        => 'required|exists:cursos,id',
        ]);

        $alumno->update($request->only('nombre', 'apellido', 'dni', 'fechanacimiento', 'curso_id'));

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno eliminado correctamente.');
    }
}