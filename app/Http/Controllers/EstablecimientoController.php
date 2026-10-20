<?php
namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\Nivel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EstablecimientoController extends Controller
{
    // Fallback por si el modelo no tiene la constante MODALIDADES
    const MODALIDADES = ['común', 'técnica', 'especial', 'adultos', 'artística'];

    private function getModalidades(): array
    {
        return defined('App\Models\Establecimiento::MODALIDADES')
            ? \App\Models\Establecimiento::MODALIDADES
            : self::MODALIDADES;
    }

    public function index()
    {
        try {
            $establecimientos = Establecimiento::with('nivel')
                ->withCount('cursos')
                ->where(function($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->orderBy('nombre')
                ->paginate(15);

            return view('establecimientos.index', compact('establecimientos'));
        } catch (QueryException $e) {
            Log::error('EstablecimientoController@index - BD: ' . $e->getMessage());
            // Si withCount falla, intentar sin él
            try {
                $establecimientos = Establecimiento::with('nivel')
                    ->where(function($q) {
                        $q->where('user_id', auth()->id())->orWhereNull('user_id');
                    })
                    ->orderBy('nombre')
                    ->paginate(15);
                return view('establecimientos.index', compact('establecimientos'));
            } catch (\Throwable $e2) {
                return back()->with('error', 'Error al cargar establecimientos: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@index: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $niveles     = Nivel::where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })->orderBy('nombre')->get();

            $modalidades = $this->getModalidades();

            return view('establecimientos.create', compact('niveles', 'modalidades'));
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'    => 'required|string|max:200',
                'cue'       => 'nullable|string|max:20',
                'modalidad' => 'nullable|string|max:50',
                'nivel_id'  => 'nullable|exists:niveles,id',
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
        } catch (QueryException $e) {
            Log::error('EstablecimientoController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@store: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Establecimiento $establecimiento)
    {
        try {
            abort_if(
                $establecimiento->user_id !== null && $establecimiento->user_id !== auth()->id(),
                403
            );
            $establecimiento->load(['nivel', 'cursos.alumnos']);
            return view('establecimientos.show', compact('establecimiento'));
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@show: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function edit(Establecimiento $establecimiento)
    {
        try {
            abort_if($establecimiento->user_id !== auth()->id(), 403);

            $niveles     = Nivel::where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })->orderBy('nombre')->get();

            $modalidades = $this->getModalidades();

            return view('establecimientos.edit', compact('establecimiento', 'niveles', 'modalidades'));
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Establecimiento $establecimiento)
    {
        try {
            abort_if($establecimiento->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'    => 'required|string|max:200',
                'cue'       => 'nullable|string|max:20',
                'modalidad' => 'nullable|string|max:50',
                'nivel_id'  => 'nullable|exists:niveles,id',
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
            Log::error('EstablecimientoController@update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@update: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
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
            Log::error('EstablecimientoController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar porque tiene datos asociados.');
        } catch (\Throwable $e) {
            Log::error('EstablecimientoController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
}
