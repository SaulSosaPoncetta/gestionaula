<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\TipoEvaluacion;
use App\Models\Alumno;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function index()
    {
        $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
        $periodos = Periodo::orderBy('orden')->get();
        $tipos    = TipoEvaluacion::orderBy('denominacion')->get();

        return view('calificaciones.index', compact('cursos', 'periodos', 'tipos'));
    }

    public function cargar(Request $request)
    {
        $request->validate([
            'curso_id'          => 'required|exists:cursos,id',
            'materia_id'        => 'required|exists:materias,id',
            'periodo_id'        => 'required|exists:periodos,id',
            'tipoevaluacion_id' => 'required|exists:tiposevaluacion,id',
            'fecha'             => 'required|date',
        ]);

        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $periodo = Periodo::findOrFail($request->periodo_id);
        $tipo    = TipoEvaluacion::findOrFail($request->tipoevaluacion_id);
        $fecha   = $request->fecha;

        $calificaciones = Calificacion::where('curso_id', $request->curso_id)
            ->where('materia_id', $request->materia_id)
            ->where('periodo_id', $request->periodo_id)
            ->where('tipoevaluacion_id', $request->tipoevaluacion_id)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('alumno_id');

        return view('calificaciones.cargar', compact(
            'curso', 'materia', 'periodo', 'tipo', 'fecha', 'calificaciones'
        ));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'curso_id'          => 'required|exists:cursos,id',
            'materia_id'        => 'required|exists:materias,id',
            'periodo_id'        => 'required|exists:periodos,id',
            'tipoevaluacion_id' => 'required|exists:tiposevaluacion,id',
            'fecha'             => 'required|date',
            'calificaciones'    => 'required|array',
        ]);

        foreach ($request->calificaciones as $alumnoId => $datos) {
            if (!isset($datos['nota']) || $datos['nota'] === '') continue;

            Calificacion::updateOrCreate(
                [
                    'alumno_id'         => $alumnoId,
                    'curso_id'          => $request->curso_id,
                    'materia_id'        => $request->materia_id,
                    'periodo_id'        => $request->periodo_id,
                    'tipoevaluacion_id' => $request->tipoevaluacion_id,
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
        $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
        $periodos = Periodo::orderBy('orden')->get();
        $tipos    = TipoEvaluacion::orderBy('denominacion')->get();

        $calificaciones = collect();
        $filtros        = [];

        if ($request->filled('curso_id')) {
            $filtros = $request->only(['curso_id', 'materia_id', 'periodo_id', 'tipoevaluacion_id', 'alumno_id']);

            $query = Calificacion::with(['alumno', 'materia', 'periodo', 'tipoevaluacion'])
                ->where('user_id', auth()->id())
                ->where('curso_id', $request->curso_id);

            if ($request->filled('materia_id'))        $query->where('materia_id', $request->materia_id);
            if ($request->filled('periodo_id'))        $query->where('periodo_id', $request->periodo_id);
            if ($request->filled('tipoevaluacion_id')) $query->where('tipoevaluacion_id', $request->tipoevaluacion_id);
            if ($request->filled('alumno_id'))         $query->where('alumno_id', $request->alumno_id);

            $calificaciones = $query->orderBy('created_at', 'desc')->paginate(30);
        }

        $alumnos = collect();
        if ($request->filled('curso_id')) {
            $alumnos = Alumno::where('user_id', auth()->id())
                ->where('curso_id', $request->curso_id)
                ->orderBy('apellido')->get();
        }

        return view('calificaciones.historial', compact(
            'cursos', 'calificaciones', 'filtros', 'periodos', 'tipos', 'alumnos'
        ));
    }
}