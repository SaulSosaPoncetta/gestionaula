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
use Illuminate\Http\Response;

class ExcelController extends Controller
{
    private function uid(): int { return auth()->id(); }

    // ── Panel de selección ────────────────────────────────────────────
    public function index()
    {
        $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', $this->uid())->orderBy('anio')->orderBy('division')->get();
        return view('excel.index', compact('materias', 'cursos'));
    }

    // ── Generador de respuesta XLSX ───────────────────────────────────
    private function xlsx(string $filename, array $sheets): Response
    {
        $xml = $this->buildXlsx($sheets);
        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Construye un XML SpreadsheetML (Excel 2003+) con múltiples hojas.
     * Compatible con Excel, LibreOffice y Google Sheets sin instalar nada.
     *
     * @param array $sheets  [ ['nombre' => 'Hoja1', 'cabeceras' => [...], 'filas' => [[...]] ] ]
     */
    private function buildXlsx(array $sheets): string
    {
        $doc  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $doc .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $doc .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";

        // Estilos
        $doc .= '<Styles>';
        $doc .= '<Style ss:ID="cab"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#1565C0" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center"/></Style>';
        $doc .= '<Style ss:ID="sub"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#42A5F5" ss:Pattern="Solid"/></Style>';
        $doc .= '<Style ss:ID="tit"><Font ss:Bold="1" ss:Size="14"/></Style>';
        $doc .= '<Style ss:ID="bold"><Font ss:Bold="1"/></Style>';
        $doc .= '<Style ss:ID="num"><NumberFormat ss:Format="0.00"/></Style>';
        $doc .= '<Style ss:ID="pct"><NumberFormat ss:Format="0.0%"/></Style>';
        $doc .= '<Style ss:ID="fecha"><NumberFormat ss:Format="DD/MM/YYYY"/></Style>';
        $doc .= '<Style ss:ID="verde"><Font ss:Bold="1" ss:Color="#1B5E20"/></Style>';
        $doc .= '<Style ss:ID="rojo"><Font ss:Bold="1" ss:Color="#B71C1C"/></Style>';
        $doc .= '<Style ss:ID="naranja"><Font ss:Bold="1" ss:Color="#E65100"/></Style>';
        $doc .= '</Styles>' . "\n";

        foreach ($sheets as $sheet) {
            $nombre = htmlspecialchars($sheet['nombre'], ENT_XML1);
            $doc .= "<Worksheet ss:Name=\"{$nombre}\"><Table>\n";

            // Cabeceras
            if (!empty($sheet['cabeceras'])) {
                $doc .= '<Row>';
                foreach ($sheet['cabeceras'] as $cab) {
                    $v = htmlspecialchars((string)$cab, ENT_XML1);
                    $doc .= "<Cell ss:StyleID=\"cab\"><Data ss:Type=\"String\">{$v}</Data></Cell>";
                }
                $doc .= "</Row>\n";
            }

            // Filas de datos
            foreach ($sheet['filas'] as $fila) {
                $doc .= '<Row>';
                foreach ($fila as $celda) {
                    if (is_array($celda)) {
                        // Formato avanzado: ['v' => valor, 't' => tipo, 's' => estilo]
                        $v    = htmlspecialchars((string)($celda['v'] ?? ''), ENT_XML1);
                        $tipo = $celda['t'] ?? 'String';
                        $est  = isset($celda['s']) ? " ss:StyleID=\"{$celda['s']}\"" : '';
                        $doc .= "<Cell{$est}><Data ss:Type=\"{$tipo}\">{$v}</Data></Cell>";
                    } else {
                        $v    = htmlspecialchars((string)$celda, ENT_XML1);
                        $tipo = is_numeric($celda) ? 'Number' : 'String';
                        $doc .= "<Cell><Data ss:Type=\"{$tipo}\">{$v}</Data></Cell>";
                    }
                }
                $doc .= "</Row>\n";
            }
            $doc .= "</Table></Worksheet>\n";
        }

        $doc .= '</Workbook>';
        return $doc;
    }

    // ── 1. Listado de alumnos ─────────────────────────────────────────
    public function alumnos(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->orderBy('apellido')->orderBy('nombre')->get();

        $filas = $alumnos->map(fn($a, $i) => [
            $i + 1,
            $a->apellido . ', ' . $a->nombre,
            $a->dni ?? '',
            $a->fechanacimiento ? Carbon::parse($a->fechanacimiento)->format('d/m/Y') : '',
            $a->telefono ?? '',
            $a->email ?? '',
            $a->contactoemergencia ?? '',
        ])->toArray();

        return $this->xlsx("alumnos_{$curso->anio}_{$curso->division}", [[
            'nombre'    => $curso->nombre_completo,
            'cabeceras' => ['N°', 'Apellido y Nombre', 'DNI', 'Fecha Nac.', 'Teléfono', 'Email', 'Contacto Emergencia'],
            'filas'     => $filas,
        ]]);
    }

    // ── 2. Asistencia ─────────────────────────────────────────────────
    public function asistencia(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $query = Asistencia::with('alumno')->where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->where('materia_id', $materia->id);
        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);

        $registros = $query->orderBy('fecha')->orderBy('alumno_id')->get();
        $porAlumno = $registros->groupBy('alumno_id');
        $fechas    = $registros->pluck('fecha')->unique()->sort()->values();
        $alumnos   = Alumno::where('user_id', $this->uid())->where('curso_id', $curso->id)->orderBy('apellido')->get();

        // Hoja resumen
        $filasSummary = $alumnos->map(function ($a) use ($porAlumno, $fechas) {
            $regs = $porAlumno->get($a->id, collect());
            $p    = $regs->where('estado', 'presente')->count();
            $au   = $regs->where('estado', 'ausente')->count();
            $t    = $regs->where('estado', 'tarde')->count();
            $j    = $regs->where('estado', 'justificado')->count();
            $tot  = $p + $au + $t + $j;
            $pct  = $tot > 0 ? round((($p + $t + $j) / $tot) * 100, 1) : 0;
            return [$a->apellido . ', ' . $a->nombre, $p, $au, $t, $j, $tot, $pct . '%'];
        })->toArray();

        // Hoja detalle por fecha
        $cabDetalle = array_merge(['Alumno'], $fechas->map(fn($f) => Carbon::parse($f)->format('d/m'))->toArray());
        $filasDetalle = $alumnos->map(function ($a) use ($porAlumno, $fechas) {
            $regs = $porAlumno->get($a->id, collect());
            $fila = [$a->apellido . ', ' . $a->nombre];
            foreach ($fechas as $f) {
                $r   = $regs->first(fn($x) => $x->fecha->toDateString() === Carbon::parse($f)->toDateString());
                $fila[] = match($r?->estado) { 'presente' => 'P', 'ausente' => 'A', 'tarde' => 'T', 'justificado' => 'J', default => '' };
            }
            return $fila;
        })->toArray();

        return $this->xlsx("asistencia_{$materia->nombre}_{$curso->anio}", [
            [
                'nombre'    => 'Resumen',
                'cabeceras' => ['Alumno', 'Presentes', 'Ausentes', 'Tardes', 'Justificados', 'Total', '%Asistencia'],
                'filas'     => $filasSummary,
            ],
            [
                'nombre'    => 'Detalle por fecha',
                'cabeceras' => $cabDetalle,
                'filas'     => $filasDetalle,
            ],
        ]);
    }

