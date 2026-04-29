<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Materia;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    const PERIODOS = ['1er trimestre', '2do trimestre', '3er trimestre'];
    const TIPOS = ['Trabajo práctico', 'Examen', 'Oral', 'Proyecto', 'Concepto'];

    public function index()
{
    $cursos = Curso::whereHas('docentes', fn($q) => $q->where('users.id', auth()->id()))
                   ->with('materias')
                   ->orderBy('nombre')
                   ->get();

    $periodos = self::PERIODOS;
    $tipos = self::TIPOS;

    return view('calificaciones.index', compact('cursos', 'periodos', 'tipos'));
}

    public function cargar(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'nullable|exists:materias,id',
            'periodo'    => 'required|string',
            'tipo'       => 'required|string',
        ]);

        $curso = Curso::with('alumnos')->findOrFail($request->curso_id);
        $materia = $request->materia_id ? Materia::find($request->materia_id) : null;

        $calificaciones = Calificacion::where('curso_id', $request->curso_id)
            ->where('periodo', $request->periodo)
            ->where('tipo', $request->tipo)
            ->when($request->materia_id, fn($q) => $q->where('materia_id', $request->materia_id))
            ->get()
            ->keyBy('alumno_id');

        $periodos = self::PERIODOS;
        $tipos = self::TIPOS;

        return view('calificaciones.cargar', compact(
            'curso', 'materia', 'calificaciones',
            'periodos', 'tipos'
        ) + $request->only('periodo', 'tipo'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'curso_id'       => 'required|exists:cursos,id',
            'materia_id'     => 'nullable|exists:materias,id',
            'periodo'        => 'required|string',
            'tipo'           => 'required|string',
            'calificaciones' => 'required|array',
        ]);

        foreach ($request->calificaciones as $alumnoId => $datos) {
            if (!isset($datos['nota']) || $datos['nota'] === '') continue;

            Calificacion::updateOrCreate(
                [
                    'alumno_id'  => $alumnoId,
                    'curso_id'   => $request->curso_id,
                    'materia_id' => $request->materia_id,
                    'periodo'    => $request->periodo,
                    'tipo'       => $request->tipo,
                ],
                [
                    'user_id'     => auth()->id(),
                    'nota'        => $datos['nota'],
                    'observacion' => $datos['observacion'] ?? null,
                ]
            );
        }

        return redirect()->route('calificaciones.index')
                         ->with('success', 'Calificaciones guardadas correctamente.');
    }

   public function historial(Request $request)
{
    $cursos = Curso::whereHas('docentes', fn($q) => $q->where('users.id', auth()->id()))
                   ->orderBy('nombre')
                   ->get();

    $calificaciones = collect();
    $filtros = [];
    $periodos = self::PERIODOS;

    if ($request->filled('curso_id')) {
        $filtros = $request->only(['curso_id', 'materia_id', 'periodo', 'alumno_id']);

        $query = Calificacion::with(['alumno', 'materia', 'docente'])
            ->where('curso_id', $request->curso_id);

        if ($request->filled('materia_id')) $query->where('materia_id', $request->materia_id);
        if ($request->filled('periodo'))    $query->where('periodo', $request->periodo);
        if ($request->filled('alumno_id'))  $query->where('alumno_id', $request->alumno_id);

        $calificaciones = $query->orderBy('periodo')->orderBy('tipo')->paginate(30);
    }

    $alumnos = collect();
    if ($request->filled('curso_id')) {
        $alumnos = \App\Models\Alumno::where('curso_id', $request->curso_id)
            ->orderBy('apellido')->get();
    }

    return view('calificaciones.historial', compact('cursos', 'calificaciones', 'filtros', 'periodos', 'alumnos'));
}
}