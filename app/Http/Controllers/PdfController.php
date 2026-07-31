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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    private function uid(): int { return auth()->id(); }

    // ── Panel de selección ────────────────────────────────────────────
    public function index()
    {
        $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', $this->uid())->orderBy('anio')->orderBy('division')->get();

        return view('pdf.index', compact('materias', 'cursos'));
    }

    // ── 1. Listado de alumnos ─────────────────────────────────────────
    public function alumnos(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id']);

        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->orderBy('apellido')->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('pdf.alumnos', compact('curso', 'alumnos'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("alumnos_{$curso->anio}_{$curso->division}.pdf");
    }

    // ── 2. Registro de asistencia ─────────────────────────────────────
    public function asistencia(Request $request)
    {
        $request->validate([
            'curso_id'    => 'required|exists:cursos,id',
            'materia_id'  => 'required|exists:materias,id',
            'fechainicio' => 'nullable|date',
            'fechafin'    => 'nullable|date',
        ]);

        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $query = Asistencia::with('alumno')
            ->where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->where('materia_id', $materia->id);

        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);

        $registros  = $query->orderBy('fecha')->orderBy('alumno_id')->get();
        $porAlumno  = $registros->groupBy('alumno_id');
        $fechas     = $registros->pluck('fecha')->unique()->sort()->values();

        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->orderBy('apellido')->get();

        $pdf = Pdf::loadView('pdf.asistencia', compact('curso','materia','alumnos','fechas','porAlumno'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("asistencia_{$materia->nombre}_{$curso->anio}_{$curso->division}.pdf");
    }

    // ── 3. Historial de calificaciones ────────────────────────────────
    public function calificaciones(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->orderBy('apellido')
            ->with(['calificaciones' => fn($q) => $q->where('materia_id', $materia->id)->orderBy('fecha')])
            ->get();

        $pdf = Pdf::loadView('pdf.calificaciones', compact('curso','materia','alumnos'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("calificaciones_{$materia->nombre}_{$curso->anio}.pdf");
    }

    // ── 4. Boletín de notas por alumno ────────────────────────────────
    public function boletin(Request $request)
    {
        $request->validate(['alumno_id' => 'required|exists:alumnos,id']);

        $alumno = Alumno::where('user_id', $this->uid())
            ->with(['curso'])
            ->findOrFail($request->alumno_id);

        $cierres = CierreNota::with('materia')
            ->where('user_id', $this->uid())
            ->where('alumno_id', $alumno->id)
            ->orderBy('tipocierre')->orderBy('materia_id')
            ->get()
            ->groupBy('tipocierre');

        $pdf = Pdf::loadView('pdf.boletin', compact('alumno','cierres'))
            ->setPaper('a4', 'portrait');

        $nombre = str_replace(' ', '_', $alumno->nombre_completo);
        return $pdf->download("boletin_{$nombre}.pdf");
    }

    // ── 5. Cierre de notas del cuatrimestre ───────────────────────────
    public function cierre(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
            'tipocierre' => 'nullable|string',
        ]);

        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $query = CierreNota::with('alumno')
            ->where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->where('materia_id', $materia->id);

        if ($request->filled('tipocierre')) $query->where('tipocierre', $request->tipocierre);

        $cierres    = $query->orderByRaw('(SELECT apellido FROM alumnos WHERE alumnos.id = cierrenotas.alumno_id)')->get();
        $tipocierre = $request->tipocierre ?? 'Todos';

        $pdf = Pdf::loadView('pdf.cierre', compact('curso','materia','cierres','tipocierre'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("cierre_{$tipocierre}_{$materia->nombre}.pdf");
    }

    // ── 6. Declaración jurada ─────────────────────────────────────────
    public function declaracion(Request $request)
    {
        $request->validate(['declaracion_id' => 'required|exists:declaraciones,id']);

        $declaracion = Declaracion::where('user_id', $this->uid())
            ->with(['items.establecimiento','items.curso','items.materia'])
            ->findOrFail($request->declaracion_id);

        $docente = auth()->user();

        $pdf = Pdf::loadView('pdf.declaracion', compact('declaracion','docente'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("declaracion_jurada_{$declaracion->ciclo}.pdf");
    }

    // ── 7. Planilla de asistencia en blanco ───────────────────────────
    public function planilla(Request $request)
    {
        $request->validate([
            'curso_id'   => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->orderBy('apellido')->get();

        $mes     = $request->filled('mes') ? (int)$request->mes : Carbon::now()->month;
        $anio    = Carbon::now()->year;
        $diasMes = Carbon::createFromDate($anio, $mes, 1)->daysInMonth;

        $pdf = Pdf::loadView('pdf.planilla', compact('curso','materia','alumnos','mes','anio','diasMes'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("planilla_asistencia_{$curso->anio}{$curso->division}_mes{$mes}.pdf");
    }

    // ── 8. Contenidos / temario ───────────────────────────────────────
    public function contenidos(Request $request)
    {
        $request->validate(['materia_id' => 'required|exists:materias,id']);

        $materia    = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $contenidos = Contenido::with('subtemas')
            ->where('user_id', $this->uid())
            ->where('materia_id', $materia->id)
            ->orderBy('numerounidad')->orderBy('created_at')
            ->get()
            ->groupBy('numerounidad');

        $pdf = Pdf::loadView('pdf.contenidos', compact('materia','contenidos'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("contenidos_{$materia->nombre}.pdf");
    }

    // ── 9. Libro de temas ─────────────────────────────────────────────
    public function librotemas(Request $request)
    {
        $request->validate([
            'curso_id'    => 'required|exists:cursos,id',
            'materia_id'  => 'required|exists:materias,id',
            'fechainicio' => 'nullable|date',
            'fechafin'    => 'nullable|date',
        ]);

        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $query = LibroTema::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)
            ->where('materia_id', $materia->id)
            ->orderBy('fecha');

        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);

        $temas = $query->get();

        $pdf = Pdf::loadView('pdf.librotemas', compact('curso','materia','temas'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("libro_temas_{$materia->nombre}_{$curso->anio}.pdf");
    }

    // ── 10. Reporte general del docente ──────────────────────────────
    public function docente(Request $request)
    {
        $docente   = auth()->user();
        $materias  = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos    = Curso::where('user_id', $this->uid())
            ->with('alumnos')
            ->orderBy('anio')->orderBy('division')->get();

        $totalAlumnos      = Alumno::where('user_id', $this->uid())->count();
        $totalAsistencias  = Asistencia::where('user_id', $this->uid())->count();
        $totalCalificaciones = Calificacion::where('user_id', $this->uid())->count();

        $pdf = Pdf::loadView('pdf.docente', compact(
            'docente','materias','cursos','totalAlumnos','totalAsistencias','totalCalificaciones'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("reporte_docente_{$docente->name}.pdf");
    }
}