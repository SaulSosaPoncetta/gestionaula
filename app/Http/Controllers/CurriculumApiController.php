<?php

namespace App\Http\Controllers;

use App\Models\EjeCurricular;
use App\Models\NucleoCurricular;
use App\Models\TemaCurricular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurriculumApiController extends Controller
{
    /**
     * GET /curriculum/ejes?materia_id=X
     * Devuelve los ejes curriculares de una materia.
     */
    public function ejes(Request $request): JsonResponse
    {
        $request->validate(['materia_id' => 'required|integer|exists:materias,id']);

        $ejes = EjeCurricular::where('materia_id', $request->materia_id)
            ->orderBy('orden')
            ->get(['id', 'nombre']);

        return response()->json($ejes);
    }

    /**
     * GET /curriculum/nucleos?eje_id=X
     * Devuelve los núcleos de un eje.
     */
    public function nucleos(Request $request): JsonResponse
    {
        $request->validate(['eje_id' => 'required|integer|exists:ejes_curriculares,id']);

        $nucleos = NucleoCurricular::where('eje_curricular_id', $request->eje_id)
            ->orderBy('orden')
            ->get(['id', 'nombre']);

        return response()->json($nucleos);
    }

    /**
     * GET /curriculum/temas?nucleo_id=X
     * Devuelve los temas de un núcleo, cada uno con su lista de subtemas.
     */
    public function temas(Request $request): JsonResponse
    {
        $request->validate(['nucleo_id' => 'required|integer|exists:nucleos_curriculares,id']);

        $temas = TemaCurricular::with(['subtemas' => fn($q) => $q->orderBy('orden')])
            ->where('nucleo_curricular_id', $request->nucleo_id)
            ->orderBy('orden')
            ->get()
            ->map(fn($t) => [
                'id'       => $t->id,
                'nombre'   => $t->nombre,
                'subtemas' => $t->subtemas->pluck('nombre'),
            ]);

        return response()->json($temas);
    }
}
