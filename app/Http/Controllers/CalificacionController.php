<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\TipoEvaluacion;
use App\Models\Alumno;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CalificacionController extends Controller
{
    public function index()
    {
        try {
            $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            $periodos = Periodo::orderBy('orden')->get();
            $tipos    = TipoEvaluacion::orderBy('denominacion')->get();

            return view('calificaciones.index', compact('cursos', 'periodos', 'tipos'));
        } catch (QueryException $e) {
            Log::error('CalificacionController@index - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los datos. Intentá de nuevo.');
        } catch (\Throwable $e) {
            Log::error('CalificacionController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function cargar(Request $request)
    {
        try {
            $request->validate([
                'curso_id'          => 'required|exists:cursos,id',
                'materia_id'        => 'required|exists:materias,id',
                'periodo_id'        => 'required|exists:periodos,id',
                'tipoevaluacion_id' => 'required|exists:tiposevaluacion,id',
                'fecha'             => 'required|date',
            ]);

            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $periodo = Periodo::findOrFail($request->periodo_id);
            $tipo    = TipoEvaluacion::findOrFail($request->tipoevaluacion_id);
            $fecha   = $request->fecha;

            $calificaciones = Calificacion::where('curso_id', $request->curso_id)
                ->where('materia_id', $request->materia_id)
                ->where('periodo_id', $request->periodo_id)
                ->where('tipoevaluacion_id', $request->tipoevaluacion_id)
                ->where('user_id', auth()->id())
                ->get()->keyBy('alumno_id');

            return view('calificaciones.cargar', compact(
                'curso', 'materia', 'periodo', 'tipo', 'fecha', 'calificaciones'
            ));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Uno de los datos seleccionados no existe. Revisá los filtros.');
        } catch (\Throwable $e) {
            Log::error('CalificacionController@cargar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function guardar(Request $request)
    {
        try {
            $request->validate([
                'curso_id'          => 'required|exists:cursos,id',
                'materia_id'        => 'required|exists:materias,id',
                'periodo_id'        => 'required|exists:periodos,id',
                'tipoevaluacion_id' => 'required|exists:tiposevaluacion,id',
                'fecha'             => 'required|date',
                'calificaciones'    => 'required|array',
            ]);

            DB::beginTransaction();

            foreach ($request->calificaciones as $alumnoId => $datos) {
                if (!isset($datos['nota']) || $datos['nota'] === '') continue;

                Calificacion::updateOrCreate(
                    [
                        'alumno_id'         => $alumnoId,
                        'curso_id'          => $request->curso_id,
                        'materia_id'        => $request->materia_id,
                        'periodo_id'        => $request->periodo_id,
                        'tipoevaluacion_id' => $request->tipoevaluacion_id,
                    ],
                    [
                        'user_id'     => auth()->id(),
                        'nota'        => $datos['nota'],
                        'observacion' => $datos['observacion'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('calificaciones.index')
                             ->with('success', 'Calificaciones guardadas correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('CalificacionController@guardar - BD: ' . $e->getMessage(), [
                'curso_id'   => $request->curso_id,
                'materia_id' => $request->materia_id,
            ]);
            return back()->with('error', 'Error al guardar las calificaciones. Intentá de nuevo.')->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CalificacionController@guardar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar.')->withInput();
        }
    }

    public function historial(Request $request)
    {
        try {
            $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $periodos = Periodo::orderBy('orden')->get();
            $tipos    = TipoEvaluacion::orderBy('denominacion')->get();

            $calificaciones = collect();
            $filtros        = [];
            $alumnos        = collect();

            if ($request->filled('curso_id') || $request->filled('alumno_id') || $request->filled('materia_id')) {
                $filtros = $request->only(['curso_id', 'materia_id', 'periodo_id', 'tipoevaluacion_id', 'alumno_id']);

                $queryCalif = Calificacion::with([
                        'alumno.curso.nivel', 'alumno.curso.especialidad',
                        'materia', 'periodo', 'tipoevaluacion', 'docente',
                    ])->where('user_id', auth()->id());

                if ($request->filled('curso_id'))          $queryCalif->where('curso_id', $request->curso_id);
                if ($request->filled('materia_id'))        $queryCalif->where('materia_id', $request->materia_id);
                if ($request->filled('periodo_id'))        $queryCalif->where('periodo_id', $request->periodo_id);
                if ($request->filled('tipoevaluacion_id')) $queryCalif->where('tipoevaluacion_id', $request->tipoevaluacion_id);
                if ($request->filled('alumno_id'))         $queryCalif->where('alumno_id', $request->alumno_id);

                $califItems = $queryCalif->get()->map(fn($c) => [
                    'origen'         => 'evaluacion',
                    'alumno'         => $c->alumno,
                    'materia'        => $c->materia?->nombre ?? '—',
                    'periodo'        => $c->periodo?->denominacion ?? '—',
                    'tipoevaluacion' => $c->tipoevaluacion?->denominacion ?? '—',
                    'trabajo'        => '—',
                    'tiponota'       => 'Evaluación',
                    'nota'           => $c->nota,
                    'observacion'    => $c->observacion,
                    'docente'        => $c->docente?->name ?? '—',
                    'fecha'          => $c->created_at,
                ]);

                $queryAct = \App\Models\ActividadAlumnoEstado::with([
                        'alumno.curso.nivel', 'alumno.curso.especialidad',
                        'actividad.materia', 'actividad', 'docente',
                    ])->where('user_id', auth()->id())->whereNotNull('nota');

                if ($request->filled('materia_id')) {
                    $queryAct->whereHas('actividad', fn($q) => $q->where('materia_id', $request->materia_id));
                }
                if ($request->filled('curso_id')) {
                    $queryAct->whereHas('actividad', fn($q) => $q->where('curso_id', $request->curso_id));
                }
                if ($request->filled('alumno_id')) {
                    $queryAct->where('alumno_id', $request->alumno_id);
                }

                $actItems = $queryAct->get()->map(fn($e) => [
                    'origen'         => 'actividad',
                    'alumno'         => $e->alumno,
                    'materia'        => $e->actividad?->materia?->nombre ?? '—',
                    'periodo'        => '—',
                    'tipoevaluacion' => $e->actividad?->tipoactividad?->denominacion ?? '—',
                    'trabajo'        => $e->actividad?->titulo ?? '—',
                    'tiponota'       => 'Actividad',
                    'nota'           => $e->nota,
                    'observacion'    => $e->observacion,
                    'docente'        => $e->docente?->name ?? '—',
                    'fecha'          => $e->created_at,
                ]);

                $calificaciones = $califItems->merge($actItems)
                    ->sortBy([
                        fn($a, $b) => strcmp($a['alumno']?->apellido ?? '', $b['alumno']?->apellido ?? ''),
                        fn($a, $b) => strcmp($a['alumno']?->nombre   ?? '', $b['alumno']?->nombre   ?? ''),
                    ])
                    ->groupBy(fn($item) => $item['alumno']?->id);

                if ($request->filled('curso_id')) {
                    $alumnos = Alumno::where('user_id', auth()->id())
                        ->where('curso_id', $request->curso_id)
                        ->orderBy('apellido')->get();
                }
            }

            return view('calificaciones.historial', compact(
                'cursos', 'materias', 'calificaciones', 'filtros',
                'periodos', 'tipos', 'alumnos'
            ));
        } catch (QueryException $e) {
            Log::error('CalificacionController@historial - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el historial de calificaciones.');
        } catch (\Throwable $e) {
            Log::error('CalificacionController@historial: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}