    // ── 3. Calificaciones ─────────────────────────────────────────────
    public function calificaciones(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $alumnos = Alumno::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->orderBy('apellido')
            ->with(['calificaciones' => fn($q) => $q->where('materia_id', $materia->id)->orderBy('created_at')])
            ->get();

        $filas = [];
        foreach ($alumnos as $a) {
            foreach ($a->calificaciones as $c) {
                $filas[] = [
                    $a->apellido . ', ' . $a->nombre,
                    $c->created_at ? $c->created_at->format('d/m/Y') : '',
                    $c->tipoevaluacion?->nombre ?? '',
                    ['v' => $c->nota ?? '', 't' => 'Number', 's' => 'num'],
                    $c->observacion ?? '',
                ];
            }
        }

        // Resumen por alumno
        $filasResumen = $alumnos->map(fn($a) => [
            $a->apellido . ', ' . $a->nombre,
            $a->calificaciones->count(),
            ['v' => round($a->calificaciones->whereNotNull('nota')->avg('nota') ?? 0, 2), 't' => 'Number', 's' => 'num'],
            ['v' => $a->calificaciones->whereNotNull('nota')->min('nota') ?? '', 't' => 'Number'],
            ['v' => $a->calificaciones->whereNotNull('nota')->max('nota') ?? '', 't' => 'Number'],
        ])->toArray();

        return $this->xlsx("calificaciones_{$materia->nombre}_{$curso->anio}", [
            [
                'nombre'    => 'Detalle',
                'cabeceras' => ['Alumno', 'Fecha', 'Tipo Evaluación', 'Nota', 'Observación'],
                'filas'     => $filas,
            ],
            [
                'nombre'    => 'Resumen',
                'cabeceras' => ['Alumno', 'Cantidad', 'Promedio', 'Mínima', 'Máxima'],
                'filas'     => $filasResumen,
            ],
        ]);
    }

