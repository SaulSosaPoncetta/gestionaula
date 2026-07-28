<?php
namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Entrega;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TareaController extends Controller
{
    public function index()
    {
        try {
            $tareas = Tarea::with(['curso', 'materia'])
                ->where('user_id', auth()->id())
                ->orderBy('fechavencimiento', 'desc')
                ->paginate(15);

            return view('tareas.index', compact('tareas'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TareaController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $cursos = Curso::where('user_id', auth()->id())
                ->with('materias')
                ->orderBy('anio')->orderBy('division')->get();

            return view('tareas.create', compact('cursos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TareaController@create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'curso_id'         => 'required|exists:cursos,id',
                'materia_id'       => 'nullable|exists:materias,id',
                'titulo'           => 'required|string|max:255',
                'descripcion'      => 'nullable|string',
                'fechavencimiento' => 'required|date',
            ]);

            $tarea = Tarea::create([
                'curso_id'         => $request->curso_id,
                'materia_id'       => $request->materia_id,
                'user_id'          => auth()->id(),
                'titulo'           => $request->titulo,
                'descripcion'      => $request->descripcion,
                'fechavencimiento' => $request->fechavencimiento,
                'estado'           => 'activa',
            ]);

            $curso = Curso::where('user_id', auth()->id())->with('alumnos')->find($request->curso_id);
            foreach ($curso->alumnos as $alumno) {
                Entrega::create([
                    'tarea_id'  => $tarea->id,
                    'alumno_id' => $alumno->id,
                    'estado'    => 'pendiente',
                ]);
            }

            DB::commit();
            return redirect()->route('tareas.index')
                             ->with('success', 'Práctico creado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TareaController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function show(Tarea $tarea)
    {
        try {
            abort_if($tarea->user_id !== auth()->id(), 403);
            $tarea->load(['curso', 'materia', 'entregas.alumno']);
            $entregas = $tarea->entregas->sortBy(fn($e) => $e->alumno->apellido);
            return view('tareas.show', compact('tarea', 'entregas'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TareaController@show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function actualizarentregas(Request $request, Tarea $tarea)
    {
        try {
            abort_if($tarea->user_id !== auth()->id(), 403);

            foreach ($request->entregas ?? [] as $entregaId => $datos) {
                Entrega::where('id', $entregaId)->update([
                    'estado'       => $datos['estado'] ?? 'pendiente',
                    'observacion'  => $datos['observacion'] ?? null,
                    'fechaentrega' => $datos['estado'] !== 'pendiente' ? now()->toDateString() : null,
                ]);
            }

            return redirect()->route('tareas.show', $tarea)
                             ->with('success', 'Entregas actualizadas correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('TareaController@actualizarentregas BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TareaController@actualizarentregas: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function cerrar(Tarea $tarea)
    {
        try {
            abort_if($tarea->user_id !== auth()->id(), 403);
            $tarea->update(['estado' => 'cerrada']);
            return redirect()->route('tareas.index')
                             ->with('success', 'Práctico cerrado correctamente.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('TareaController@cerrar BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TareaController@cerrar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}