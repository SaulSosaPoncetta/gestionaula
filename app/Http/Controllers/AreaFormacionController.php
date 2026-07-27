<?php

namespace App\Http\Controllers;

use App\Models\AreaFormacion;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AreaFormacionController extends Controller
{
    public function index()
    {
        try {
            $areas = AreaFormacion::withCount('materias')
                ->where('user_id', auth()->id())
                ->orderBy('nombre')
                ->paginate(15);

            return view('areasformacion.index', compact('areas'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('AreaFormacionController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            return view('areasformacion.create');

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('AreaFormacionController@create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'      => 'required|string|max:200',
                'descripcion' => 'nullable|string',
            ]);

            AreaFormacion::create([
                'user_id'     => auth()->id(),
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return redirect()->route('areasformacion.index')
                             ->with('success', 'Área de formación creada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('AreaFormacionController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function edit(AreaFormacion $areasformacion)
    {
        try {
            abort_if($areasformacion->user_id !== auth()->id(), 403);
            return view('areasformacion.edit', ['area' => $areasformacion]);

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('AreaFormacionController@edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, AreaFormacion $areasformacion)
    {
        try {
            abort_if($areasformacion->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'      => 'required|string|max:200',
                'descripcion' => 'nullable|string',
            ]);

            $areasformacion->update($request->only('nombre', 'descripcion'));

            return redirect()->route('areasformacion.index')
                             ->with('success', 'Área de formación actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('AreaFormacionController@update BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('AreaFormacionController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function destroy(AreaFormacion $areasformacion)
    {
        try {
            abort_if($areasformacion->user_id !== auth()->id(), 403);
            $areasformacion->delete();
            return redirect()->route('areasformacion.index')
                             ->with('success', 'Área de formación eliminada correctamente.');

        } catch (QueryException $e) {
            Log::error('AreaFormacionController@destroy BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('AreaFormacionController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}