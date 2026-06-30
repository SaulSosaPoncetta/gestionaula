<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\CierreNota;
use App\Models\Contenido;
use App\Models\Curso;
use App\Models\Declaracion;
use App\Models\LibroTema;
use App\Models\Materia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    private function uid(): int { return auth()->id(); }

    public function index()
    {
        $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', $this->uid())->orderBy('anio')->orderBy('division')->get();
        return view('pdf.index', compact('materias', 'cursos'));
    }

    public function alumnos(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->orderBy('apellido')->orderBy('nombre')->get();
        return view('pdf.alumnos', compact('curso', 'alumnos'));
    }

    public function asistencia(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $query   = Asistencia::with('alumno')
            ->where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->where('materia_id', $materia->id);
        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);
        $registros = $query->orderBy('fecha')->orderBy('alumno_id')->get();
        $porAlumno = $registros->groupBy('alumno_id');
        $fechas    = $registros->pluck('fecha')->unique()->sort()->values();
        $alumnos   = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->orderBy('apellido')->get();
        return view('pdf.asistencia', compact('curso', 'materia', 'alumnos', 'fechas', 'porAlumno'));
    }

    public function calificaciones(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->orderBy('apellido')
            ->with(['calificaciones' => fn($q) => $q->where('materia_id', $materia->id)->orderBy('created_at')])
            ->get();
        return view('pdf.calificaciones', compact('curso', 'materia', 'alumnos'));
    }

    public function boletin(Request $request)
    {
        $request->validate(['alumno_id' => 'required|exists:alumnos,id']);
        $alumno  = Alumno::where('user_id', $this->uid())->with('curso')->findOrFail($request->alumno_id);
        $cierres = CierreNota::with('materia')
            ->where('user_id', $this->uid())->where('alumno_id', $alumno->id)
            ->orderBy('tipocierre')->orderBy('materia_id')->get()->groupBy('tipocierre');
        return view('pdf.boletin', compact('alumno', 'cierres'));
    }

    public function cierre(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);
        $curso      = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia    = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $query      = CierreNota::with('alumno')
            ->where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->where('materia_id', $materia->id);
        if ($request->filled('tipocierre')) $query->where('tipocierre', $request->tipocierre);
        $cierres    = $query->orderByRaw('(SELECT apellido FROM alumnos WHERE alumnos.id = cierrenotas.alumno_id)')->get();
        $tipocierre = $request->tipocierre ?? 'Todos';
        return view('pdf.cierre', compact('curso', 'materia', 'cierres', 'tipocierre'));
    }

    public function declaracion(Request $request)
    {
        $request->validate(['declaracion_id' => 'required|exists:declaraciones,id']);
        $declaracion = Declaracion::where('user_id', $this->uid())
            ->with(['items.establecimiento', 'items.curso', 'items.materia'])
            ->findOrFail($request->declaracion_id);
        $docente = auth()->user();
        return view('pdf.declaracion', compact('declaracion', 'docente'));
    }

    public function planilla(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->orderBy('apellido')->get();
        $mes     = $request->filled('mes') ? (int)$request->mes : Carbon::now()->month;
        $anio    = Carbon::now()->year;
        $diasMes = Carbon::createFromDate($anio, $mes, 1)->daysInMonth;
        return view('pdf.planilla', compact('curso', 'materia', 'alumnos', 'mes', 'anio', 'diasMes'));
    }

    public function contenidos(Request $request)
    {
        $request->validate(['materia_id' => 'required|exists:materias,id']);
        $materia    = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $contenidos = Contenido::with('subtemas')
            ->where('user_id', $this->uid())->where('materia_id', $materia->id)
            ->orderBy('numerounidad')->orderBy('created_at')->get()->groupBy('numerounidad');
        return view('pdf.contenidos', compact('materia', 'contenidos'));
    }

    public function librotemas(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $query   = LibroTema::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->where('materia_id', $materia->id)->orderBy('fecha');
        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);
        $temas = $query->get();
        return view('pdf.librotemas', compact('curso', 'materia', 'temas'));
    }

    public function docente(Request $request)
    {
        $docente    = auth()->user();
        $materias   = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos     = Curso::where('user_id', $this->uid())->with('alumnos')->orderBy('anio')->orderBy('division')->get();
        $totalAlumnos       = Alumno::where('user_id', $this->uid())->count();
        $totalAsistencias   = Asistencia::where('user_id', $this->uid())->count();
        $totalCalificaciones= Calificacion::where('user_id', $this->uid())->count();
        return view('pdf.docente', compact(
            'docente', 'materias', 'cursos',
            'totalAlumnos', 'totalAsistencias', 'totalCalificaciones'
        ));
    }
}
