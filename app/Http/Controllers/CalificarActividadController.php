<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadAlumnoEstado;
use App\Models\ActividadAsignacion;
use App\Models\ActividadNota;
use App\Models\ActividadGrupo;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CalificarActividadController extends Controller
{
    /**
     * Paso 1: Seleccionar materia y curso
     */
    public function index()
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $cursos   = collect();

            if (request()->filled('materia_id')) {
                $cursos = Curso::where('user_id', auth()->id())
                    ->whereHas('materias', fn($q) =>
                        $q->where('materias.id', request('materia_id'))
                    )->orderBy('anio')->orderBy('division')->get();

                if ($cursos->isEmpty()) {
                    $cursos = Curso::where('user_id', auth()->id())
                        ->orderBy('anio')->orderBy('division')->get();
                }
            }

            return view('calificaractividad.index', compact('materias', 'cursos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    /**
     * Paso 2: Lista de alumnos con sus actividades asignadas
     */
    public function ver(Request $request)
    {
        try {
            $request->validate([
                'materia_id' => 'required|exists:materias,id',
                'curso_id'   => 'required|exists:cursos,id',
            ]);

            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);

            // Asignaciones activas para esta materia y curso
            $asignaciones = ActividadAsignacion::with([
                    'actividad.items',
                    'actividad.tipoactividad',
                    'actividad.grupos.alumnos',
                ])
                ->where('user_id', auth()->id())
                ->where('materia_id', $request->materia_id)
                ->where('curso_id', $request->curso_id)
                ->where('estado', 'activa')
                ->get();

            $alumnos = $curso->alumnos->sortBy('apellido');

            // Notas ya registradas
            $notasRegistradas = ActividadNota::where('user_id', auth()->id())
                ->whereIn('asignacion_id', $asignaciones->pluck('id'))
                ->whereIn('alumno_id', $alumnos->pluck('id'))
                ->get()
                ->keyBy(fn($n) => $n->alumno_id . '_' . $n->asignacion_id);

            $hoy = now()->toDateString();

            return view('calificaractividad.ver', compact(
                'materia', 'curso', 'asignaciones', 'alumnos',
                'notasRegistradas', 'hoy'
            ));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@ver: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    /**
     * Guardar nota individual o grupal
     */
    public function calificar(Request $request)
    {
        try {
            $request->validate([
                'asignacion_id'  => 'required|exists:actividadasignaciones,id',
                'alumno_id'      => 'required|exists:alumnos,id',
                'actividad_id'   => 'required|exists:actividades,id',
                'notaindividual' => 'nullable|numeric|min:1|max:10',
                'notagrupal'     => 'nullable|numeric|min:1|max:10',
                'estado'         => 'required|in:pendiente,enproceso,entregado,vencido',
                'observacion'    => 'nullable|string',
            ]);

            $fechaestado = null;
            if (in_array($request->estado, ['entregado', 'vencido'])) {
                $fechaestado = now()->toDateString();
            }

            ActividadNota::updateOrCreate(
                [
                    'asignacion_id' => $request->asignacion_id,
                    'alumno_id'     => $request->alumno_id,
                    'actividad_id'  => $request->actividad_id,
                ],
                [
                    'user_id'        => auth()->id(),
                    'notaindividual' => $request->notaindividual,
                    'notagrupal'     => $request->notagrupal,
                    'estado'         => $request->estado,
                    'fechaestado'    => $fechaestado,
                    'observacion'    => $request->observacion,
                ]
            );

            return redirect()->back()->with('success', 'Calificación guardada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@calificar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    /**
     * Ver historial
     */
    public function historial(Request $request)
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

            $registros = collect();
            $materia   = null;
            $curso     = null;

            if ($request->filled('materia_id') && $request->filled('curso_id')) {
                $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
                $curso   = Curso::where('user_id', auth()->id())->findOrFail($request->curso_id);

                $registros = ActividadNota::with(['alumno', 'actividad', 'asignacion'])
                    ->where('user_id', auth()->id())
                    ->whereHas('asignacion', fn($q) =>
                        $q->where('materia_id', $request->materia_id)
                          ->where('curso_id', $request->curso_id)
                    )
                    ->orderBy('created_at', 'desc')
                    ->paginate(30);
            }

            return view('calificaractividad.historial', compact(
                'materias', 'cursos', 'registros', 'materia', 'curso'
            ));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@historial: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    /**
     * Incompletas
     */
    public function incompletas(Request $request)
    {
        try {
            $request->validate([
                'materia_id' => 'required|exists:materias,id',
                'curso_id'   => 'required|exists:cursos,id',
            ]);

            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $curso   = Curso::where('user_id', auth()->id())->findOrFail($request->curso_id);

            $registros = ActividadAlumnoEstado::with(['alumno', 'actividad'])
                ->where('user_id', auth()->id())
                ->where('estado', 'incompleta')
                ->whereHas('actividad', fn($q) =>
                    $q->where('materia_id', $request->materia_id)
                      ->where('curso_id', $request->curso_id)
                )
                ->get();

            return view('calificaractividad.incompletas', compact('materia', 'curso', 'registros'));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@incompletas: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function pasarAVencida(Request $request, ActividadAlumnoEstado $estado)
    {
        try {
            abort_if($estado->user_id !== auth()->id(), 403);
            abort_if($estado->estado !== 'incompleta', 403);

            $estado->update([
                'estado'      => 'vencida',
                'fechaestado' => now()->toDateString(),
            ]);

            return redirect()->back()->with('success', 'Estado actualizado a entrega vencida.');

        } catch (QueryException $e) {
            Log::error('CalificarActividadController@pasarAVencida BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@pasarAVencida: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function calificadas(Request $request)
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

            $registros = collect();
            $materia   = null;
            $curso     = null;

            if ($request->filled('materia_id') && $request->filled('curso_id')) {
                $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
                $curso   = Curso::where('user_id', auth()->id())->findOrFail($request->curso_id);

                $registros = ActividadNota::with(['alumno', 'actividad'])
                    ->where('user_id', auth()->id())
                    ->whereHas('asignacion', fn($q) =>
                        $q->where('materia_id', $request->materia_id)
                          ->where('curso_id', $request->curso_id)
                    )
                    ->whereNotNull('notaindividual')
                    ->orderBy('created_at', 'desc')
                    ->paginate(30);
            }

            return view('calificaractividad.calificadas', compact(
                'materias', 'cursos', 'registros', 'materia', 'curso'
            ));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@calificadas: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function showCalificada(ActividadAlumnoEstado $estado)
    {
        try {
            abort_if($estado->user_id !== auth()->id(), 403);
            $estado->load(['alumno', 'actividad.materia', 'actividad.curso']);
            return view('calificaractividad.show', compact('estado'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@showCalificada: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function editCalificada(ActividadAlumnoEstado $estado)
    {
        try {
            abort_if($estado->user_id !== auth()->id(), 403);
            $estado->load(['alumno', 'actividad.materia', 'actividad.curso']);
            return view('calificaractividad.edit', compact('estado'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@editCalificada: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function updateCalificada(Request $request, ActividadAlumnoEstado $estado)
    {
        try {
            abort_if($estado->user_id !== auth()->id(), 403);

            $request->validate([
                'estado'      => 'required|in:finalizado,vencida,incompleta',
                'fechaestado' => 'nullable|date',
                'nota'        => 'nullable|numeric|min:0|max:10',
                'observacion' => 'nullable|string',
            ]);

            $estado->update([
                'estado'      => $request->estado,
                'fechaestado' => $request->fechaestado,
                'nota'        => $request->nota,
                'observacion' => $request->observacion,
            ]);

            return redirect()->route('calificaractividad.calificadas', [
                'materia_id' => $estado->actividad->materia_id,
                'curso_id'   => $estado->actividad->curso_id,
            ])->with('success', 'Calificación actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('CalificarActividadController@updateCalificada BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CalificarActividadController@updateCalificada: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}