    // ── 4. Cierre de notas ────────────────────────────────────────────
    public function cierre(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $query = CierreNota::with('alumno')
            ->where('user_id', $this->uid())->where('curso_id', $curso->id)->where('materia_id', $materia->id);
        if ($request->filled('tipocierre')) $query->where('tipocierre', $request->tipocierre);
        $cierres = $query->orderByRaw('(SELECT apellido FROM alumnos WHERE alumnos.id = cierrenotas.alumno_id)')->get();

        $filas = $cierres->map(fn($r) => [
            $r->alumno?->apellido . ', ' . $r->alumno?->nombre,
            $r->tipocierre,
            ['v' => $r->notanumerica ?? 0, 't' => 'Number', 's' => 'num'],
            $r->notavalorativa ?? '',
            ['v' => $r->promediocalificaciones ?? '', 't' => 'Number', 's' => 'num'],
            ['v' => $r->promedioactividades ?? '', 't' => 'Number', 's' => 'num'],
            $r->porcentajeasistencia ? $r->porcentajeasistencia . '%' : '',
            $r->fecharegistro?->format('d/m/Y') ?? '',
        ])->toArray();

        return $this->xlsx("cierre_{$materia->nombre}_{$curso->anio}", [[
            'nombre'    => 'Cierre de Notas',
            'cabeceras' => ['Alumno', 'Tipo Cierre', 'Nota Final', 'Valoración', 'Prom. Calific.', 'Prom. Actividades', 'Asistencia', 'Fecha'],
            'filas'     => $filas,
        ]]);
    }

    // ── 5. Declaración jurada ─────────────────────────────────────────
    public function declaracion(Request $request)
    {
        $request->validate(['declaracion_id' => 'required|exists:declaraciones,id']);
        $declaracion = Declaracion::where('user_id', $this->uid())
            ->with(['items.establecimiento', 'items.curso', 'items.materia'])
            ->findOrFail($request->declaracion_id);

        $filas = $declaracion->items->sortBy('dia')->map(fn($i) => [
            ucfirst($i->dia),
            $i->horainicio ? substr($i->horainicio, 0, 5) : '',
            $i->horafin    ? substr($i->horafin, 0, 5)    : '',
            $i->establecimiento?->nombre ?? '',
            $i->curso?->nombre_completo ?? '',
            $i->materia?->nombre ?? '',
        ])->toArray();

        return $this->xlsx("declaracion_{$declaracion->ciclo}", [[
            'nombre'    => 'Declaración Jurada',
            'cabeceras' => ['Día', 'Hora Entrada', 'Hora Salida', 'Establecimiento', 'Curso', 'Materia'],
            'filas'     => $filas,
        ]]);
    }

