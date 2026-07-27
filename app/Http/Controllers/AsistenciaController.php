<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\CalendarioEscolar;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    use DetectaHorarioActivo;

    public function index()
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $horarioActivo = $this->detectarHorarioActivo();
            $materiaIdDefault = request('materia_id', $horarioActivo?->materia_id);
            $usandoMateriaDelHorario = !request()->filled('materia_id')
                || (string) request('materia_id') === (string) $horarioActivo?->materia_id;
            $cursoIdDefault = request('curso_id', $usandoMateriaDelHorario ? $horarioActivo?->curso_id : null);
            $cursos = Curso::where('user_id', auth()->id())
                ->orderBy('anio')->orderBy('division')->get();

            return view('asistencia.index', compact(
                'materias', 'cursos', 'materiaIdDefault', 'cursoIdDefault', 'horarioActivo'
            ));
        } catch (QueryException $e) {
            Log::error('AsistenciaController@index - Error BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los datos. Intentá de nuevo.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function accion(Request $request)
    {
        try {
            $request->validate([
                'curso_id'   => 'required|exists:cursos,id',
                'materia_id' => 'required|exists:materias,id',
            ]);

            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);

            return view('asistencia.accion', compact('curso', 'materia'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El curso o materia seleccionado no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@accion: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function registrar(Request $request)
    {
        try {
            $request->validate([
                'curso_id'   => 'required|exists:cursos,id',
                'materia_id' => 'required|exists:materias,id',
                'fecha'      => 'required|date',
            ]);

            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $fecha   = $request->fecha;

            $asistencias = Asistencia::where('curso_id', $request->curso_id)
                ->where('fecha', $fecha)
                ->where('materia_id', $request->materia_id)
                ->where('user_id', auth()->id())
                ->get()
                ->keyBy('alumno_id');

            return view('asistencia.registrar', compact('curso', 'materia', 'fecha', 'asistencias'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Curso o materia no encontrados.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@registrar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function guardar(Request $request)
    {
        try {
            $request->validate([
                'curso_id'    => 'required|exists:cursos,id',
                'materia_id'  => 'required|exists:materias,id',
                'fecha'       => 'required|date',
                'asistencias' => 'required|array',
            ]);

            $esFeriado = CalendarioEscolar::where('user_id', auth()->id())
                ->where('esferiado', true)
                ->where(function ($q) use ($request) {
                    $q->where('fecha', $request->fecha)
                      ->orWhere(function ($q2) use ($request) {
                          $q2->where('fechainicio', '<=', $request->fecha)
                             ->where('fechafin', '>=', $request->fecha);
                      });
                })->first();

            DB::beginTransaction();

            foreach ($request->asistencias as $alumnoId => $datos) {
                $estado      = $datos['estado'] ?? 'presente';
                $horallegada = null;
                $fotoruta    = null;
                $observacion = $datos['observacion'] ?? null;

                if ($esFeriado) {
                    $nota = 'Feriado: ' . $esFeriado->denominacion;
                    $observacion = $observacion ? $nota . ' — ' . $observacion : $nota;
                }

                if ($estado === 'tarde' && !empty($datos['horallegada'])) {
                    $horallegada = $datos['horallegada'];
                }

                if ($estado === 'ausente' && $request->hasFile("fotos.{$alumnoId}")) {
                    $file     = $request->file("fotos.{$alumnoId}");
                    $fotoruta = $file->store("justificaciones/{$request->fecha}", 'public');
                    $estado   = 'justificado';
                }

                Asistencia::updateOrCreate(
                    [
                        'alumno_id'  => $alumnoId,
                        'fecha'      => $request->fecha,
                        'materia_id' => $request->materia_id,
                    ],
                    [
                        'curso_id'          => $request->curso_id,
                        'user_id'           => auth()->id(),
                        'estado'            => $estado,
                        'horallegada'       => $horallegada,
                        'fotojustificacion' => $fotoruta,
                        'observacion'       => $observacion,
                    ]
                );

                \App\Services\AsistenciaService::actualizarPorcentaje($alumnoId, $request->materia_id);
            }

            DB::commit();

            $msg = 'Asistencia guardada correctamente.';
            if ($esFeriado) {
                $msg .= ' (Feriado: ' . $esFeriado->denominacion . ')';
            }

            return redirect()->route('asistencia.registrar', [
                'curso_id'   => $request->curso_id,
                'materia_id' => $request->materia_id,
                'fecha'      => $request->fecha,
            ])->with('success', $msg);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('AsistenciaController@guardar - BD: ' . $e->getMessage(), [
                'curso_id'   => $request->curso_id,
                'materia_id' => $request->materia_id,
                'fecha'      => $request->fecha,
            ]);
            return back()->with('error', 'Error al guardar la asistencia. Intentá de nuevo.')->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AsistenciaController@guardar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar.')->withInput();
        }
    }

    public function listado(Request $request)
    {
        try {
            $request->validate([
                'curso_id'   => 'required|exists:cursos,id',
                'materia_id' => 'required|exists:materias,id',
            ]);

            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);

            $registros = Asistencia::with('alumno')
                ->where('curso_id', $request->curso_id)
                ->where('materia_id', $request->materia_id)
                ->where('user_id', auth()->id())
                ->orderBy('fecha', 'desc')
                ->get();

            $resumenAlumnos = $curso->alumnos->sortBy('apellido')->map(function ($alumno) use ($registros, $materia) {
                $regs       = $registros->where('alumno_id', $alumno->id);
                $total      = $regs->count();
                $presentes  = $regs->whereIn('estado', ['presente', 'tarde', 'justificado'])->count();
                $porcentaje = $total > 0 ? round(($presentes / $total) * 100, 2) : 100;

                $color = \App\Services\AsistenciaService::colorAlerta(
                    $porcentaje,
                    $materia->porcentajelimite ?? 75,
                    $total,
                    $materia->cantidadclasesanuales ?? 0
                );

                return [
                    'alumno'      => $alumno,
                    'presente'    => $presentes,
                    'ausente'     => $regs->where('estado', 'ausente')->count(),
                    'tarde'       => $regs->where('estado', 'tarde')->count(),
                    'justificado' => $regs->where('estado', 'justificado')->count(),
                    'total'       => $total,
                    'porcentaje'  => $porcentaje,
                    'color'       => $color,
                ];
            });

            return view('asistencia.listado', compact('curso', 'materia', 'resumenAlumnos'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Curso o materia no encontrados.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@listado: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el listado.');
        }
    }

    public function historial(Request $request)
    {
        try {
            $materias    = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $cursos      = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            $asistencias = collect();
            $filtros     = [];
            $resumen     = [];
            $alumnos     = collect();

            $horarioActivo    = $this->detectarHorarioActivo();
            $cursoIdDefault   = $request->get('curso_id',   $horarioActivo?->curso_id);
            $materiaIdDefault = $request->get('materia_id', $horarioActivo?->materia_id);

            if (!$request->filled('curso_id') && $cursoIdDefault) {
                $request->merge(['curso_id' => $cursoIdDefault, 'materia_id' => $materiaIdDefault]);
            }

            if ($request->filled('curso_id')) {
                $filtros = $request->only(['curso_id', 'materia_id', 'fechainicio', 'fechafin', 'alumno_id']);
                $query = Asistencia::with(['alumno', 'materia', 'curso'])
                    ->where('user_id', auth()->id())
                    ->where('curso_id', $request->curso_id);

                if ($request->filled('materia_id'))  $query->where('materia_id', $request->materia_id);
                if ($request->filled('alumno_id'))   $query->where('alumno_id',  $request->alumno_id);
                if ($request->filled('fechainicio')) $query->where('fecha', '>=', $request->fechainicio);
                if ($request->filled('fechafin'))    $query->where('fecha', '<=', $request->fechafin);

                $asistencias = $query->orderBy('alumno_id')->orderBy('fecha', 'desc')->get();

                $resumen = [
                    'presente'    => $asistencias->where('estado', 'presente')->count(),
                    'ausente'     => $asistencias->where('estado', 'ausente')->count(),
                    'tarde'       => $asistencias->where('estado', 'tarde')->count(),
                    'justificado' => $asistencias->where('estado', 'justificado')->count(),
                    'total'       => $asistencias->count(),
                ];

                $asistencias = $asistencias->groupBy('alumno_id');
                $alumnos = Alumno::where('user_id', auth()->id())
                    ->where('curso_id', $request->curso_id)
                    ->orderBy('apellido')->get();
            }

            return view('asistencia.historial', compact(
                'materias', 'cursos', 'asistencias', 'filtros',
                'resumen', 'alumnos', 'cursoIdDefault', 'materiaIdDefault'
            ));
        } catch (QueryException $e) {
            Log::error('AsistenciaController@historial - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el historial.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@historial: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function editarRegistro(Asistencia $asistencia)
    {
        try {
            abort_if($asistencia->user_id !== auth()->id(), 403);
            $asistencia->load(['alumno', 'materia', 'curso']);
            return view('asistencia.editar', compact('asistencia'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()->with('error', 'No tenés permiso para editar este registro.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@editarRegistro: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function actualizarRegistro(Request $request, Asistencia $asistencia)
    {
        try {
            abort_if($asistencia->user_id !== auth()->id(), 403);

            $request->validate([
                'estado'      => 'required|in:presente,ausente,tarde,justificado',
                'horallegada' => 'nullable|date_format:H:i',
                'observacion' => 'nullable|string|max:500',
            ]);

            $horallegada = null;
            $fotoruta    = $asistencia->fotojustificacion;

            if ($request->estado === 'tarde' && $request->filled('horallegada')) {
                $horallegada = $request->horallegada;
            }

            if ($request->estado === 'ausente' && $request->hasFile('fotojustificacion')) {
                if ($asistencia->fotojustificacion) {
                    Storage::disk('public')->delete($asistencia->fotojustificacion);
                }
                $fotoruta = $request->file('fotojustificacion')
                    ->store('justificaciones/' . $asistencia->fecha->format('Y-m-d'), 'public');
                $request->merge(['estado' => 'justificado']);
            }

            $asistencia->update([
                'estado'            => $request->estado,
                'horallegada'       => $horallegada,
                'fotojustificacion' => $fotoruta,
                'observacion'       => $request->observacion,
            ]);

            \App\Services\AsistenciaService::actualizarPorcentaje(
                $asistencia->alumno_id,
                $asistencia->materia_id
            );

            return redirect()->route('asistencia.alumno', array_filter([
                'alumno_id'  => $asistencia->alumno_id,
                'materia_id' => $request->filtro_materia_id,
                'curso_id'   => $request->filtro_curso_id,
            ]))->with('success', 'Asistencia actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('AsistenciaController@actualizarRegistro - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el registro.')->withInput();
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@actualizarRegistro: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function alumno(Request $request)
    {
        try {
            $alumnos  = collect();
            $alumno   = null;
            $resumen  = [];
            $detalle  = collect();
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

            if ($request->filled('alumno_id')) {
                $alumno = Alumno::where('user_id', auth()->id())
                    ->with('curso')->find($request->alumno_id);
            }

            if (!$alumno && $request->filled('buscar')) {
                $alumnos = Alumno::with('curso')
                    ->where('user_id', auth()->id())
                    ->where(function ($q) use ($request) {
                        $q->where('apellido', 'like', '%' . $request->buscar . '%')
                          ->orWhere('nombre',   'like', '%' . $request->buscar . '%');
                    })
                    ->orderBy('apellido')->get();

                if ($alumnos->count() === 1) {
                    $alumno  = $alumnos->first();
                    $alumnos = collect();
                }
            }

            if ($alumno) {
                $query = Asistencia::with(['materia', 'curso'])
                    ->where('alumno_id', $alumno->id)
                    ->where('user_id', auth()->id());

                if ($request->filled('materia_id')) {
                    $query->where('materia_id', $request->materia_id);
                }

                $registros = $query->orderBy('fecha', 'desc')->get();

                $resumen = [
                    'presente'    => $registros->where('estado', 'presente')->count(),
                    'ausente'     => $registros->where('estado', 'ausente')->count(),
                    'tarde'       => $registros->where('estado', 'tarde')->count(),
                    'justificado' => $registros->where('estado', 'justificado')->count(),
                    'total'       => $registros->count(),
                ];

                $detalle = $registros;
            }

            return view('asistencia.alumno', compact(
                'alumnos', 'alumno', 'resumen', 'detalle', 'materias'
            ));
        } catch (QueryException $e) {
            Log::error('AsistenciaController@alumno - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al buscar el alumno.');
        } catch (\Throwable $e) {
            Log::error('AsistenciaController@alumno: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}
