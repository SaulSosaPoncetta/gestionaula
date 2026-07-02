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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    // ── Página de opciones (descargar / Google Sheets) ────────────────
    public function opciones(Request $request)
    {
        $archivo  = $request->query('archivo');
        $titulo   = $request->query('titulo', 'Exportación');

        // Verificar que el archivo exista y pertenezca al usuario (está en el path)
        if (!$archivo || !Storage::disk('public')->exists('exports/' . $archivo)) {
            return redirect()->route('excel.index')->with('error', 'El archivo ya no está disponible.');
        }

        $url = Storage::disk('public')->url('exports/' . $archivo);

        return view('excel.opciones', compact('archivo', 'titulo', 'url'));
    }

    // ── Forzar descarga ───────────────────────────────────────────────
    public function descargar(Request $request)
    {
        $archivo = $request->query('archivo');

        if (!$archivo || !Storage::disk('public')->exists('exports/' . $archivo)) {
            abort(404, 'Archivo no encontrado.');
        }

        $path = Storage::disk('public')->path('exports/' . $archivo);
        return response()->download($path, $archivo, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * Genera un archivo Excel HTML (.xls) compatible desde Excel 97
     * y redirige a la página de opciones.
     *
     * @param string $titulo   Título del reporte
     * @param array  $sheets   [ ['nombre'=>'Hoja1', 'cabeceras'=>[...], 'filas'=>[[...]] ] ]
     */
    private function generarYRedirigir(string $titulo, array $sheets): \Illuminate\Http\RedirectResponse
    {
        $html = $this->buildXls($sheets);

        // Nombre único de archivo
        $nombre   = Str::slug($titulo) . '_' . Carbon::now('America/Argentina/Buenos_Aires')->format('Ymd_His') . '.xls';
        Storage::disk('public')->put('exports/' . $nombre, $html);

        // Limpiar archivos viejos (más de 1 hora) del mismo usuario
        $this->limpiarExports();

        return redirect()->route('excel.opciones', ['archivo' => $nombre, 'titulo' => $titulo]);
    }

    /**
     * Construye el HTML-table Excel compatible con Excel 97-365,
     * LibreOffice Calc y Google Sheets (importar CSV/Excel).
     */
    private function buildXls(array $sheets): string
    {
        $estilos = '
            body { font-family: Arial, sans-serif; font-size: 11pt; }
            .cab  { background-color: #1565C0; color: white; font-weight: bold; text-align: center; }
            .sub  { background-color: #42A5F5; color: white; font-weight: bold; }
            .bold { font-weight: bold; }
            .num  { text-align: right; mso-number-format:"0.00"; }
            .verde{ color: #1B5E20; font-weight: bold; }
            .rojo { color: #B71C1C; font-weight: bold; }
            .naranja { color: #E65100; font-weight: bold; }
            td, th { border: 1px solid #cccccc; padding: 4px 8px; }
        ';

        // Encabezado del libro Excel HTML (soporta múltiples hojas)
        $doc  = "<!DOCTYPE html>\n";
        $doc .= '<html xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $doc .= '      xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $doc .= '      xmlns="http://www.w3.org/TR/REC-html40">' . "\n";
        $doc .= '<head>' . "\n";
        $doc .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>' . "\n";
        $doc .= '<style>' . $estilos . '</style>' . "\n";
        $doc .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>' . "\n";

        foreach ($sheets as $sheet) {
            $nombre = htmlspecialchars($sheet['nombre'], ENT_XML1);
            $doc .= "<x:ExcelWorksheet><x:Name>{$nombre}</x:Name>";
            $doc .= "<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>";
            $doc .= "</x:ExcelWorksheet>\n";
        }

        $doc .= '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' . "\n";
        $doc .= '</head><body>' . "\n";

        foreach ($sheets as $idx => $sheet) {
            if ($idx > 0) $doc .= '<br style="mso-data-placement:same-cell"/>' . "\n";
            $doc .= '<table border="1" cellpadding="4" cellspacing="0">' . "\n";

            // Título de la hoja (fila superior mergeada)
            $cols = count($sheet['cabeceras']);
            $doc .= "<tr><th class=\"sub\" colspan=\"{$cols}\">";
            $doc .= htmlspecialchars($sheet['nombre'], ENT_QUOTES);
            $doc .= "</th></tr>\n";

            // Cabeceras
            if (!empty($sheet['cabeceras'])) {
                $doc .= '<tr>';
                foreach ($sheet['cabeceras'] as $cab) {
                    $v    = htmlspecialchars((string)$cab, ENT_QUOTES);
                    $doc .= "<th class=\"cab\">{$v}</th>";
                }
                $doc .= "</tr>\n";
            }

            // Filas de datos
            foreach ($sheet['filas'] as $fila) {
                $doc .= '<tr>';
                foreach ($fila as $celda) {
                    if (is_array($celda)) {
                        $v   = htmlspecialchars((string)($celda['v'] ?? ''), ENT_QUOTES);
                        $cls = $celda['c'] ?? '';
                        $tip = $celda['t'] ?? ''; // 'num' para números
                        $att = $tip === 'num' ? ' x:num' : '';
                        $doc .= "<td class=\"{$cls}\"{$att}>{$v}</td>";
                    } else {
                        $v   = htmlspecialchars((string)$celda, ENT_QUOTES);
                        $att = is_numeric($celda) && $celda !== '' ? ' x:num' : '';
                        $doc .= "<td{$att}>{$v}</td>";
                    }
                }
                $doc .= "</tr>\n";
            }

            $doc .= "</table>\n";
        }

        // Pie con metadatos
        $doc .= '<br><p style="font-size:9pt;color:#888">GestiónAula — Exportado: ';
        $doc .= Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i');
        $doc .= ' — Usuario: ' . htmlspecialchars(auth()->user()->name, ENT_QUOTES);
        $doc .= '</p></body></html>';

        return $doc;
    }

    private function limpiarExports(): void
    {
        try {
            $files = Storage::disk('public')->files('exports');
            foreach ($files as $file) {
                if (Storage::disk('public')->lastModified($file) < (time() - 3600)) {
                    Storage::disk('public')->delete($file);
                }
            }
        } catch (\Throwable) {}
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

        return $this->generarYRedirigir("Alumnos {$curso->nombre_completo}", [[
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

        $filasSummary = $alumnos->map(function ($a) use ($porAlumno) {
            $regs = $porAlumno->get($a->id, collect());
            $p    = $regs->where('estado', 'presente')->count();
            $au   = $regs->where('estado', 'ausente')->count();
            $t    = $regs->where('estado', 'tarde')->count();
            $j    = $regs->where('estado', 'justificado')->count();
            $tot  = $p + $au + $t + $j;
            $pct  = $tot > 0 ? round((($p + $t + $j) / $tot) * 100, 1) : 0;
            $cls  = $pct >= 75 ? 'verde' : ($pct >= 60 ? 'naranja' : 'rojo');
            return [$a->apellido . ', ' . $a->nombre, $p, $au, $t, $j, $tot, ['v' => $pct . '%', 'c' => $cls]];
        })->toArray();

        $cabDetalle   = array_merge(['Alumno'], $fechas->map(fn($f) => Carbon::parse($f)->format('d/m'))->toArray());
        $filasDetalle = $alumnos->map(function ($a) use ($porAlumno, $fechas) {
            $regs = $porAlumno->get($a->id, collect());
            $fila = [$a->apellido . ', ' . $a->nombre];
            foreach ($fechas as $f) {
                $r = $regs->first(fn($x) => $x->fecha->toDateString() === Carbon::parse($f)->toDateString());
                $fila[] = match($r?->estado) { 'presente' => 'P', 'ausente' => 'A', 'tarde' => 'T', 'justificado' => 'J', default => '' };
            }
            return $fila;
        })->toArray();

        return $this->generarYRedirigir("Asistencia {$materia->nombre} {$curso->anio}", [
            ['nombre' => 'Resumen',          'cabeceras' => ['Alumno', 'Presentes', 'Ausentes', 'Tardes', 'Justificados', 'Total', '% Asistencia'], 'filas' => $filasSummary],
            ['nombre' => 'Detalle por fecha','cabeceras' => $cabDetalle, 'filas' => $filasDetalle],
        ]);
    }

    // ── 3. Calificaciones ─────────────────────────────────────────────
    public function calificaciones(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $alumnos = Alumno::where('user_id', $this->uid())->where('curso_id', $curso->id)->orderBy('apellido')
            ->with(['calificaciones' => fn($q) => $q->where('materia_id', $materia->id)->orderBy('created_at')])->get();

        $filas = [];
        foreach ($alumnos as $a) {
            foreach ($a->calificaciones as $c) {
                $nota = $c->nota;
                $cls  = $nota >= 7 ? 'verde' : ($nota >= 4 ? 'naranja' : 'rojo');
                $filas[] = [
                    $a->apellido . ', ' . $a->nombre,
                    $c->created_at ? $c->created_at->format('d/m/Y') : '',
                    $c->tipoevaluacion?->nombre ?? '',
                    ['v' => $nota ?? '', 'c' => $cls, 't' => 'num'],
                    $c->observacion ?? '',
                ];
            }
        }

        $filasResumen = $alumnos->map(fn($a) => [
            $a->apellido . ', ' . $a->nombre,
            $a->calificaciones->count(),
            ['v' => round($a->calificaciones->whereNotNull('nota')->avg('nota') ?? 0, 2), 't' => 'num'],
            ['v' => $a->calificaciones->whereNotNull('nota')->min('nota') ?? ''],
            ['v' => $a->calificaciones->whereNotNull('nota')->max('nota') ?? ''],
        ])->toArray();

        return $this->generarYRedirigir("Calificaciones {$materia->nombre} {$curso->anio}", [
            ['nombre' => 'Detalle',  'cabeceras' => ['Alumno', 'Fecha', 'Tipo Evaluación', 'Nota', 'Observación'], 'filas' => $filas],
            ['nombre' => 'Resumen',  'cabeceras' => ['Alumno', 'Cantidad', 'Promedio', 'Mínima', 'Máxima'],        'filas' => $filasResumen],
        ]);
    }

    // ── 4. Cierre de notas ────────────────────────────────────────────
    public function cierre(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
        $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $query   = CierreNota::with('alumno')->where('user_id', $this->uid())
            ->where('curso_id', $curso->id)->where('materia_id', $materia->id);
        if ($request->filled('tipocierre')) $query->where('tipocierre', $request->tipocierre);
        $cierres = $query->orderByRaw('(SELECT apellido FROM alumnos WHERE alumnos.id = cierrenotas.alumno_id)')->get();

        $filas = $cierres->map(fn($r) => [
            $r->alumno?->apellido . ', ' . $r->alumno?->nombre,
            $r->tipocierre,
            ['v' => $r->notanumerica ?? 0, 't' => 'num', 'c' => ($r->notanumerica >= 7 ? 'verde' : ($r->notanumerica >= 4 ? 'naranja' : 'rojo'))],
            $r->notavalorativa ?? '',
            ['v' => $r->promediocalificaciones ?? '', 't' => 'num'],
            ['v' => $r->promedioactividades ?? '',    't' => 'num'],
            $r->porcentajeasistencia ? $r->porcentajeasistencia . '%' : '',
            $r->fecharegistro?->format('d/m/Y') ?? '',
        ])->toArray();

        return $this->generarYRedirigir("Cierre {$materia->nombre} {$curso->anio}", [[
            'nombre'    => 'Cierre de Notas',
            'cabeceras' => ['Alumno', 'Tipo Cierre', 'Nota Final', 'Valoración', 'Prom. Calific.', 'Prom. Actividades', '% Asistencia', 'Fecha'],
            'filas'     => $filas,
        ]]);
    }

    // ── 5. Declaración jurada ─────────────────────────────────────────
    public function declaracion(Request $request)
    {
        $request->validate(['declaracion_id' => 'required|exists:declaraciones,id']);
        $declaracion = Declaracion::where('user_id', $this->uid())
            ->with(['items.establecimiento', 'items.curso', 'items.materia'])->findOrFail($request->declaracion_id);

        $filas = $declaracion->items->sortBy('dia')->map(fn($i) => [
            ucfirst($i->dia),
            $i->horainicio ? substr($i->horainicio, 0, 5) : '',
            $i->horafin    ? substr($i->horafin, 0, 5)    : '',
            $i->establecimiento?->nombre ?? '',
            $i->curso?->nombre_completo ?? '',
            $i->materia?->nombre ?? '',
        ])->toArray();

        return $this->generarYRedirigir("Declaracion Jurada {$declaracion->ciclo}", [[
            'nombre'    => 'Declaración Jurada',
            'cabeceras' => ['Día', 'Hora Entrada', 'Hora Salida', 'Establecimiento', 'Curso', 'Materia'],
            'filas'     => $filas,
        ]]);
    }

    // ── 6. Contenidos ─────────────────────────────────────────────────
    public function contenidos(Request $request)
    {
        $request->validate(['materia_id' => 'required|exists:materias,id']);
        $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
        $temas   = Contenido::with('subtemas')->where('user_id', $this->uid())
            ->where('materia_id', $materia->id)->orderBy('numerounidad')->orderBy('created_at')->get();

        $filas = [];
        foreach ($temas as $t) {
            $filas[] = [['v' => 'Unidad ' . ($t->numerounidad ?? 'S/N'), 'c' => 'bold'], $t->tema, '', $t->observacion ?? ''];
            foreach ($t->subtemas->sortBy('orden') as $s) {
                $filas[] = ['', '', $s->subtema, ''];
            }
        }

        return $this->generarYRedirigir("Contenidos {$materia->nombre}", [[
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
        $query   = LibroTema::where('user_id', $this->uid())->where('curso_id', $curso->id)->where('materia_id', $materia->id)->orderBy('fecha');
        if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
        if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);
        $temas = $query->get();

        $filas = $temas->map(fn($t) => [
            Carbon::parse($t->fecha)->format('d/m/Y'),
            $t->tipoclase?->nombre ?? '',
            $t->tema ?? '',
            $t->observacion ?? '',
        ])->toArray();

        return $this->generarYRedirigir("Libro de Temas {$materia->nombre} {$curso->anio}", [[
            'nombre'    => 'Libro de Temas',
            'cabeceras' => ['Fecha', 'Tipo de Clase', 'Tema', 'Observación'],
            'filas'     => $filas,
        ]]);
    }

    // ── 8. Reporte general ────────────────────────────────────────────
    public function docente(Request $request)
    {
        $docente  = auth()->user();
        $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', $this->uid())->with('alumnos')->orderBy('anio')->get();
        $total    = [
            'alumnos'        => Alumno::where('user_id', $this->uid())->count(),
            'asistencias'    => Asistencia::where('user_id', $this->uid())->count(),
            'calificaciones' => Calificacion::where('user_id', $this->uid())->count(),
        ];

        $filasResumen = [
            ['Docente',             $docente->name],
            ['Email',               $docente->email],
            ['Total Materias',      $materias->count()],
            ['Total Cursos',        $cursos->count()],
            ['Total Alumnos',       $total['alumnos']],
            ['Reg. Asistencia',     $total['asistencias']],
            ['Reg. Calificaciones', $total['calificaciones']],
            ['Fecha exportación',   Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i')],
        ];

        $filasMaterias = $materias->map(fn($m) => [
            $m->nombre, $m->anio ?? '', $m->tipohora ?? '', $m->cargahorariasemanal ?? '', $m->cargahorariaanual ?? '',
        ])->toArray();

        $filasCursos = $cursos->map(fn($c) => [
            $c->nombre_completo, $c->anio, $c->division, $c->turno, $c->alumnos->count(),
        ])->toArray();

        return $this->generarYRedirigir("Reporte Docente {$docente->name}", [
            ['nombre' => 'Resumen',  'cabeceras' => ['Dato', 'Valor'],                                                'filas' => $filasResumen],
            ['nombre' => 'Materias', 'cabeceras' => ['Nombre', 'Año', 'Tipo Hora', 'Hs/Sem', 'Hs/Anual'],            'filas' => $filasMaterias],
            ['nombre' => 'Cursos',   'cabeceras' => ['Curso', 'Año', 'División', 'Turno', 'Alumnos'],                 'filas' => $filasCursos],
        ]);
    }
}
