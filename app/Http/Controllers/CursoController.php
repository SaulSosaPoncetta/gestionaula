<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Establecimiento;
use App\Models\Nivel;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    const ANIOS = ['1ro', '2do', '3ro', '4to', '5to', '6to', '7mo'];

    public function index()
    {
        $cursos = Curso::with(['establecimiento', 'nivel', 'especialidad'])
            ->withCount(['alumnos', 'materias'])
            ->orderBy('anio')
            ->orderBy('division')
            ->paginate(15);

        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        $establecimientos = Establecimiento::with('nivel')->orderBy('nombre')->get();
        $niveles          = Nivel::orderBy('nombre')->get();
        $especialidades   = Especialidad::orderBy('nombre')->get();
        $anios            = self::ANIOS;

        return view('cursos.create', compact('establecimientos', 'niveles', 'especialidades', 'anios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anio'               => 'nullable|string|max:10',
            'division'           => 'nullable|string|max:10',
            'turno'              => 'nullable|string|max:50',
            'nivel_id'           => 'nullable|exists:niveles,id',
            'especialidad_id'    => 'nullable|exists:especialidades,id',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
        ]);

        // nombre generado: "3ro A"
        $nombre = trim(($request->anio ?? '') . ' ' . ($request->division ?? ''));

        Curso::create([
            'nombre'             => $nombre ?: 'Sin nombre',
            'anio'               => $request->anio,
            'division'           => $request->division,
            'turno'              => $request->turno,
            'nivel_id'           => $request->nivel_id,
            'especialidad_id'    => $request->especialidad_id,
            'establecimiento_id' => $request->establecimiento_id,
        ]);

        return redirect()->route('cursos.index')
                         ->with('success', 'Curso creado correctamente.');
    }

    public function edit(Curso $curso)
    {
        $establecimientos = Establecimiento::with('nivel')->orderBy('nombre')->get();
        $niveles          = Nivel::orderBy('nombre')->get();
        $especialidades   = Especialidad::orderBy('nombre')->get();
        $anios            = self::ANIOS;

        return view('cursos.edit', compact('curso', 'establecimientos', 'niveles', 'especialidades', 'anios'));
    }

    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'anio'               => 'nullable|string|max:10',
            'division'           => 'nullable|string|max:10',
            'turno'              => 'nullable|string|max:50',
            'nivel_id'           => 'nullable|exists:niveles,id',
            'especialidad_id'    => 'nullable|exists:especialidades,id',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
        ]);

        $nombre = trim(($request->anio ?? '') . ' ' . ($request->division ?? ''));

        $curso->update([
            'nombre'             => $nombre ?: $curso->nombre,
            'anio'               => $request->anio,
            'division'           => $request->division,
            'turno'              => $request->turno,
            'nivel_id'           => $request->nivel_id,
            'especialidad_id'    => $request->especialidad_id,
            'establecimiento_id' => $request->establecimiento_id,
        ]);

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
