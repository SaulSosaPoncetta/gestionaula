<?php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Establecimiento;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HorarioController extends Controller
{
    const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    public function index()
    {
        try {
            $dias = self::DIAS;
            $ordenDias = implode(' ', array_map(
                fn($d, $i) => "WHEN '{$d}' THEN {$i}",
                $dias, array_keys($dias)
            ));

            $horarios = Horario::with(['curso', 'materia', 'establecimiento'])
                ->where('user_id', auth()->id())
                ->orderByRaw("CASE dia {$ordenDias} ELSE 99 END")
                ->orderBy('horainicio')
                ->get()->groupBy('dia');

            return view('horarios.index', compact('horarios', 'dias'));
        } catch (QueryException $e) {
            Log::error('HorarioController@index - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los horarios.');
        } catch (\Throwable $e) {
            Log::error('HorarioController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
            $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
            $dias     = self::DIAS;

            $designacionesRaw = \App\Models\Designacion::with('horarios')
                ->where('user_id', auth()->id())->orderBy('nombreestablecimiento')->get();

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
        } catch (\Throwable $e) {
            Log::error('HorarioController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario de horarios.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'establecimiento_id' => 'nullable|exists:establecimientos,id',
                'curso_id'           => 'required|exists:cursos,id',
                'materia_id'         => 'nullable|exists:materias,id',
                'dia'                => 'required|in:' . implode(',', self::DIAS),
                'horainicio'         => 'required|date_format:H:i',
                'horafin'            => 'required|date_format:H:i|after:horainicio',
            ]);

            $curso = Curso::where('id', $request->curso_id)
                ->where('user_id', auth()->id())->firstOrFail();

            if ($request->filled('establecimiento_id')) {
                Establecimiento::where('id', $request->establecimiento_id)
                    ->where('user_id', auth()->id())->firstOrFail();
            }

            if ($request->filled('materia_id')) {
                Materia::where('id', $request->materia_id)
                    ->where('user_id', auth()->id())->firstOrFail();
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

            return redirect()->route('horarios.index')->with('success', 'Horario agregado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('HorarioController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el horario. Verificá que no esté duplicado.')->withInput();
        } catch (\Throwable $e) {
            Log::error('HorarioController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar.')->withInput();
        }
    }

    public function destroy(Horario $horario)
    {
        try {
            abort_if($horario->user_id !== auth()->id(), 403);
            $horario->delete();
            return redirect()->route('horarios.index')->with('success', 'Horario eliminado correctamente.');
        } catch (QueryException $e) {
            Log::error('HorarioController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar el horario. Tiene registros asociados.');
        } catch (\Throwable $e) {
            Log::error('HorarioController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al eliminar.');
        }
    }
}
