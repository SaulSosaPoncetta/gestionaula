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
    public function index()
    {
        // Detectar materia activa según horario actual
        $materiaActiva = null;
        $cursoActivo   = null;

        $ahora         = now();
        $diaActual     = strtolower($ahora->locale('es')->isoFormat('dddd'));
        // Normalizar día
        $diaActual = str_replace(
            ['lunes','martes','miércoles','jueves','viernes','sábado','domingo'],
            ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'],
            $diaActual
        );
        $horaActual = $ahora->format('H:i:s');

        $horarioActivo = Horario::with(['materia', 'curso'])
            ->where('user_id', auth()->id())
            ->where('dia', $diaActual)
            ->where('horainicio', '<=', $horaActual)
            ->where('horafin', '>=', $horaActual)
            ->first();

        if ($horarioActivo) {
            $materiaActiva = $horarioActivo->materia;
            $cursoActivo   = $horarioActivo->curso;
        }

        // Listado de libros de temas del docente
        $registros = LibroTema::with(['materia', 'curso', 'tipoclase', 'contenido', 'actividad'])
            ->where('user_id', auth()->id())
            ->orderBy('fecha', 'desc')
            ->orderBy('numeroclase', 'desc')
            ->paginate(20);

        $materias = Materia::orderBy('nombre')->get();
        $cursos   = Curso::orderBy('anio')->orderBy('division')->get();

        return view('librotemas.index', compact(
            'registros', 'materiaActiva', 'cursoActivo', 'materias', 'cursos'
        ));
    }

    public function create(Request $request)
    {
        $materias   = Materia::orderBy('nombre')->get();
        $cursos     = Curso::orderBy('anio')->orderBy('division')->get();
        $tiposclase = TipoClase::orderBy('denominacion')->get();

        $materiaId = $request->materia_id;
        $cursoId   = $request->curso_id;

        // Contenidos filtrados por materia
        $contenidos = $materiaId
            ? Contenido::where('materia_id', $materiaId)->orderBy('tema')->get()
            : collect();

        // Actividades filtradas por materia y curso
        $actividades = ($materiaId && $cursoId)
            ? Actividad::where('materia_id', $materiaId)
                       ->where('curso_id', $cursoId)
                       ->where('estado', 'activa')
                       ->orderBy('titulo')->get()
            : collect();

        // Número de clase siguiente para esta materia y curso
        $siguienteClase = 1;
        if ($materiaId && $cursoId) {
            $ultimo = LibroTema::where('user_id', auth()->id())
                ->where('materia_id', $materiaId)
                ->where('curso_id', $cursoId)
                ->max('numeroclase');
            $siguienteClase = $ultimo ? $ultimo + 1 : 1;
        }

        // Detectar materia/curso activo por horario
        $ahora     = now();
        $diaActual = strtolower($ahora->locale('es')->isoFormat('dddd'));
        $diaActual = str_replace(
            ['lunes','martes','miércoles','jueves','viernes','sábado','domingo'],
            ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'],
            $diaActual
        );
        $horaActual = $ahora->format('H:i:s');

        $horarioActivo = Horario::with(['materia', 'curso'])
            ->where('user_id', auth()->id())
            ->where('dia', $diaActual)
            ->where('horainicio', '<=', $horaActual)
            ->where('horafin', '>=', $horaActual)
            ->first();

        $materiaActiva = $horarioActivo?->materia;
        $cursoActivo   = $horarioActivo?->curso;

        // Si no viene materia_id en la URL pero hay horario activo, usar esa
        if (!$materiaId && $materiaActiva) {
            $materiaId = $materiaActiva->id;
            $cursoId   = $cursoActivo?->id;

            $contenidos = Contenido::where('materia_id', $materiaId)->orderBy('tema')->get();
            $actividades = Actividad::where('materia_id', $materiaId)
                ->where('curso_id', $cursoId)
                ->where('estado', 'activa')
                ->orderBy('titulo')->get();

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
        $librotema->delete();
        return redirect()->route('librotemas.index')
                         ->with('success', 'Registro eliminado correctamente.');
    }
}