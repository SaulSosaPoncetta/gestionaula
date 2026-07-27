<?php
namespace App\Http\Controllers;

use App\Models\Ciclo;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CicloController extends Controller
{
    public function index()
    {
        try {
            $ciclos = Ciclo::withCount('materias')
                ->where('user_id', auth()->id())
                ->orderBy('tipo')
                ->paginate(15);

            // Totales filtrados por usuario
            $totalBasico   = Ciclo::where('user_id', auth()->id())
                ->where('tipo', 'basico')
                ->withCount('materias')->get()->sum('materias_count');

            $totalSuperior = Ciclo::where('user_id', auth()->id())
                ->where('tipo', 'superior')
                ->withCount('materias')->get()->sum('materias_count');

            return view('ciclos.index', compact('ciclos', 'totalBasico', 'totalSuperior'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CicloController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $tipos = Ciclo::TIPOS;
            return view('ciclos.create', compact('tipos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CicloController@create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'      => 'required|string|max:200',
                'tipo'        => 'required|in:' . implode(',', Ciclo::TIPOS),
                'descripcion' => 'nullable|string',
            ]);

            Ciclo::create([
                'user_id'     => auth()->id(),
                'nombre'      => $request->nombre,
                'tipo'        => $request->tipo,
                'descripcion' => $request->descripcion,
            ]);

            return redirect()->route('ciclos.index')
                             ->with('success', 'Ciclo creado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CicloController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function edit(Ciclo $ciclo)
    {
        try {
            abort_if($ciclo->user_id !== auth()->id(), 403);
            $tipos = Ciclo::TIPOS;
            return view('ciclos.edit', compact('ciclo', 'tipos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CicloController@edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, Ciclo $ciclo)
    {
        try {
            abort_if($ciclo->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'      => 'required|string|max:200',
                'tipo'        => 'required|in:' . implode(',', Ciclo::TIPOS),
                'descripcion' => 'nullable|string',
            ]);

            $ciclo->update($request->only('nombre', 'tipo', 'descripcion'));

            return redirect()->route('ciclos.index')
                             ->with('success', 'Ciclo actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('CicloController@update BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CicloController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function destroy(Ciclo $ciclo)
    {
        try {
            abort_if($ciclo->user_id !== auth()->id(), 403);
            $ciclo->delete();
            return redirect()->route('ciclos.index')
                             ->with('success', 'Ciclo eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('CicloController@destroy BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('CicloController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}