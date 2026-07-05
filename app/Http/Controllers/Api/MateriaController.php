<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $materias = Materia::where('user_id', $request->user()->id)
            ->orderBy('nombre')
            ->get();

        return response()->json($materias);
    }
}