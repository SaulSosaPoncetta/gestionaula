<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Curso;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    public function index()
{
    $dias = self::DIAS;

    $horarios = Horario::with(['curso', 'materia'])
        ->where('user_id', auth()->id())
        ->orderByRaw("FIELD(dia, 'lunes','martes','miercoles','jueves','viernes','sabado')")
        ->orderBy('horainicio')
        ->get()
        ->groupBy('dia');

    return view('horarios.index', compact('horarios', 'dias'));
}


    public function create()
{
    $dias = self::DIAS;

    $cursos = Curso::whereHas('docentes', fn($q) => $q->where('users.id', auth()->id()))
                   ->with('materias')->orderBy('nombre')->get();

    return view('horarios.create', compact('cursos', 'dias'));
}

    public function store(Request $request)
    {
        $request->validate([
            'curso_id'    => 'required|exists:cursos,id',
            'materia_id'  => 'nullable|exists:materias,id',
            'dia'         => 'required|in:' . implode(',', self::DIAS),
            'horainicio'  => 'required|date_format:H:i',
            'horafin'     => 'required|date_format:H:i|after:horainicio',
        ]);

        Horario::create([
            'user_id'    => auth()->id(),
            'curso_id'   => $request->curso_id,
            'materia_id' => $request->materia_id,
            'dia'        => $request->dia,
            'horainicio' => $request->horainicio,
            'horafin'    => $request->horafin,
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