    // ── 6. Contenidos / temario ───────────────────────────────────────
    public function contenidos(Request $request)
    {
        $request->validate(['materia_id' => 'required|exists:materias,id']);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $temas   = Contenido::with('subtemas')
            ->where('user_id', $this->uid())->where('materia_id', $materia->id)
            ->orderBy('numerounidad')->orderBy('created_at')->get();

        $filas = [];
        foreach ($temas as $t) {
            $filas[] = [['v' => 'Unidad ' . ($t->numerounidad ?? 'S/N'), 's' => 'bold'], $t->tema, '', $t->observacion ?? ''];
            foreach ($t->subtemas->sortBy('orden') as $s) {
                $filas[] = ['', '', $s->subtema, ''];
            }
        }

        return $this->xlsx("contenidos_{$materia->nombre}", [[
            'nombre'    => 'Contenidos',
            'cabeceras' => ['Unidad', 'Tema', 'Subtema', 'Observación'],
            'filas'     => $filas,
        ]]);
    }

    // ── 7. Libro de temas ─────────────────────────────────────────────
    public function librotemas(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);

        $query = LibroTema::where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->where('materia_id', $materia->id)->orderBy('fecha');
        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);
        $temas = $query->get();

        $filas = $temas->map(fn($t) => [
            Carbon::parse($t->fecha)->format('d/m/Y'),
            $t->tipoclase?->nombre ?? '',
            $t->tema ?? '',
            $t->observacion ?? '',
        ])->toArray();

        return $this->xlsx("libro_temas_{$materia->nombre}_{$curso->anio}", [[
            'nombre'    => 'Libro de Temas',
            'cabeceras' => ['Fecha', 'Tipo de Clase', 'Tema', 'Observación'],
            'filas'     => $filas,
        ]]);
    }

    // ── 8. Reporte general del docente ────────────────────────────────
    public function docente(Request $request)
    {
        $docente  = auth()->user();
        $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', $this->uid())->with('alumnos')->orderBy('anio')->get();

        $filasMaterias = $materias->map(fn($m) => [
            $m->nombre, $m->anio ?? '', $m->tipohora ?? '', $m->cargahorariasemanal ?? '', $m->cargahorariaanual ?? '',
        ])->toArray();

        $filasCursos = $cursos->map(fn($c) => [
            $c->nombre_completo, $c->anio, $c->division, $c->turno, $c->alumnos->count(),
        ])->toArray();

        $totalAsistencias    = \App\Models\Asistencia::where('user_id', $this->uid())->count();
        $totalCalificaciones = Calificacion::where('user_id', $this->uid())->count();
        $totalAlumnos        = \App\Models\Alumno::where('user_id', $this->uid())->count();

        $filasResumen = [
            ['Docente',               $docente->name],
            ['Total Materias',        $materias->count()],
            ['Total Cursos',          $cursos->count()],
            ['Total Alumnos',         $totalAlumnos],
            ['Registros Asistencia',  $totalAsistencias],
            ['Registros Calificaciones', $totalCalificaciones],
            ['Fecha de exportación',  Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i')],
        ];

        return $this->xlsx("reporte_docente_{$docente->name}", [
            ['nombre' => 'Resumen', 'cabeceras' => ['Dato', 'Valor'], 'filas' => $filasResumen],
            ['nombre' => 'Materias', 'cabeceras' => ['Nombre', 'Año', 'Tipo Hora', 'Hs/Sem', 'Hs/Anual'], 'filas' => $filasMaterias],
            ['nombre' => 'Cursos', 'cabeceras' => ['Curso', 'Año', 'División', 'Turno', 'Alumnos'], 'filas' => $filasCursos],
        ]);
    }
}
