<?php

namespace App\Http\Controllers;

use App\Models\Modalidad;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ModalidadController extends Controller
{
    public function index()
    {
        try {
            $modalidades = Modalidad::where(function ($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->orderBy('nombre')
                ->paginate(15);

            return view('modalidades.index', compact('modalidades'));
        } catch (\Throwable $e) {
            Log::error('ModalidadController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar modalidades: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('modalidades.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:150',
            ]);

            Modalidad::create([
                'nombre'  => trim($request->nombre),
                'slug'    => Str::slug($request->nombre),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('modalidades.index')
                             ->with('success', 'Modalidad creada correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('ModalidadController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('ModalidadController@store: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Modalidad $modalidad)
    {
        try {
            abort_if($modalidad->user_id !== null && $modalidad->user_id !== auth()->id(), 403);
            return view('modalidades.edit', compact('modalidad'));
        } catch (\Throwable $e) {
            Log::error('ModalidadController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Modalidad $modalidad)
    {
        try {
            abort_if($modalidad->user_id !== null && $modalidad->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre' => 'required|string|max:150',
            ]);

            $modalidad->update([
                'nombre' => trim($request->nombre),
                'slug'   => Str::slug($request->nombre),
            ]);

            return redirect()->route('modalidades.index')
                             ->with('success', 'Modalidad actualizada correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('ModalidadController@update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('ModalidadController@update: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Modalidad $modalidad)
    {
        try {
            abort_if($modalidad->user_id !== null && $modalidad->user_id !== auth()->id(), 403);
            $modalidad->delete();
            return redirect()->route('modalidades.index')
                             ->with('success', 'Modalidad eliminada correctamente.');
        } catch (QueryException $e) {
            Log::error('ModalidadController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar porque tiene datos asociados.');
        } catch (\Throwable $e) {
            Log::error('ModalidadController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
}
