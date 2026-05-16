<?php

namespace App\Http\Controllers;

use App\Models\TipoValoracion;
use Illuminate\Http\Request;

class TipoValoracionController extends Controller
{
    public function index()
    {
        $valoraciones = TipoValoracion::where('user_id', auth()->id())
            ->orderBy('notainferior')
            ->paginate(15);

        return view('tipovaloraciones.index', compact('valoraciones'));
    }

    public function create()
    {
        return view('tipovaloraciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'denominacion' => 'required|string|max:200',
            'notainferior' => 'required|numeric|min:0|max:10',
            'notasuperior' => 'required|numeric|min:0|max:10|gte:notainferior',
        ]);

        TipoValoracion::create([
            'user_id'      => auth()->id(),
            'denominacion' => $request->denominacion,
            'notainferior' => $request->notainferior,
            'notasuperior' => $request->notasuperior,
        ]);

        return redirect()->route('tipovaloraciones.index')
                         ->with('success', 'Tipo de valoración creado correctamente.');
    }

    public function edit(TipoValoracion $tipovaloracion)
    {
        abort_if($tipovaloracion->user_id !== auth()->id(), 403);
        return view('tipovaloraciones.edit', compact('tipovaloracion'));
    }

    public function update(Request $request, TipoValoracion $tipovaloracion)
    {
        abort_if($tipovaloracion->user_id !== auth()->id(), 403);

        $request->validate([
            'denominacion' => 'required|string|max:200',
            'notainferior' => 'required|numeric|min:0|max:10',
            'notasuperior' => 'required|numeric|min:0|max:10|gte:notainferior',
        ]);

        $tipovaloracion->update([
            'denominacion' => $request->denominacion,
            'notainferior' => $request->notainferior,
            'notasuperior' => $request->notasuperior,
        ]);

        return redirect()->route('tipovaloraciones.index')
                         ->with('success', 'Tipo de valoración actualizado correctamente.');
    }

    public function destroy(TipoValoracion $tipovaloracion)
    {
        abort_if($tipovaloracion->user_id !== auth()->id(), 403);
        $tipovaloracion->delete();
        return redirect()->route('tipovaloraciones.index')
                         ->with('success', 'Tipo de valoración eliminado correctamente.');
    }
}