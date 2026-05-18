<?php

namespace App\Http\Controllers;

use App\Models\LibroTema;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\TipoClase;
use App\Models\Contenido;
use App\Models\Actividad;
use App\Models\Horario;
use Illuminate\Http\Request;

class LibroTemaController extends Controller
{
    private function detectarHorarioActivo()
{
    $ahora = \Carbon\Carbon::now();

    $mapaDias = [
        1 => 'lunes',
        2 => 'martes',
        3 => 'miercoles',
        4 => 'jueves',
        5 => 'viernes',
        6 => 'sabado',
        7 => 'domingo',
    ];

    $diaActual  = $mapaDias[$ahora->isoWeekday()];
    $horaActual = $ahora->format('H:i:s');

    return Horario::with(['materia', 'curso'])
        ->where('user_id', auth()->id())
        ->where('dia', $diaActual)
        ->where('horainicio', '<=', $horaActual)
        ->where('horafin', '>=', $horaActual)
        ->first();
}

    public function index(Request $request)
    {
        $horarioActivo = $this->detectarHorarioActivo();
        $materiaActiva = $horarioActivo?->materia;
        $cursoActivo   = $horarioActivo?->curso;

        $query = LibroTema::with(['materia', 'curso', 'tipoclase', 'contenido', 'actividad'])
            ->where('user_id', auth()->id())
            ->orderBy('fecha', 'desc')
            ->orderBy('numeroclase', 'desc');

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }
        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        $registros = $query->paginate(20);
        $materias  = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos    = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

        return view('librotemas.index', compact(
            'registros', 'materiaActiva', 'cursoActivo', 'materias', 'cursos'
        ));
    }

    public function create(Request $request)
    {
        $materias   = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos     = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
        $tiposclase = TipoClase::orderBy('denominacion')->get();

        $materiaId = $request->materia_id;
        $cursoId   = $request->curso_id;

        // Si no viene materia_id, detectar por horario activo
        if (!$materiaId) {
            $horarioActivo = $this->detectarHorarioActivo();
            if ($horarioActivo) {
                $materiaId = $horarioActivo->materia_id;
                $cursoId   = $horarioActivo->curso_id;
            }
        }

        $materiaActiva = $materiaId ? Materia::find($materiaId) : null;
        $cursoActivo   = $cursoId   ? Curso::find($cursoId)     : null;

        $contenidos = $materiaId
            ? Contenido::where('user_id', auth()->id())
                       ->where('materia_id', $materiaId)
                       ->orderBy('tema')->get()
            : collect();

        $actividades = ($materiaId && $cursoId)
            ? Actividad::where('user_id', auth()->id())
                       ->where('materia_id', $materiaId)
                       ->where('curso_id', $cursoId)
                       ->where('estado', 'activa')
                       ->orderBy('titulo')->get()
            : collect();

        $siguienteClase = 1;
        if ($materiaId && $cursoId) {
            $ultimo = LibroTema::where('user_id', auth()->id())
                ->where('materia_id', $materiaId)
                ->where('curso_id', $cursoId)
                ->max('numeroclase');
            $siguienteClase = $ultimo ? $ultimo + 1 : 1;
        }

        return view('librotemas.create', compact(
            'materias', 'cursos', 'tiposclase', 'contenidos', 'actividades',
            'siguienteClase', 'materiaActiva', 'cursoActivo',
            'materiaId', 'cursoId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id'   => 'required|exists:materias,id',
            'curso_id'     => 'required|exists:cursos,id',
            'tipoclase_id' => 'nullable|exists:tiposclase,id',
            'contenido_id' => 'nullable|exists:contenidos,id',
            'actividad_id' => 'nullable|exists:actividades,id',
            'numeroclase'  => 'required|integer|min:1',
            'numerounidad' => 'nullable|integer|min:1',
            'observacion'  => 'nullable|string',
        ]);

        LibroTema::create([
            'user_id'      => auth()->id(),
            'materia_id'   => $request->materia_id,
            'curso_id'     => $request->curso_id,
            'tipoclase_id' => $request->tipoclase_id,
            'contenido_id' => $request->contenido_id,
            'actividad_id' => $request->actividad_id,
            'fecha'        => now()->toDateString(),
            'numeroclase'  => $request->numeroclase,
            'numerounidad' => $request->numerounidad,
            'observacion'  => $request->observacion,
        ]);

        return redirect()->route('librotemas.index')
                         ->with('success', 'Tema registrado correctamente.');
    }

    public function destroy(LibroTema $librotema)
    {
        abort_if($librotema->user_id !== auth()->id(), 403);
        $librotema->delete();
        return redirect()->route('librotemas.index')
                         ->with('success', 'Registro eliminado correctamente.');
    }
}