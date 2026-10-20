<?php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    public function index()
    {
        $dias = self::DIAS;

        $ordenDias = implode(' ', array_map(
            fn($d, $i) => "WHEN '{$d}' THEN {$i}",
            $dias,
            array_keys($dias)
        ));

        $horarios = Horario::with(['curso', 'materia', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->orderByRaw("CASE dia {$ordenDias} ELSE 99 END")
            ->orderBy('horainicio')
            ->get()
            ->groupBy('dia');

        return view('horarios.index', compact('horarios', 'dias'));
    }

    public function create()
{
    $establecimientos = \App\Models\Establecimiento::where(function($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('nombre')->get();
    $cursos   = \App\Models\Curso::where(function($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('anio')->orderBy('division')->get();
    $materias = \App\Models\Materia::where(function($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('nombre')->get();
    $dias     = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];

    $designacionesRaw = \App\Models\Designacion::with('horarios')
        ->where('user_id', auth()->id())
        ->orderBy('nombreestablecimiento')
        ->get();

    // Aplanar: una opcion por cada (designacion, dia) -- tanto en modo
    // unificado (1 dia) como dividido (N dias).
    $designacionesFilas = collect();
    foreach ($designacionesRaw as $desig) {
        foreach ($desig->filasHorario() as $fila) {
            $designacionesFilas->push(array_merge($fila, [
                'nombreestablecimiento' => $desig->nombreestablecimiento,
                'nombremateria'         => $desig->nombremateria,
                'anodesignado'          => $desig->anodesignado,
                'divisiondesignada'     => $desig->divisiondesignada,
                'turno'                 => $desig->turnodesempeno,
                'tipohorario'           => $desig->tipohorario,
            ]));
        }
    }

    return view('horarios.create', compact(
        'establecimientos', 'cursos', 'materias', 'dias', 'designacionesFilas'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
            'curso_id'           => 'required|exists:cursos,id',
            'materia_id'         => 'nullable|exists:materias,id',
            'dia'                => 'required|in:' . implode(',', self::DIAS),
            'horainicio'         => 'required|date_format:H:i',
            'horafin'            => 'required|date_format:H:i|after:horainicio',
        ]);

        $curso = Curso::where('id', $request->curso_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->filled('establecimiento_id')) {
            Establecimiento::where('id', $request->establecimiento_id)
                ->where(function($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->firstOrFail();
        }

        if ($request->filled('materia_id')) {
            Materia::where('id', $request->materia_id)
                ->where(function($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->firstOrFail();
        }

        Horario::create([
            'user_id'            => auth()->id(),
            'establecimiento_id' => $request->establecimiento_id ?: null,
            'curso_id'           => $curso->id,
            'materia_id'         => $request->materia_id ?: null,
            'dia'                => $request->dia,
            'horainicio'         => $request->horainicio,
            'horafin'            => $request->horafin,
        ]);

        return redirect()->route('horarios.index')
                         ->with('success', 'Horario agregado correctamente.');
    }

    public function destroy(Horario $horario)
    {
        abort_if($horario->user_id !== auth()->id(), 403);
        $horario->delete();
        return redirect()->route('horarios.index')
                         ->with('success', 'Horario eliminado correctamente.');
    }

    // ── Altas rápidas desde el formulario (AJAX) ──────────────────────────────

    public function quickEstablecimiento(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate(['nombre' => 'required|string|max:255']);

            $est = \App\Models\Establecimiento::firstOrCreate(
                ['user_id' => auth()->id(), 'nombre' => trim($request->nombre)]
            );

            return response()->json(['id' => $est->id, 'label' => $est->nombre]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function quickCurso(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'anio'     => 'required|string|max:10',
                'division' => 'required|string|max:10',
            ]);

            $anio     = trim($request->anio);
            $division = strtoupper(trim($request->division));
            $turno    = $request->turno ?: null;
            $nombre   = $anio . '° ' . $division . ($turno ? ' — ' . ucfirst($turno) : '');

            $curso = Curso::firstOrCreate(
                [
                    'user_id'  => auth()->id(),
                    'anio'     => $anio,
                    'division' => $division,
                ],
                [
                    'nombre' => $nombre,
                    'turno'  => $turno,
                ]
            );

            $label = $curso->anio . '° ' . $curso->division
                   . ($curso->turno ? ' — ' . ucfirst($curso->turno) : '');

            return response()->json(['id' => $curso->id, 'label' => $label]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function quickMateria(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate(['nombre' => 'required|string|max:255']);

            $materia = Materia::firstOrCreate(
                ['user_id' => auth()->id(), 'nombre' => trim($request->nombre)],
                [
                    'tipomateria' => $request->tipo     ?? 'aula',
                    'tipohora'    => $request->tipoHora ?? 'catedra',
                ]
            );

            return response()->json(['id' => $materia->id, 'label' => $materia->nombre]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}