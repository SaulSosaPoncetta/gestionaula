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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExcelController extends Controller
{
    private function uid(): int { return auth()->id(); }

    public function index()
    {
        try {
            $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
            $cursos   = Curso::where('user_id', $this->uid())->orderBy('anio')->orderBy('division')->get();
            return view('excel.index', compact('materias', 'cursos'));
        } catch (\Throwable $e) {
            Log::error('ExcelController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el panel de exportación.');
        }
    }

    public function opciones(Request $request)
    {
        try {
            $archivo = $request->query('archivo');
            $titulo  = $request->query('titulo', 'Exportación');
            if (!$archivo || !Storage::disk('public')->exists('exports/' . $archivo)) {
                return redirect()->route('excel.index')->with('error', 'El archivo ya no está disponible.');
            }
            $url = Storage::disk('public')->url('exports/' . $archivo);
            return view('excel.opciones', compact('archivo', 'titulo', 'url'));
        } catch (\Throwable $e) {
            Log::error('ExcelController@opciones: ' . $e->getMessage());
            return redirect()->route('excel.index')->with('error', 'Error al mostrar las opciones.');
        }
    }

    public function descargar(Request $request)
    {
        try {
            $archivo = $request->query('archivo');
            if (!$archivo || !Storage::disk('public')->exists('exports/' . $archivo)) {
                abort(404, 'Archivo no encontrado.');
            }
            $path = Storage::disk('public')->path('exports/' . $archivo);
            return response()->download($path, $archivo, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
        } catch (\Throwable $e) {
            Log::error('ExcelController@descargar: ' . $e->getMessage());
            return back()->with('error', 'Error al descargar el archivo.');
        }
    }

    private function buildXls(array $sheets): string
    {
        $doc  = "<!DOCTYPE html>\n";
        $doc .= '<html xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $doc .= '      xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $doc .= '      xmlns="http://www.w3.org/TR/REC-html40">' . "\n";
        $doc .= "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>\n";
        $doc .= '<style>body{font-family:Arial,sans-serif;font-size:11pt;} .cab{background-color:#1565C0;color:white;font-weight:bold;text-align:center;} td,th{border:1px solid #cccccc;padding:4px 8px;}</style>';
        $doc .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>';
        foreach ($sheets as $s) {
            $n = htmlspecialchars($s['nombre'], ENT_XML1);
            $doc .= "<x:ExcelWorksheet><x:Name>{$n}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>\n";
        }
        $doc .= '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>' . "\n";
        foreach ($sheets as $sheet) {
            $doc .= '<table border="1" cellpadding="4" cellspacing="0">' . "\n";
            $cols = count($sheet['cabeceras']);
            $doc .= '<tr><th class="cab" colspan="' . $cols . '">' . htmlspecialchars($sheet['nombre'], ENT_QUOTES) . '</th></tr>' . "\n";
            $doc .= '<tr>';
            foreach ($sheet['cabeceras'] as $cab) {
                $doc .= '<th class="cab">' . htmlspecialchars((string)$cab, ENT_QUOTES) . '</th>';
            }
            $doc .= "</tr>\n";
            foreach ($sheet['filas'] as $fila) {
                $doc .= '<tr>';
                foreach ($fila as $celda) {
                    if (is_array($celda)) {
                        $v = htmlspecialchars((string)($celda['v'] ?? ''), ENT_QUOTES);
                        $att = ($celda['t'] ?? '') === 'num' ? ' x:num' : '';
                        $doc .= "<td{$att}>{$v}</td>";
                    } else {
                        $v = htmlspecialchars((string)$celda, ENT_QUOTES);
                        $att = is_numeric($celda) && $celda !== '' ? ' x:num' : '';
                        $doc .= "<td{$att}>{$v}</td>";
                    }
                }
                $doc .= "</tr>\n";
            }
            $doc .= "</table>\n";
        }
        $doc .= '<p style="font-size:9pt;color:#888">GestiónAula — ' . Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') . '</p></body></html>';
        return $doc;
    }

    private function generar(string $titulo, array $sheets)
    {
        $html   = $this->buildXls($sheets);
        $nombre = Str::slug($titulo) . '_' . Carbon::now('America/Argentina/Buenos_Aires')->format('Ymd_His') . '.xls';
        Storage::disk('public')->makeDirectory('exports');
        Storage::disk('public')->put('exports/' . $nombre, $html);
        try {
            foreach (Storage::disk('public')->files('exports') as $f) {
                if (Storage::disk('public')->lastModified($f) < (time() - 3600)) {
                    Storage::disk('public')->delete($f);
                }
            }
        } catch (\Throwable) {}
        return redirect()->route('excel.opciones', ['archivo' => $nombre, 'titulo' => $titulo]);
    }

    public function alumnos(Request $request)
    {
        try {
            $request->validate(['curso_id' => 'required|exists:cursos,id']);
            $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
            $alumnos = Alumno::where('user_id', $this->uid())->where('curso_id', $curso->id)->orderBy('apellido')->get();
            $filas   = $alumnos->map(fn($a, $i) => [$i+1, $a->apellido.', '.$a->nombre, $a->dni ?? '', $a->fechanacimiento ? Carbon::parse($a->fechanacimiento)->format('d/m/Y') : '', $a->telefono ?? '', $a->email ?? '', $a->contactoemergencia ?? ''])->toArray();
            return $this->generar("Alumnos {$curso->nombre_completo}", [['nombre' => $curso->nombre_completo, 'cabeceras' => ['N°','Apellido y Nombre','DNI','Fecha Nac.','Teléfono','Email','Contacto Emergencia'], 'filas' => $filas]]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@alumnos: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte de alumnos.');
        }
    }

    public function asistencia(Request $request)
    {
        try {
            $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
            $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
            $query   = Asistencia::with('alumno')->where('user_id', $this->uid())->where('curso_id', $curso->id)->where('materia_id', $materia->id);
            if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
            if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);
            $registros = $query->orderBy('fecha')->orderBy('alumno_id')->get();
            $porAlumno = $registros->groupBy('alumno_id');
            $fechas    = $registros->pluck('fecha')->unique()->sort()->values();
            $alumnos   = Alumno::where('user_id', $this->uid())->where('curso_id', $curso->id)->orderBy('apellido')->get();
            $filasSummary = $alumnos->map(function ($a) use ($porAlumno) {
                $regs = $porAlumno->get($a->id, collect());
                $p=$regs->where('estado','presente')->count(); $au=$regs->where('estado','ausente')->count(); $t=$regs->where('estado','tarde')->count(); $j=$regs->where('estado','justificado')->count();
                $tot=$p+$au+$t+$j; $pct=$tot>0?round((($p+$t+$j)/$tot)*100,1):0;
                return [$a->apellido.', '.$a->nombre, $p, $au, $t, $j, $tot, $pct.'%'];
            })->toArray();
            $cabDetalle = array_merge(['Alumno'], $fechas->map(fn($f) => Carbon::parse($f)->format('d/m'))->toArray());
            $filasDetalle = $alumnos->map(function ($a) use ($porAlumno, $fechas) {
                $regs = $porAlumno->get($a->id, collect());
                $fila = [$a->apellido.', '.$a->nombre];
                foreach ($fechas as $f) {
                    $r = $regs->first(fn($x) => $x->fecha->toDateString() === Carbon::parse($f)->toDateString());
                    $fila[] = match($r?->estado) {'presente'=>'P','ausente'=>'A','tarde'=>'T','justificado'=>'J',default=>''};
                }
                return $fila;
            })->toArray();
            return $this->generar("Asistencia {$materia->nombre} {$curso->anio}", [
                ['nombre' => 'Resumen',          'cabeceras' => ['Alumno','Presentes','Ausentes','Tardes','Justificados','Total','% Asistencia'], 'filas' => $filasSummary],
                ['nombre' => 'Detalle por fecha','cabeceras' => $cabDetalle, 'filas' => $filasDetalle],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@asistencia: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte de asistencia.');
        }
    }

    public function calificaciones(Request $request)
    {
        try {
            $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
            $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
            $alumnos = Alumno::where('user_id', $this->uid())->where('curso_id', $curso->id)->orderBy('apellido')
                ->with(['calificaciones' => fn($q) => $q->where('materia_id', $materia->id)->orderBy('created_at')])->get();
            $filas = [];
            foreach ($alumnos as $a) {
                foreach ($a->calificaciones as $c) {
                    $filas[] = [$a->apellido.', '.$a->nombre, $c->created_at?->format('d/m/Y') ?? '', $c->tipoevaluacion?->nombre ?? '', ['v'=>$c->nota??'','t'=>'num'], $c->observacion??''];
                }
            }
            $filasResumen = $alumnos->map(fn($a) => [
                $a->apellido.', '.$a->nombre, $a->calificaciones->count(),
                ['v'=>round($a->calificaciones->whereNotNull('nota')->avg('nota')??0,2),'t'=>'num'],
                ['v'=>$a->calificaciones->whereNotNull('nota')->min('nota')??''],
                ['v'=>$a->calificaciones->whereNotNull('nota')->max('nota')??''],
            ])->toArray();
            return $this->generar("Calificaciones {$materia->nombre} {$curso->anio}", [
                ['nombre'=>'Detalle', 'cabeceras'=>['Alumno','Fecha','Tipo Evaluación','Nota','Observación'], 'filas'=>$filas],
                ['nombre'=>'Resumen', 'cabeceras'=>['Alumno','Cantidad','Promedio','Mínima','Máxima'],        'filas'=>$filasResumen],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@calificaciones: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte de calificaciones.');
        }
    }

    public function cierre(Request $request)
    {
        try {
            $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
            $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
            $query   = CierreNota::with('alumno')->where('user_id', $this->uid())->where('curso_id', $curso->id)->where('materia_id', $materia->id);
            if ($request->filled('tipocierre')) $query->where('tipocierre', $request->tipocierre);
            $cierres = $query->orderByRaw('(SELECT apellido FROM alumnos WHERE alumnos.id = cierrenotas.alumno_id)')->get();
            $filas   = $cierres->map(fn($r) => [
                $r->alumno?->apellido.', '.$r->alumno?->nombre, $r->tipocierre,
                ['v'=>$r->notanumerica??0,'t'=>'num'], $r->notavalorativa??'',
                ['v'=>$r->promediocalificaciones??'','t'=>'num'],
                ['v'=>$r->promedioactividades??'','t'=>'num'],
                $r->porcentajeasistencia ? $r->porcentajeasistencia.'%' : '',
                $r->fecharegistro?->format('d/m/Y')??'',
            ])->toArray();
            return $this->generar("Cierre {$materia->nombre} {$curso->anio}", [
                ['nombre'=>'Cierre de Notas','cabeceras'=>['Alumno','Tipo Cierre','Nota Final','Valoración','Prom. Calific.','Prom. Actividades','% Asistencia','Fecha'],'filas'=>$filas],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@cierre: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el cierre de notas.');
        }
    }

    public function declaracion(Request $request)
    {
        try {
            $request->validate(['declaracion_id' => 'required|exists:declaraciones,id']);
            $declaracion = Declaracion::where('user_id', $this->uid())->with(['items.establecimiento','items.curso','items.materia'])->findOrFail($request->declaracion_id);
            $filas = $declaracion->items->sortBy('dia')->map(fn($i) => [ucfirst($i->dia), $i->horainicio?substr($i->horainicio,0,5):'', $i->horafin?substr($i->horafin,0,5):'', $i->establecimiento?->nombre??'', $i->curso?->nombre_completo??'', $i->materia?->nombre??''])->toArray();
            return $this->generar("Declaracion {$declaracion->ciclo}", [
                ['nombre'=>'Declaración Jurada','cabeceras'=>['Día','Hora Entrada','Hora Salida','Establecimiento','Curso','Materia'],'filas'=>$filas],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@declaracion: ' . $e->getMessage());
            return back()->with('error', 'Error al generar la declaración jurada.');
        }
    }

    public function contenidos(Request $request)
    {
        try {
            $request->validate(['materia_id' => 'required|exists:materias,id']);
            $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
            $temas   = Contenido::with('subtemas')->where('user_id', $this->uid())->where('materia_id', $materia->id)->orderBy('numerounidad')->orderBy('created_at')->get();
            $filas = [];
            foreach ($temas as $t) {
                $filas[] = ['Unidad '.($t->numerounidad??'S/N'), $t->tema, '', $t->observacion??''];
                foreach ($t->subtemas->sortBy('orden') as $s) {
                    $filas[] = ['', '', $s->subtema, ''];
                }
            }
            return $this->generar("Contenidos {$materia->nombre}", [
                ['nombre'=>'Contenidos','cabeceras'=>['Unidad','Tema','Subtema','Observación'],'filas'=>$filas],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@contenidos: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el temario.');
        }
    }

    public function librotemas(Request $request)
    {
        try {
            $request->validate(['curso_id' => 'required|exists:cursos,id', 'materia_id' => 'required|exists:materias,id']);
            $curso   = Curso::where('user_id', $this->uid())->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', $this->uid())->findOrFail($request->materia_id);
            $query   = LibroTema::where('user_id', $this->uid())->where('curso_id', $curso->id)->where('materia_id', $materia->id)->orderBy('fecha');
            if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
            if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);
            $filas = $query->get()->map(fn($t) => [Carbon::parse($t->fecha)->format('d/m/Y'), $t->tipoclase?->nombre??'', $t->tema??'', $t->observacion??''])->toArray();
            return $this->generar("Libro Temas {$materia->nombre} {$curso->anio}", [
                ['nombre'=>'Libro de Temas','cabeceras'=>['Fecha','Tipo de Clase','Tema','Observación'],'filas'=>$filas],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@librotemas: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el libro de temas.');
        }
    }

    public function docente(Request $request)
    {
        try {
            $docente  = auth()->user();
            $materias = Materia::where('user_id', $this->uid())->orderBy('nombre')->get();
            $cursos   = Curso::where('user_id', $this->uid())->with('alumnos')->orderBy('anio')->get();
            $filasR   = [
                ['Docente',$docente->name],['Email',$docente->email],
                ['Total Materias',$materias->count()],['Total Cursos',$cursos->count()],
                ['Total Alumnos',Alumno::where('user_id',$this->uid())->count()],
                ['Reg. Asistencia',Asistencia::where('user_id',$this->uid())->count()],
                ['Reg. Calificaciones',Calificacion::where('user_id',$this->uid())->count()],
                ['Fecha exportación',Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i')],
            ];
            $filasM = $materias->map(fn($m) => [$m->nombre,$m->anio??'',$m->tipohora??'',$m->cargahorariasemanal??'',$m->cargahorariaanual??''])->toArray();
            $filasC = $cursos->map(fn($c) => [$c->nombre_completo,$c->anio,$c->division,$c->turno,$c->alumnos->count()])->toArray();
            return $this->generar("Reporte Docente {$docente->name}", [
                ['nombre'=>'Resumen', 'cabeceras'=>['Dato','Valor'],                                       'filas'=>$filasR],
                ['nombre'=>'Materias','cabeceras'=>['Nombre','Año','Tipo Hora','Hs/Sem','Hs/Anual'],       'filas'=>$filasM],
                ['nombre'=>'Cursos',  'cabeceras'=>['Curso','Año','División','Turno','Alumnos'],            'filas'=>$filasC],
            ]);
        } catch (\Throwable $e) {
            Log::error('ExcelController@docente: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte del docente.');
        }
    }
}
