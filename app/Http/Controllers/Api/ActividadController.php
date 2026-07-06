<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $actividades = Actividad::with(['tipoactividad', 'items'])
            ->where('user_id', $request->user()->id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('numerounidad')
            ->orderBy('numeroactividad')
            ->get();

        return response()->json($actividades);
    }
}