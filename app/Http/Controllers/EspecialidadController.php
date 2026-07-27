<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EspecialidadController extends Controller
{
    public function index()
    {
        try {
            $especialidades = Especialidad::withCount('materias')
                ->where('user_id', auth()->id())
                ->orderBy('nombre')
                ->paginate(15);

            return view('especialidades.index', compact('especialidades'));

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
            return view('especialidades.create');

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
                'nombre'      => 'required|string|max:200',
                'descripcion' => 'nullable|string',
            ]);

            Especialidad::create([
                'user_id'     => auth()->id(),
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return redirect()->route('especialidades.index')
                             ->with('success', 'Especialidad creada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(Especialidad $especialidad)
    {
        try {
            abort_if($especialidad->user_id !== auth()->id(), 403);
            return view('especialidades.edit', compact('especialidad'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        try {
            abort_if($especialidad->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'      => 'required|string|max:200',
                'descripcion' => 'nullable|string',
            ]);

            $especialidad->update($request->only('nombre', 'descripcion'));

            return redirect()->route('especialidades.index')
                             ->with('success', 'Especialidad actualizada correctamente.');

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

    public function destroy(Especialidad $especialidad)
    {
        try {
            abort_if($especialidad->user_id !== auth()->id(), 403);
            $especialidad->delete();
            return redirect()->route('especialidades.index')
                             ->with('success', 'Especialidad eliminada correctamente.');

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