<?php
namespace App\Http\Controllers;

use App\Models\MaterialTeoricoArchivo;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialTeoricoController extends Controller
{
    public function index()
    {
        $archivos = MaterialTeoricoArchivo::with('tarea')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('materialteoricoarchivos.index', compact('archivos'));
    }

    public function create()
    {
        $tareas = Tarea::where('user_id', auth()->id())
            ->orderBy('titulo')
            ->get();

        return view('materialteoricoarchivos.create', compact('tareas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|string|max:300',
            'descripcion' => 'nullable|string|max:500',
            'tarea_id'    => 'nullable|exists:tareas,id',
            'archivos'    => 'required|array|max:3',
            'archivos.*'  => 'required|file|mimes:pdf|max:10240',
        ]);

        $existentes = MaterialTeoricoArchivo::where('user_id', auth()->id())
            ->when($request->tarea_id, fn($q) => $q->where('tarea_id', $request->tarea_id))
            ->count();

        if ($existentes + count($request->file('archivos')) > 3) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No podés superar 3 archivos PDF por práctico.');
        }

        foreach ($request->file('archivos') as $i => $file) {
            $ruta = $file->store('materialteoricoarchivos/' . auth()->id(), 'public');
            MaterialTeoricoArchivo::create([
                'user_id'     => auth()->id(),
                'tarea_id'    => $request->tarea_id,
                'titulo'      => $request->titulo,
                'descripcion' => $request->descripcion,
                'ruta'        => $ruta,
                'orden'       => $existentes + $i + 1,
            ]);
        }

        return redirect()->route('materialteoricoarchivos.index')
                         ->with('success', 'Material teórico subido correctamente.');
    }

    public function destroy(MaterialTeoricoArchivo $materialteoricoarchivo)
    {
        abort_if($materialteoricoarchivo->user_id !== auth()->id(), 403);
        Storage::disk('public')->delete($materialteoricoarchivo->ruta);
        $materialteoricoarchivo->delete();
        return redirect()->route('materialteoricoarchivos.index')
                         ->with('success', 'Archivo eliminado correctamente.');
    }
}