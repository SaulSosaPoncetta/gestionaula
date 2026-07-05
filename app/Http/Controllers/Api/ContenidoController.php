<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contenido;
use Illuminate\Http\Request;

class ContenidoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $contenidos = Contenido::with('subtemas')
            ->where('user_id', $request->user()->id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('numerounidad')
            ->orderBy('created_at')
            ->get();

        return response()->json($contenidos);
    }
}