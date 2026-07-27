<?php
namespace App\Http\Controllers;

use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NivelController extends Controller
{
    public function index()
    {
        try {
            $niveles = Nivel::withCount('establecimientos')
                ->where('user_id', auth()->id())
                ->orderBy('tipo')
                ->paginate(15);

            return view('niveles.index', compact('niveles'));

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
            $tipos = Nivel::TIPOS;
            return view('niveles.create', compact('tipos'));

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
                'nombre' => 'required|string|max:100',
                'tipo'   => 'required|in:' . implode(',', Nivel::TIPOS),
            ]);

            Nivel::create([
                'user_id' => auth()->id(),
                'nombre'  => $request->nombre,
                'tipo'    => $request->tipo,
            ]);

            return redirect()->route('niveles.index')
                             ->with('success', 'Nivel creado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(Nivel $nivel)
    {
        try {
            abort_if($nivel->user_id !== auth()->id(), 403);
            $tipos = Nivel::TIPOS;
            return view('niveles.edit', compact('nivel', 'tipos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, Nivel $nivel)
    {
        try {
            abort_if($nivel->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre' => 'required|string|max:100',
                'tipo'   => 'required|in:' . implode(',', Nivel::TIPOS),
            ]);

            $nivel->update($request->only('nombre', 'tipo'));

            return redirect()->route('niveles.index')
                             ->with('success', 'Nivel actualizado correctamente.');

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

    public function destroy(Nivel $nivel)
    {
        try {
            abort_if($nivel->user_id !== auth()->id(), 403);
            $nivel->delete();
            return redirect()->route('niveles.index')
                             ->with('success', 'Nivel eliminado correctamente.');

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