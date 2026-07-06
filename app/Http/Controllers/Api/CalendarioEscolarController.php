<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarioEscolar;
use Illuminate\Http\Request;

class CalendarioEscolarController extends Controller
{
    public function index(Request $request)
    {
        $eventos = CalendarioEscolar::with('periodo')
            ->where('user_id', $request->user()->id)
            ->orderBy('fecha')
            ->get();

        return response()->json($eventos);
    }
}