<?php

namespace App\Http\Controllers;

use App\Models\LibroTema;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\TipoClase;
use App\Models\Contenido;
use App\Models\Actividad;
use App\Models\Horario;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LibroTemaController extends Controller
{
    use DetectaHorarioActivo;

    public function index(Request $request)
    {
        $horarioActivo = $this->detectarHorarioActivo();
        $materiaActiva = $horarioActivo?->materia;
        $cursoActivo   = $horarioActivo?->curso;

        // Preseleccionar según horario activo si no hay filtro explícito
        $materiaIdFiltro = $request->filled('materia_id')
            ? $request->materia_id
            : $horarioActivo?->materia_id;

        $cursoIdFiltro = $request->filled('curso_id')
            ? $request->curso_id
            : $horarioActivo?->curso_id;

        $query = LibroTema::with(['materia', 'curso', 'tipoclase', 'contenido', 'actividad'])
            ->where('user_id', auth()->id())
            ->orderBy('fecha', 'desc')
            ->orderBy('numeroclase', 'desc');

        if ($materiaIdFiltro) $query->where('materia_id', $materiaIdFiltro);
        if ($cursoIdFiltro)   $query->where('curso_id',   $cursoIdFiltro);

        $registros = $query->paginate(20);
        $materias  = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

        // Cursos filtrados por la materia seleccionada (via horarios)
        $cursosParaMateria = $this->cursosDeMateria($materiaIdFiltro);

        // Todos los cursos (para JS cuando cambia materia)
        $cursos = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

        return view('librotemas.index', compact(
            'registros', 'materiaActiva', 'cursoActivo', 'materias', 'cursos',
            'cursosParaMateria', 'materiaIdFiltro', 'cursoIdFiltro'
        ));
    }

    /**
     * Retorna los cursos donde el docente dicta una materia específica,
     * con el curso activo primero.
     */
    private function cursosDeMateria(?int $materiaId): \Illuminate\Support\Collection
    {
        if (!$materiaId) {
            return Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
        }

        $horarioActivo = $this->detectarHorarioActivo();

        return Horario::with('curso')
            ->where('user_id', auth()->id())
            ->where('materia_id', $materiaId)
            ->get()
            ->pluck('curso')
            ->filter()
            ->unique('id')
            ->sortBy(function ($curso) use ($horarioActivo) {
                // El curso activo va primero (orden 0), el resto alfabético
                return $horarioActivo?->curso_id === $curso->id
                    ? '0'
                    : '1_' . $curso->nombre_completo;
            })
            ->values();
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

        // Cursos filtrados por materia (activo primero)
        $cursosParaMateria = $this->cursosDeMateria($materiaId);
        $cursos = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

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
            'materias', 'cursos', 'cursosParaMateria', 'tiposclase', 'contenidos', 'actividades',
            'siguienteClase', 'materiaActiva', 'cursoActivo',
            'materiaId', 'cursoId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id'   => 'required|exists:materias,id',
            'curso_id'     => 'required|exists:cursos,id',
            'fecha'        => 'required|date',
            'tipoclase_id' => 'nullable|exists:tiposclase,id',
            'contenido_id' => 'nullable|exists:contenidos,id',
            'actividad_id' => 'nullable|exists:actividades,id',
            'numeroclase'  => 'required|integer|min:1',
            'numerounidad' => 'nullable|integer|min:1',
            'observacion'  => 'nullable|string',
        ]);

        LibroTema::create([
            'uuid'         => \Illuminate\Support\Str::uuid()->toString(),
            'user_id'      => auth()->id(),
            'materia_id'   => $request->materia_id,
            'curso_id'     => $request->curso_id,
            'tipoclase_id' => $request->tipoclase_id,
            'contenido_id' => $request->contenido_id,
            'actividad_id' => $request->actividad_id,
            'fecha'        => $request->fecha,
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
    $materiaId = $librotema->materia_id;
    $cursoId   = $librotema->curso_id;
    $librotema->delete();
    return redirect()->route('librotemas.index', array_filter([
        'materia_id' => $materiaId,
        'curso_id'   => $cursoId,
    ]))->with('success', 'Registro eliminado correctamente.');
}
}