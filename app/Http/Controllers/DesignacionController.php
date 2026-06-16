<?php
namespace App\Http\Controllers;

use App\Models\Designacion;
use App\Models\DesignacionHorario;
use Illuminate\Http\Request;

class DesignacionController extends Controller
{
    const CAMPOS_BASE = [
        'distrito', 'tipoestablecimiento', 'numeroescuela',
        'nombreestablecimiento', 'secuencia', 'dependencia_tipo',
        'regimenstatutario', 'caracterderevista', 'tipohora',
        'cupof', 'ige', 'cantmodulos', 'dependencia', 'turnodesempeno',
        'fechadesde', 'fechahasta', 'anodesignado', 'divisiondesignada',
        'fechadesignacion', 'fechatomaposecion', 'nombremateria',
    ];

    public function index(Request $request)
    {
        $query = Designacion::with('horarios')
            ->where('user_id', auth()->id())
            ->orderBy('nombreestablecimiento')
            ->orderBy('diasemana');

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombreestablecimiento', 'like', '%' . $request->buscar . '%')
                  ->orWhere('nombremateria', 'like', '%' . $request->buscar . '%')
                  ->orWhere('numeroescuela', 'like', '%' . $request->buscar . '%')
                  ->orWhere('ige', 'like', '%' . $request->buscar . '%');
            });
        }

        $designaciones = $query->paginate(20);
        return view('designaciones.index', compact('designaciones'));
    }

    public function create()
    {
        return view('designaciones.create');
    }

    private function reglasBase(): array
    {
        return [
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
            'ige'                  => 'nullable|string|max:50',
            'cantmodulos'          => 'nullable|string|max:20',
            'dependencia'          => 'nullable|string|max:200',
            'turnodesempeno'       => 'required|string|max:50',
            'fechadesde'           => 'nullable|date',
            'fechahasta'           => 'nullable|date|after_or_equal:fechadesde',
            'anodesignado'         => 'required|string|max:20',
            'divisiondesignada'    => 'required|string|max:20',
            'fechadesignacion'     => 'nullable|date',
            'fechatomaposecion'    => 'nullable|date',
            'nombremateria'        => 'required|string|max:300',
            'tipohorario'          => 'required|in:unificado,dividido',
        ];
    }

    private function reglasHorario(Request $request): array
    {
        if ($request->input('tipohorario') === 'dividido') {
            return [
                'horarios'                 => 'required|array|min:1',
                'horarios.*.dia'           => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
                'horarios.*.cantmodulos'   => 'nullable|string|max:20',
                'horarios.*.horaentrada'   => 'required|date_format:H:i',
                'horarios.*.horasalida'    => 'required|date_format:H:i|after:horarios.*.horaentrada',
            ];
        }

        return [
            'diasemana'   => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'horaentrada' => 'required|date_format:H:i',
            'horasalida'  => 'required|date_format:H:i|after:horaentrada',
        ];
    }

    private function guardarHorariosDividido(Designacion $designacion, array $horarios): void
    {
        $designacion->horarios()->delete();

        foreach ($horarios as $i => $fila) {
            DesignacionHorario::create([
                'designacion_id' => $designacion->id,
                'dia'            => $fila['dia'],
                'cantmodulos'    => $fila['cantmodulos'] ?? null,
                'horaentrada'    => $fila['horaentrada'],
                'horasalida'     => $fila['horasalida'],
                'orden'          => $i,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate(array_merge($this->reglasBase(), $this->reglasHorario($request)));

        $datos = $request->only(self::CAMPOS_BASE);
        $datos['user_id']     = auth()->id();
        $datos['tipohorario'] = $request->tipohorario;

        if ($request->tipohorario === 'unificado') {
            $datos['diasemana']   = $request->diasemana;
            $datos['horaentrada'] = $request->horaentrada;
            $datos['horasalida']  = $request->horasalida;
        } else {
            $datos['diasemana']   = null;
            $datos['horaentrada'] = null;
            $datos['horasalida']  = null;
        }

        $designacion = Designacion::create($datos);

        if ($request->tipohorario === 'dividido') {
            $this->guardarHorariosDividido($designacion, $request->input('horarios', []));
        }

        return redirect()->route('designaciones.index')
                         ->with('success', 'Designación creada correctamente.');
    }

    public function edit(Designacion $designacion)
    {
        abort_if($designacion->user_id !== auth()->id(), 403);
        $designacion->load('horarios');
        return view('designaciones.edit', compact('designacion'));
    }

    public function update(Request $request, Designacion $designacion)
    {
        abort_if($designacion->user_id !== auth()->id(), 403);

        $request->validate(array_merge($this->reglasBase(), $this->reglasHorario($request)));

        $datos = $request->only(self::CAMPOS_BASE);
        $datos['tipohorario'] = $request->tipohorario;

        if ($request->tipohorario === 'unificado') {
            $datos['diasemana']   = $request->diasemana;
            $datos['horaentrada'] = $request->horaentrada;
            $datos['horasalida']  = $request->horasalida;
        } else {
            $datos['diasemana']   = null;
            $datos['horaentrada'] = null;
            $datos['horasalida']  = null;
        }

        $designacion->update($datos);

        if ($request->tipohorario === 'dividido') {
            $this->guardarHorariosDividido($designacion, $request->input('horarios', []));
        } else {
            $designacion->horarios()->delete();
        }

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
        $designaciones = Designacion::with('horarios')
            ->where('user_id', auth()->id())
            ->orderBy('nombreestablecimiento')
            ->get();
        return response()->json($designaciones);
    }
}
