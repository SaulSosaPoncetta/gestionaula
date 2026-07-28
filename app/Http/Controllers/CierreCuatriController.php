<?php

namespace App\Http\Controllers;

use App\Models\CierreNota;
use App\Models\Curso;
use App\Models\Materia;
use App\Services\CierreService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CierreCuatriController extends Controller
{
    public function index(Request $request)
    {
        try {
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();

            $cursos = Curso::where('user_id', auth()->id())
                ->orderBy('anio')->orderBy('division')->get();

            return view('cierre_cuatri.index', compact('materias', 'cursos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function calcular(Request $request)
    {
        try {
            $request->validate([
                'materia_id' => 'required|exists:materias,id',
                'curso_id'   => 'required|exists:cursos,id',
                'tipocierre' => 'required|string|max:100',
            ]);

            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
            $alumnos = $curso->alumnos->sortBy('apellido');

            $resultados = $alumnos->map(function ($alumno) use ($request) {
                $calculo = CierreService::calcular(
                    $alumno->id,
                    $request->materia_id,
                    $request->curso_id
                );
                return array_merge($calculo, ['alumno' => $alumno]);
            });

            $tipocierre = $request->tipocierre;

            return view('cierre_cuatri.calcular', compact(
                'materia', 'curso', 'resultados', 'tipocierre'
            ));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.calcular: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function guardar(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'materia_id' => 'required|exists:materias,id',
                'curso_id'   => 'required|exists:cursos,id',
                'tipocierre' => 'required|string|max:100',
                'registros'  => 'required|array',
            ]);

            foreach ($request->registros as $alumnoId => $datos) {
                CierreNota::updateOrCreate(
                    [
                        'user_id'    => auth()->id(),
                        'alumno_id'  => $alumnoId,
                        'materia_id' => $request->materia_id,
                        'curso_id'   => $request->curso_id,
                        'tipocierre' => $request->tipocierre,
                    ],
                    [
                        'notanumerica'           => $datos['notanumerica'],
                        'notavalorativa'         => $datos['notavalorativa'] ?? null,
                        'promedioactividades'    => $datos['promedioactividades'] ?? null,
                        'promediocalificaciones' => $datos['promediocalificaciones'] ?? null,
                        'notaasistencia'         => $datos['notaasistencia'] ?? null,
                        'porcentajeasistencia'   => $datos['porcentajeasistencia'] ?? null,
                        'fecharegistro'          => now('America/Argentina/Buenos_Aires')->toDateString(),
                    ]
                );
            }

            return redirect()->route('cierre_cuatri.historial', [
                'materia_id' => $request->materia_id,
                'curso_id'   => $request->curso_id,
            ])->with('success', 'Cierre de notas guardado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Controllers.guardar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

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

                $registros = CierreNota::with(['alumno'])
                    ->where('user_id', auth()->id())
                    ->where('materia_id', $request->materia_id)
                    ->where('curso_id', $request->curso_id)
                    ->when($request->filled('tipocierre'), fn($q) => $q->where('tipocierre', $request->tipocierre))
                    ->orderBy('tipocierre')
                    ->orderByRaw('(SELECT apellido FROM alumnos WHERE alumnos.id = cierrenotas.alumno_id)')
                    ->get();
            }

            $tiposCierre = ['1er Cuatrimestre', '2do Cuatrimestre', 'Anual', 'Recuperatorio'];

            return view('cierre_cuatri.historial', compact(
                'materias', 'cursos', 'registros', 'materia', 'curso', 'tiposCierre'
            ));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.historial: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}
