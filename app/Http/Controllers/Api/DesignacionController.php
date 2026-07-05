<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designacion;
use Illuminate\Http\Request;

class DesignacionController extends Controller
{
    public function index(Request $request)
    {
        $designaciones = Designacion::with('horarios')
            ->where('user_id', $request->user()->id)
            ->orderBy('nombreestablecimiento')
            ->orderBy('diasemana')
            ->get();

        return response()->json($designaciones);
    }

    public function show(Request $request, Designacion $designacion)
    {
        abort_if($designacion->user_id !== $request->user()->id, 403);

        $designacion->load('horarios');

        return response()->json($designacion);
    }
}