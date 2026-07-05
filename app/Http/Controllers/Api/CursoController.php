<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $cursos = Curso::where('user_id', $request->user()->id)
            ->orderBy('anio')
            ->orderBy('division')
            ->get();

        return response()->json($cursos);
    }

    public function alumnos(Request $request, Curso $curso)
    {
        abort_if($curso->user_id !== $request->user()->id, 403);

        $alumnos = $curso->alumnos()
            ->where('alumnos.user_id', $request->user()->id)
            ->orderBy('apellido')
            ->get();

        return response()->json($alumnos);
    }
}