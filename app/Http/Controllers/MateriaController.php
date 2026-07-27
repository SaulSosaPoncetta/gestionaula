<?php
namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Ciclo;
use App\Models\AreaFormacion;
use App\Models\Especialidad;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Materia::with(['ciclo', 'areaformacion', 'especialidad'])
                ->where('user_id', auth()->id())->orderBy('nombre');

            if ($request->filled('ciclo_id'))          $query->where('ciclo_id', $request->ciclo_id);
            if ($request->filled('area_formacion_id')) $query->where('area_formacion_id', $request->area_formacion_id);

            $materias = $query->paginate(20);
            $ciclos   = Ciclo::where('user_id', auth()->id())->orderBy('tipo')->get();
            $areas    = AreaFormacion::where('user_id', auth()->id())->orderBy('nombre')->get();

            return view('materias.index', compact('materias', 'ciclos', 'areas'));
        } catch (QueryException $e) {
            Log::error('MateriaController@index - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar las materias.');
        } catch (\Throwable $e) {
            Log::error('MateriaController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $ciclos         = Ciclo::where('user_id', auth()->id())->orderBy('tipo')->get();
            $areas          = AreaFormacion::where('user_id', auth()->id())->orderBy('nombre')->get();
            $especialidades = Especialidad::where('user_id', auth()->id())->orderBy('nombre')->get();
            $tipos          = Materia::TIPOS;
            $tiposhora      = Materia::TIPOSHORA;

            return view('materias.create', compact('ciclos', 'areas', 'especialidades', 'tipos', 'tiposhora'));
        } catch (\Throwable $e) {
            Log::error('MateriaController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'              => 'required|string|max:200',
                'ciclo_id'            => 'nullable|exists:ciclos,id',
                'area_formacion_id'   => 'nullable|exists:areasformacion,id',
                'especialidad_id'     => 'nullable|exists:especialidades,id',
                'anio'                => 'nullable|string|max:20',
                'tipomateria'         => 'nullable|in:aula,taller',
                'tipohora'            => 'nullable|in:' . implode(',', Materia::TIPOSHORA),
                'cargahorariasemanal' => 'nullable|integer|min:1',
                'cargahorariaanual'   => 'nullable|integer|min:1',
                'hsporclase'          => 'nullable|integer|min:1',
            ]);

            $cantidadclasesanuales = null;
            if ($request->filled('cargahorariaanual') && $request->filled('hsporclase') && $request->hsporclase > 0) {
                $cantidadclasesanuales = intval($request->cargahorariaanual / $request->hsporclase);
            }

            Materia::create(array_merge(
                $request->only('nombre', 'ciclo_id', 'area_formacion_id', 'especialidad_id',
                    'anio', 'tipomateria', 'tipohora', 'cargahorariasemanal', 'cargahorariaanual', 'hsporclase'),
                [
                    'user_id'               => auth()->id(),
                    'cantidadclasesanuales' => $cantidadclasesanuales,
                    'porcentajelimite'      => $request->porcentajelimite ?? 75,
                ]
            ));

            return redirect()->route('materias.index')->with('success', 'Materia creada correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('MateriaController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar la materia.')->withInput();
        } catch (\Throwable $e) {
            Log::error('MateriaController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar.')->withInput();
        }
    }

    public function edit(Materia $materia)
    {
        try {
            abort_if($materia->user_id !== auth()->id(), 403);

            $ciclos         = Ciclo::where('user_id', auth()->id())->orderBy('tipo')->get();
            $areas          = AreaFormacion::where('user_id', auth()->id())->orderBy('nombre')->get();
            $especialidades = Especialidad::where('user_id', auth()->id())->orderBy('nombre')->get();
            $tipos          = Materia::TIPOS;
            $tiposhora      = Materia::TIPOSHORA;

            return view('materias.edit', compact('materia', 'ciclos', 'areas', 'especialidades', 'tipos', 'tiposhora'));
        } catch (\Throwable $e) {
            Log::error('MateriaController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la materia para editar.');
        }
    }

    public function update(Request $request, Materia $materia)
    {
        try {
            abort_if($materia->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'              => 'required|string|max:200',
                'ciclo_id'            => 'nullable|exists:ciclos,id',
                'area_formacion_id'   => 'nullable|exists:areasformacion,id',
                'especialidad_id'     => 'nullable|exists:especialidades,id',
                'anio'                => 'nullable|string|max:20',
                'tipomateria'         => 'nullable|in:aula,taller',
                'tipohora'            => 'nullable|in:' . implode(',', Materia::TIPOSHORA),
                'cargahorariasemanal' => 'nullable|integer|min:1',
                'cargahorariaanual'   => 'nullable|integer|min:1',
                'hsporclase'          => 'nullable|integer|min:1',
            ]);

            $cantidadclasesanuales = null;
            if ($request->filled('cargahorariaanual') && $request->filled('hsporclase') && $request->hsporclase > 0) {
                $cantidadclasesanuales = intval($request->cargahorariaanual / $request->hsporclase);
            }

            $materia->update(array_merge(
                $request->only('nombre', 'ciclo_id', 'area_formacion_id', 'especialidad_id',
                    'anio', 'tipomateria', 'tipohora', 'cargahorariasemanal', 'cargahorariaanual',
                    'hsporclase', 'porcentajelimite'),
                ['cantidadclasesanuales' => $cantidadclasesanuales]
            ));

            return redirect()->route('materias.index')->with('success', 'Materia actualizada correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('MateriaController@update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la materia.')->withInput();
        } catch (\Throwable $e) {
            Log::error('MateriaController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al actualizar.')->withInput();
        }
    }

    public function destroy(Materia $materia)
    {
        try {
            abort_if($materia->user_id !== auth()->id(), 403);
            $materia->delete();
            return redirect()->route('materias.index')->with('success', 'Materia eliminada correctamente.');
        } catch (QueryException $e) {
            Log::error('MateriaController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar la materia porque tiene registros asociados.');
        } catch (\Throwable $e) {
            Log::error('MateriaController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al eliminar.');
        }
    }
}
