<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\Nivel;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    public function index()
    {
        $establecimientos = Establecimiento::with('nivel')
            ->withCount(['cursos', 'docentes'])
            ->orderBy('nombre')
            ->paginate(15);

        return view('establecimientos.index', compact('establecimientos'));
    }

    public function create()
    {
        $niveles = Nivel::orderBy('tipo')->get();
        $modalidades = Establecimiento::MODALIDADES;
        return view('establecimientos.create', compact('niveles', 'modalidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:200',
            'cue'       => 'nullable|string|max:20|unique:establecimientos,cue',
            'modalidad' => 'required|in:' . implode(',', Establecimiento::MODALIDADES),
            'nivel_id'  => 'required|exists:niveles,id',
            'direccion' => 'nullable|string|max:200',
            'localidad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:100',
        ]);

        Establecimiento::create($request->only(
            'nombre', 'cue', 'modalidad', 'nivel_id',
            'direccion', 'localidad', 'provincia', 'telefono', 'email'
        ));

        return redirect()->route('establecimientos.index')
                         ->with('success', 'Establecimiento creado correctamente.');
    }

    public function show(Establecimiento $establecimiento)
    {
        $establecimiento->load(['nivel', 'cursos.alumnos', 'docentes']);
        return view('establecimientos.show', compact('establecimiento'));
    }

    public function edit(Establecimiento $establecimiento)
    {
        $niveles = Nivel::orderBy('tipo')->get();
        $modalidades = Establecimiento::MODALIDADES;
        return view('establecimientos.edit', compact('establecimiento', 'niveles', 'modalidades'));
    }

    public function update(Request $request, Establecimiento $establecimiento)
    {
        $request->validate([
            'nombre'    => 'required|string|max:200',
            'cue'       => 'nullable|string|max:20|unique:establecimientos,cue,' . $establecimiento->id,
            'modalidad' => 'required|in:' . implode(',', Establecimiento::MODALIDADES),
            'nivel_id'  => 'required|exists:niveles,id',
            'direccion' => 'nullable|string|max:200',
            'localidad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:100',
        ]);

        $establecimiento->update($request->only(
            'nombre', 'cue', 'modalidad', 'nivel_id',
            'direccion', 'localidad', 'provincia', 'telefono', 'email'
        ));

        return redirect()->route('establecimientos.index')
                         ->with('success', 'Establecimiento actualizado correctamente.');
    }

    public function destroy(Establecimiento $establecimiento)
    {
        $establecimiento->delete();
        return redirect()->route('establecimientos.index')
                         ->with('success', 'Establecimiento eliminado correctamente.');
    }
}