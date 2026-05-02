<?php
namespace App\Http\Controllers;
use App\Models\Ciclo;
use Illuminate\Http\Request;

class CicloController extends Controller
{
    public function index()
    {
        $ciclos = Ciclo::withCount('materias')
            ->orderBy('tipo')
            ->paginate(15);

        // Total de materias en ciclos básicos y superiores
        $totalBasico   = Ciclo::where('tipo', 'basico')->withCount('materias')->get()->sum('materias_count');
        $totalSuperior = Ciclo::where('tipo', 'superior')->withCount('materias')->get()->sum('materias_count');

        return view('ciclos.index', compact('ciclos', 'totalBasico', 'totalSuperior'));
    }

    public function create()
    {
        $tipos = Ciclo::TIPOS;
        return view('ciclos.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200',
            'tipo'        => 'required|in:' . implode(',', Ciclo::TIPOS),
            'descripcion' => 'nullable|string',
        ]);
        Ciclo::create($request->only('nombre', 'tipo', 'descripcion'));
        return redirect()->route('ciclos.index')
                         ->with('success', 'Ciclo creado correctamente.');
    }

    public function edit(Ciclo $ciclo)
    {
        $tipos = Ciclo::TIPOS;
        return view('ciclos.edit', compact('ciclo', 'tipos'));
    }

    public function update(Request $request, Ciclo $ciclo)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200',
            'tipo'        => 'required|in:' . implode(',', Ciclo::TIPOS),
            'descripcion' => 'nullable|string',
        ]);
        $ciclo->update($request->only('nombre', 'tipo', 'descripcion'));
        return redirect()->route('ciclos.index')
                         ->with('success', 'Ciclo actualizado correctamente.');
    }

    public function destroy(Ciclo $ciclo)
    {
        $ciclo->delete();
        return redirect()->route('ciclos.index')
                         ->with('success', 'Ciclo eliminado correctamente.');
    }
}