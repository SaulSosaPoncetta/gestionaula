<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $planes = Plan::withCount('suscripciones')->orderBy('precio')->get();
        return view('admin.planes.index', compact('planes'));
    }

    public function create()
    {
        return view('admin.planes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
            'periodicidad' => 'required|in:mensual,trimestral,anual',
        ]);

        Plan::create($request->only('nombre', 'descripcion', 'precio', 'periodicidad'));

        return redirect()->route('admin.planes.index')->with('success', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.planes.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
            'periodicidad' => 'required|in:mensual,trimestral,anual',
            'activo'       => 'nullable|boolean',
        ]);

        $plan->update([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'periodicidad' => $request->periodicidad,
            'activo'       => $request->boolean('activo'),
        ]);

        return redirect()->route('admin.planes.index')->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.planes.index')->with('success', 'Plan eliminado correctamente.');
    }
}