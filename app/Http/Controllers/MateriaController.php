<?php
namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Ciclo;
use App\Models\AreaFormacion;
use App\Models\Especialidad;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Materia::with(['ciclo', 'areaformacion', 'especialidad', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->orderBy('nombre');

        if ($request->filled('ciclo_id'))           $query->where('ciclo_id', $request->ciclo_id);
        if ($request->filled('area_formacion_id'))  $query->where('area_formacion_id', $request->area_formacion_id);
        if ($request->filled('establecimiento_id')) $query->where('establecimiento_id', $request->establecimiento_id);

        $materias         = $query->paginate(20);
        $ciclos           = Ciclo::where('user_id', auth()->id())->orderBy('tipo')->get();
        $areas            = AreaFormacion::where('user_id', auth()->id())->orderBy('nombre')->get();
        $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();

        return view('materias.index', compact('materias', 'ciclos', 'areas', 'establecimientos'));
    }

    public function create()
    {
        $ciclos           = Ciclo::where('user_id', auth()->id())->orderBy('tipo')->get();
        $areas            = AreaFormacion::where('user_id', auth()->id())->orderBy('nombre')->get();
        $especialidades   = Especialidad::where('user_id', auth()->id())->orderBy('nombre')->get();
        $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
        $tipos            = Materia::TIPOS;
        $tiposhora        = Materia::TIPOSHORA;

        return view('materias.create', compact('ciclos', 'areas', 'especialidades', 'establecimientos', 'tipos', 'tiposhora'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'              => 'required|string|max:200',
            'ciclo_id'            => 'nullable|exists:ciclos,id',
            'area_formacion_id'   => 'nullable|exists:areasformacion,id',
            'especialidad_id'     => 'nullable|exists:especialidades,id',
            'establecimiento_id'  => 'nullable|exists:establecimientos,id',
            'anio'                => 'nullable|string|max:20',
            'tipomateria'         => 'nullable|in:aula,taller',
            'tipohora'            => 'nullable|in:' . implode(',', Materia::TIPOSHORA),
            'cargahorariasemanal' => 'nullable|integer|min:1',
            'cargahorariaanual'   => 'nullable|integer|min:1',
        ]);

        Materia::create(array_merge(
            $request->only('nombre', 'ciclo_id', 'area_formacion_id', 'especialidad_id',
                'establecimiento_id', 'anio', 'tipomateria', 'tipohora',
                'cargahorariasemanal', 'cargahorariaanual'),
            ['user_id' => auth()->id()]
        ));

        return redirect()->route('materias.index')->with('success', 'Materia creada correctamente.');
    }

    public function edit(Materia $materia)
    {
        abort_if($materia->user_id !== auth()->id(), 403);

        $ciclos           = Ciclo::where('user_id', auth()->id())->orderBy('tipo')->get();
        $areas            = AreaFormacion::where('user_id', auth()->id())->orderBy('nombre')->get();
        $especialidades   = Especialidad::where('user_id', auth()->id())->orderBy('nombre')->get();
        $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
        $tipos            = Materia::TIPOS;
        $tiposhora        = Materia::TIPOSHORA;

        return view('materias.edit', compact('materia', 'ciclos', 'areas', 'especialidades', 'establecimientos', 'tipos', 'tiposhora'));
    }

    public function update(Request $request, Materia $materia)
    {
        abort_if($materia->user_id !== auth()->id(), 403);

        $request->validate([
            'nombre'              => 'required|string|max:200',
            'ciclo_id'            => 'nullable|exists:ciclos,id',
            'area_formacion_id'   => 'nullable|exists:areasformacion,id',
            'especialidad_id'     => 'nullable|exists:especialidades,id',
            'establecimiento_id'  => 'nullable|exists:establecimientos,id',
            'anio'                => 'nullable|string|max:20',
            'tipomateria'         => 'nullable|in:aula,taller',
            'tipohora'            => 'nullable|in:' . implode(',', Materia::TIPOSHORA),
            'cargahorariasemanal' => 'nullable|integer|min:1',
            'cargahorariaanual'   => 'nullable|integer|min:1',
        ]);

        $materia->update($request->only('nombre', 'ciclo_id', 'area_formacion_id', 'especialidad_id',
            'establecimiento_id', 'anio', 'tipomateria', 'tipohora',
            'cargahorariasemanal', 'cargahorariaanual'));

        return redirect()->route('materias.index')->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia)
    {
        abort_if($materia->user_id !== auth()->id(), 403);
        $materia->delete();
        return redirect()->route('materias.index')->with('success', 'Materia eliminada correctamente.');
    }
}