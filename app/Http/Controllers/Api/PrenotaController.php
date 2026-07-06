<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CierreNota;
use App\Models\Curso;
use App\Models\Materia;
use App\Services\PrenotaService;
use Illuminate\Http\Request;

class PrenotaController extends Controller
{
    // Calcula (sin guardar) las notas sugeridas para cada alumno del curso
    public function calcular(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $curso = Curso::where('user_id', $request->user()->id)
            ->with('alumnos')
            ->findOrFail($request->curso_id);

        $alumnos = $curso->alumnos->sortBy('apellido');

        $resultados = $alumnos->map(function ($alumno) use ($request) {
            $calculo = PrenotaService::calcular(
                $alumno->id,
                $request->materia_id,
                $request->curso_id
            );

            return array_merge($calculo, [
                'alumno_id' => $alumno->id,
                'nombre'    => $alumno->nombre,
                'apellido'  => $alumno->apellido,
            ]);
        })->values();

        return response()->json($resultados);
    }

    // Guarda el cierre de notas para todos los alumnos de golpe
    public function guardar(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
            'tipocierre' => 'required|string|max:100',
            'registros'  => 'required|array',
        ]);

        foreach ($request->registros as $alumnoId => $datos) {
            CierreNota::updateOrCreate(
                [
                    'user_id'    => $request->user()->id,
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
                    'fecharegistro'          => now()->toDateString(),
                ]
            );
        }

        return response()->json(['message' => 'Notas guardadas correctamente']);
    }

    // Historial de cierres ya guardados
    public function historial(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'curso_id'   => 'required|exists:cursos,id',
        ]);

        $registros = CierreNota::with('alumno')
            ->where('user_id', $request->user()->id)
            ->where('materia_id', $request->materia_id)
            ->where('curso_id', $request->curso_id)
            ->orderBy('tipocierre')
            ->orderBy('fecharegistro', 'desc')
            ->get();

        return response()->json($registros);
    }
}