<?php
namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PeriodoController extends Controller
{
    public function index()
    {
        try {
            try {
                $periodos = Periodo::withCount('calificaciones')
                    ->orderBy('orden')
                    ->paginate(15);
            } catch (QueryException $e) {
                Log::warning('PeriodoController@index - withCount falló: ' . $e->getMessage());
                $periodos = Periodo::orderBy('orden')->paginate(15);
            }

            return view('periodos.index', compact('periodos'));
        } catch (\Throwable $e) {
            Log::error('PeriodoController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar períodos: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('periodos.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'denominacion' => 'required|string|max:100',
            ]);

            // Calcular orden automáticamente si no se envía
            $orden = $request->orden ?? (Periodo::max('orden') + 1);

            Periodo::create([
                'denominacion' => trim($request->denominacion),
                'orden'        => $orden,
            ]);

            return redirect()->route('periodos.index')
                             ->with('success', 'Período creado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('PeriodoController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('PeriodoController@store: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Periodo $periodo)
    {
        return view('periodos.edit', compact('periodo'));
    }

    public function update(Request $request, Periodo $periodo)
    {
        try {
            $request->validate([
                'denominacion' => 'required|string|max:100',
            ]);

            $periodo->update([
                'denominacion' => trim($request->denominacion),
            ]);

            return redirect()->route('periodos.index')
                             ->with('success', 'Período actualizado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('PeriodoController@update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('PeriodoController@update: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Periodo $periodo)
    {
        try {
            $periodo->delete();
            return redirect()->route('periodos.index')
                             ->with('success', 'Período eliminado correctamente.');
        } catch (QueryException $e) {
            Log::error('PeriodoController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar porque tiene datos asociados.');
        } catch (\Throwable $e) {
            Log::error('PeriodoController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
}
