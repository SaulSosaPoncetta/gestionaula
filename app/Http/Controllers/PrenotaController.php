<?php

namespace App\Http\Controllers;

use App\Models\CierreNota;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Alumno;
use App\Services\PrenotaService;
use Illuminate\Http\Request;

class PrenotaController extends Controller
{
    public function index()
    {
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

        return view('prenotas.index', compact('materias', 'cursos'));
    }

    public function calcular(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
            'tipocierre' => 'required|string|max:100',
        ]);

        $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
        $curso   = Curso::where('user_id', auth()->id())->with('alumnos')->findOrFail($request->curso_id);
        $alumnos = $curso->alumnos->sortBy('apellido');

        $resultados = $alumnos->map(function($alumno) use ($request) {
            $calculo = PrenotaService::calcular(
                $alumno->id,
                $request->materia_id,
                $request->curso_id
            );
            return array_merge($calculo, ['alumno' => $alumno]);
        });

        return view('prenotas.calcular', compact(
            'materia', 'curso', 'resultados', 'alumnos'
        ))->with('tipocierre', $request->tipocierre);
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'materia_id'  => 'required|exists:materias,id',
            'curso_id'    => 'required|exists:cursos,id',
            'tipocierre'  => 'required|string|max:100',
            'registros'   => 'required|array',
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
                    'notanumerica'          => $datos['notanumerica'],
                    'notavalorativa'        => $datos['notavalorativa'] ?? null,
                    'promedioactividades'   => $datos['promedioactividades'] ?? null,
                    'promediocalificaciones'=> $datos['promediocalificaciones'] ?? null,
                    'notaasistencia'        => $datos['notaasistencia'] ?? null,
                    'porcentajeasistencia'  => $datos['porcentajeasistencia'] ?? null,
                    'fecharegistro'         => now()->toDateString(),
                ]
            );
        }

        return redirect()->route('prenotas.historial', [
            'materia_id' => $request->materia_id,
            'curso_id'   => $request->curso_id,
        ])->with('success', 'Prenotas guardadas correctamente.');
    }

    public function historial(Request $request)
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

        $registros = collect();
        $materia   = null;
        $curso     = null;

        if ($request->filled('materia_id') && $request->filled('curso_id')) {
            $materia = Materia::where('user_id', auth()->id())->findOrFail($request->materia_id);
            $curso   = Curso::where('user_id', auth()->id())->findOrFail($request->curso_id);

            $registros = CierreNota::with(['alumno', 'materia', 'curso'])
                ->where('user_id', auth()->id())
                ->where('materia_id', $request->materia_id)
                ->where('curso_id', $request->curso_id)
                ->orderBy('tipocierre')
                ->orderBy('fecharegistro', 'desc')
                ->paginate(30);
        }

        return view('prenotas.historial', compact(
            'materias', 'cursos', 'registros', 'materia', 'curso'
        ));
    }
}