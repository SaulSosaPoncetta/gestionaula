<?php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    public function index()
    {
        $dias = self::DIAS;

        $horarios = Horario::with(['curso', 'materia', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->orderByRaw("FIELD(dia, 'lunes','martes','miercoles','jueves','viernes','sabado','domingo')")
            ->orderBy('horainicio')
            ->get()
            ->groupBy('dia');

        return view('horarios.index', compact('horarios', 'dias'));
    }

    public function create()
    {
        $dias             = self::DIAS;
        $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos           = Curso::where('user_id', auth()->id())
                                ->with(['establecimiento', 'materias'])
                                ->orderBy('anio')->orderBy('division')->get();
        $materias         = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

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
        abort_if($horario->user_id !== auth()->id(), 403);
        $horario->delete();
        return redirect()->route('horarios.index')
                         ->with('success', 'Horario eliminado correctamente.');
    }
}