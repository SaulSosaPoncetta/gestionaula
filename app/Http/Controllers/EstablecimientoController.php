<?php
namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EstablecimientoController extends Controller
{
    public function index()
    {
        try {
            $establecimientos = Establecimiento::with('nivel')
                ->where('user_id', auth()->id())
                ->orderBy('nombre')
                ->paginate(15);

            return view('establecimientos.index', compact('establecimientos'));

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
            $niveles     = Nivel::where('user_id', auth()->id())->orderBy('tipo')->get();
            $modalidades = Establecimiento::MODALIDADES;
            return view('establecimientos.create', compact('niveles', 'modalidades'));

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
                'nombre'    => 'required|string|max:200',
                'cue'       => 'nullable|string|max:20',
                'modalidad' => 'required|in:' . implode(',', Establecimiento::MODALIDADES),
                'nivel_id'  => 'required|exists:niveles,id',
                'direccion' => 'nullable|string|max:200',
                'localidad' => 'nullable|string|max:100',
                'provincia' => 'nullable|string|max:100',
                'telefono'  => 'nullable|string|max:30',
                'email'     => 'nullable|email|max:100',
            ]);

            Establecimiento::create(array_merge(
                $request->only('nombre', 'cue', 'modalidad', 'nivel_id',
                    'direccion', 'localidad', 'provincia', 'telefono', 'email'),
                ['user_id' => auth()->id()]
            ));

            return redirect()->route('establecimientos.index')
                             ->with('success', 'Establecimiento creado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function show(Establecimiento $establecimiento)
    {
        try {
            abort_if($establecimiento->user_id !== auth()->id(), 403);
            $establecimiento->load(['nivel', 'cursos.alumnos']);
            return view('establecimientos.show', compact('establecimiento'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function edit(Establecimiento $establecimiento)
    {
        try {
            abort_if($establecimiento->user_id !== auth()->id(), 403);
            $niveles     = Nivel::where('user_id', auth()->id())->orderBy('tipo')->get();
            $modalidades = Establecimiento::MODALIDADES;
            return view('establecimientos.edit', compact('establecimiento', 'niveles', 'modalidades'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, Establecimiento $establecimiento)
    {
        try {
            abort_if($establecimiento->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'    => 'required|string|max:200',
                'cue'       => 'nullable|string|max:20',
                'modalidad' => 'required|in:' . implode(',', Establecimiento::MODALIDADES),
                'nivel_id'  => 'required|exists:niveles,id',
                'direccion' => 'nullable|string|max:200',
                'localidad' => 'nullable|string|max:100',
                'provincia' => 'nullable|string|max:100',
                'telefono'  => 'nullable|string|max:30',
                'email'     => 'nullable|email|max:100',
            ]);

            $establecimiento->update($request->only(
                'nombre', 'cue', 'modalidad', 'nivel_id',
                'direccion', 'localidad', 'provincia', 'telefono', 'email'
            ));

            return redirect()->route('establecimientos.index')
                             ->with('success', 'Establecimiento actualizado correctamente.');

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

    public function destroy(Establecimiento $establecimiento)
    {
        try {
            abort_if($establecimiento->user_id !== auth()->id(), 403);
            $establecimiento->delete();
            return redirect()->route('establecimientos.index')
                             ->with('success', 'Establecimiento eliminado correctamente.');

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