<?php
namespace App\Http\Controllers;

use App\Models\Designacion;
use Illuminate\Http\Request;

class DesignacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Designacion::where('user_id', auth()->id())
            ->orderBy('nombreestablecimiento')
            ->orderBy('diasemana');

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombreestablecimiento', 'like', '%' . $request->buscar . '%')
                  ->orWhere('nombremateria', 'like', '%' . $request->buscar . '%')
                  ->orWhere('numeroescuela', 'like', '%' . $request->buscar . '%');
            });
        }

        $designaciones = $query->paginate(20);
        return view('designaciones.index', compact('designaciones'));
    }

    public function create()
    {
        return view('designaciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'distrito'             => 'required|string|max:200',
            'tipoestablecimiento'  => 'required|string|max:200',
            'numeroescuela'        => 'required|string|max:50',
            'nombreestablecimiento'=> 'required|string|max:300',
            'secuencia'            => 'nullable|string|max:50',
            'dependencia_tipo'     => 'required|in:oficial,dipregep',
            'regimenstatutario'    => 'required|string|max:200',
            'caracterderevista'    => 'required|string|max:200',
            'tipohora'             => 'required|in:modulos,horas',
            'cupof'                => 'nullable|string|max:100',
            'dependencia'          => 'nullable|string|max:200',
            'turnodesempeno'       => 'required|string|max:50',
            'fechadesde'           => 'nullable|date',
            'fechahasta'           => 'nullable|date|after_or_equal:fechadesde',
            'anodesignado'         => 'required|string|max:20',
            'divisiondesignada'    => 'required|string|max:20',
            'fechadesignacion'     => 'nullable|date',
            'fechatomaposecion'    => 'nullable|date',
            'nombremateria'        => 'required|string|max:300',
            'horaentrada'          => 'required|date_format:H:i',
            'horasalida'           => 'required|date_format:H:i',
            'diasemana'            => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        ]);

        Designacion::create(array_merge(
            $request->only([
                'distrito', 'tipoestablecimiento', 'numeroescuela',
                'nombreestablecimiento', 'secuencia', 'dependencia_tipo',
                'regimenstatutario', 'caracterderevista', 'tipohora',
                'cupof', 'dependencia', 'turnodesempeno',
                'fechadesde', 'fechahasta', 'anodesignado', 'divisiondesignada',
                'fechadesignacion', 'fechatomaposecion', 'nombremateria',
                'horaentrada', 'horasalida', 'diasemana'
            ]),
            ['user_id' => auth()->id()]
        ));

        return redirect()->route('designaciones.index')
                         ->with('success', 'Designación creada correctamente.');
    }

    public function edit(Designacion $designacion)
    {
        abort_if($designacion->user_id !== auth()->id(), 403);
        return view('designaciones.edit', compact('designacion'));
    }

    public function update(Request $request, Designacion $designacion)
    {
        abort_if($designacion->user_id !== auth()->id(), 403);

        $request->validate([
            'distrito'             => 'required|string|max:200',
            'tipoestablecimiento'  => 'required|string|max:200',
            'numeroescuela'        => 'required|string|max:50',
            'nombreestablecimiento'=> 'required|string|max:300',
            'secuencia'            => 'nullable|string|max:50',
            'dependencia_tipo'     => 'required|in:oficial,dipregep',
            'regimenstatutario'    => 'required|string|max:200',
            'caracterderevista'    => 'required|string|max:200',
            'tipohora'             => 'required|in:modulos,horas',
            'cupof'                => 'nullable|string|max:100',
            'dependencia'          => 'nullable|string|max:200',
            'turnodesempeno'       => 'required|string|max:50',
            'fechadesde'           => 'nullable|date',
            'fechahasta'           => 'nullable|date',
            'anodesignado'         => 'required|string|max:20',
            'divisiondesignada'    => 'required|string|max:20',
            'fechadesignacion'     => 'nullable|date',
            'fechatomaposecion'    => 'nullable|date',
            'nombremateria'        => 'required|string|max:300',
            'horaentrada'          => 'required|date_format:H:i',
            'horasalida'           => 'required|date_format:H:i',
            'diasemana'            => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        ]);

        $designacion->update($request->only([
            'distrito', 'tipoestablecimiento', 'numeroescuela',
            'nombreestablecimiento', 'secuencia', 'dependencia_tipo',
            'regimenstatutario', 'caracterderevista', 'tipohora',
            'cupof', 'dependencia', 'turnodesempeno',
            'fechadesde', 'fechahasta', 'anodesignado', 'divisiondesignada',
            'fechadesignacion', 'fechatomaposecion', 'nombremateria',
            'horaentrada', 'horasalida', 'diasemana'
        ]));

        return redirect()->route('designaciones.index')
                         ->with('success', 'Designación actualizada correctamente.');
    }

    public function destroy(Designacion $designacion)
    {
        abort_if($designacion->user_id !== auth()->id(), 403);
        $designacion->delete();
        return redirect()->route('designaciones.index')
                         ->with('success', 'Designación eliminada correctamente.');
    }

    // API para el HorarioController
    public function listar()
    {
        $designaciones = Designacion::where('user_id', auth()->id())
            ->orderBy('nombreestablecimiento')
            ->get();
        return response()->json($designaciones);
    }
}