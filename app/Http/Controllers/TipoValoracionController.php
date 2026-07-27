<?php

namespace App\Http\Controllers;

use App\Models\TipoValoracion;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TipoValoracionController extends Controller
{
    public function index()
    {
        try {
            $valoraciones = TipoValoracion::where('user_id', auth()->id())
                ->orderBy('notainferior')
                ->paginate(15);

            return view('tipovaloraciones.index', compact('valoraciones'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            return view('tipovaloraciones.create');

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(TipoValoracion $tipovaloracion)
    {
        try {
            abort_if($tipovaloracion->user_id !== auth()->id(), 403);
            return view('tipovaloraciones.edit', compact('tipovaloracion'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, TipoValoracion $tipovaloracion)
    {
        try {
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('Controllers.update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function destroy(TipoValoracion $tipovaloracion)
    {
        try {
            abort_if($tipovaloracion->user_id !== auth()->id(), 403);
            $tipovaloracion->delete();
            return redirect()->route('tipovaloraciones.index')
                             ->with('success', 'Tipo de valoración eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('Controllers.destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }
}