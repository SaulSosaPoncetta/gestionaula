<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LibroTema;
use Illuminate\Http\Request;

class LibroTemaController extends Controller
{
    public function index(Request $request)
    {
        $query = LibroTema::with(['materia', 'curso', 'tipoclase', 'contenido', 'actividad'])
            ->where('user_id', $request->user()->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('numeroclase', 'desc');

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }
        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        $registros = $query->get();

        return response()->json($registros);
    }
}