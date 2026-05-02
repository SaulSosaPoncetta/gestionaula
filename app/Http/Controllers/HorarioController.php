<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    public function index()
    {
        $dias = self::DIAS;

        $horarios = Horario::with(['curso', 'materia', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->orderByRaw("FIELD(dia, 'lunes','martes','miercoles','jueves','viernes','sabado')")
            ->orderBy('horainicio')
            ->get()
            ->groupBy('dia');

        return view('horarios.index', compact('horarios', 'dias'));
    }

    public function create()
    {
        $dias            = self::DIAS;
        $establecimientos = Establecimiento::orderBy('nombre')->get();
        // Todos los cursos disponibles
        $cursos          = Curso::with(['establecimiento', 'materias'])
                                ->orderBy('anio')->orderBy('division')->get();
        // Todas las materias para pre-cargar en JS
        $materias        = Materia::orderBy('nombre')->get();

        return view('horarios.create', compact('cursos', 'materias', 'dias', 'establecimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
            'curso_id'           => 'required|exists:cursos,id',
            'materia_id'         => 'nullable|exists:materias,id',
            'dia'                => 'required|in:' . implode(',', self::DIAS),
            'horainicio'         => 'required|date_format:H:i',
            'horafin'            => 'required|date_format:H:i|after:horainicio',
        ]);

        Horario::create([
            'user_id'            => auth()->id(),
            'establecimiento_id' => $request->establecimiento_id,
            'curso_id'           => $request->curso_id,
            'materia_id'         => $request->materia_id,
            'dia'                => $request->dia,
            'horainicio'         => $request->horainicio,
            'horafin'            => $request->horafin,
        ]);

        return redirect()->route('horarios.index')
                         ->with('success', 'Horario agregado correctamente.');
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')
                         ->with('success', 'Horario eliminado correctamente.');
    }
}
