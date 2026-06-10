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
    $establecimientos = \App\Models\Establecimiento::where('user_id', auth()->id())
        ->orderBy('nombre')->get();
    $cursos   = \App\Models\Curso::where('user_id', auth()->id())
        ->with('materias')
        ->orderBy('anio')->orderBy('division')->get();
    $materias = \App\Models\Materia::where('user_id', auth()->id())
        ->orderBy('nombre')->get();
    $dias     = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
    $designaciones = \App\Models\Designacion::where('user_id', auth()->id())
        ->orderBy('nombreestablecimiento')
        ->get();

    return view('horarios.create', compact(
        'establecimientos', 'cursos', 'materias', 'dias', 'designaciones'
    ));
}

    public function destroy(Horario $horario)
    {
        abort_if($horario->user_id !== auth()->id(), 403);
        $horario->delete();
        return redirect()->route('horarios.index')
                         ->with('success', 'Horario eliminado correctamente.');
    }
}