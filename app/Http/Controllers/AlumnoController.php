<?php
namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Curso;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $cursos = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            $query  = Alumno::with('curso')->where('user_id', auth()->id())->orderBy('apellido');

            if ($request->filled('curso_id'))    $query->where('curso_id', $request->curso_id);
            if ($request->filled('tipocursada')) $query->where('tipocursada', $request->tipocursada);
            if ($request->filled('buscar')) {
                $query->where(function ($q) use ($request) {
                    $q->where('apellido', 'like', '%' . $request->buscar . '%')
                      ->orWhere('nombre',  'like', '%' . $request->buscar . '%');
                });
            }

            $alumnos = $query->paginate(20);
            return view('alumnos.index', compact('alumnos', 'cursos'));
        } catch (QueryException $e) {
            Log::error('AlumnoController@index - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los alumnos. Intentá de nuevo.');
        } catch (\Throwable $e) {
            Log::error('AlumnoController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $cursos = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            return view('alumnos.create', compact('cursos'));
        } catch (\Throwable $e) {
            Log::error('AlumnoController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'          => 'required|string|max:100',
                'apellido'        => 'required|string|max:100',
                'dni'             => 'nullable|string|max:20',
                'fechanacimiento' => 'nullable|date',
                'telefono'        => 'nullable|string|max:30',
                'email'           => 'nullable|email|max:100',
                'tipocursada'     => 'required|in:regular,libre,recursa,intensifica',
                'curso_id'        => 'required|exists:cursos,id',
            ]);

            Alumno::create([
                'user_id'         => auth()->id(),
                'nombre'          => $request->nombre,
                'apellido'        => $request->apellido,
                'dni'             => $request->dni,
                'fechanacimiento' => $request->fechanacimiento,
                'telefono'        => $request->telefono,
                'email'           => $request->email,
                'tipocursada'     => $request->tipocursada,
                'curso_id'        => $request->curso_id,
            ]);

            return redirect()->route('alumnos.index')->with('success', 'Alumno creado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('AlumnoController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el alumno. Verificá que el DNI no esté repetido.')->withInput();
        } catch (\Throwable $e) {
            Log::error('AlumnoController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar.')->withInput();
        }
    }

    public function show(Alumno $alumno)
    {
        try {
            abort_if($alumno->user_id !== auth()->id(), 403);
            $alumno->load(['curso.especialidad', 'curso.nivel', 'curso.establecimiento']);

            $asistencias = \App\Models\Asistencia::with('materia')
                ->where('alumno_id', $alumno->id)
                ->orderBy('fecha', 'desc')->get();

            $resumen = [
                'presente'    => $asistencias->where('estado', 'presente')->count(),
                'ausente'     => $asistencias->where('estado', 'ausente')->count(),
                'tarde'       => $asistencias->where('estado', 'tarde')->count(),
                'justificado' => $asistencias->where('estado', 'justificado')->count(),
                'total'       => $asistencias->count(),
            ];

            return view('alumnos.show', compact('alumno', 'asistencias', 'resumen'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('alumnos.index')->with('error', 'Alumno no encontrado.');
        } catch (\Throwable $e) {
            Log::error('AlumnoController@show: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el alumno.');
        }
    }

    public function edit(Alumno $alumno)
    {
        try {
            abort_if($alumno->user_id !== auth()->id(), 403);
            $cursos = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
            return view('alumnos.edit', compact('alumno', 'cursos'));
        } catch (\Throwable $e) {
            Log::error('AlumnoController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function update(Request $request, Alumno $alumno)
    {
        try {
            DB::beginTransaction();
            abort_if($alumno->user_id !== auth()->id(), 403);

            $request->validate([
                'nombre'          => 'required|string|max:100',
                'apellido'        => 'required|string|max:100',
                'dni'             => 'nullable|string|max:20',
                'fechanacimiento' => 'nullable|date',
                'telefono'        => 'nullable|string|max:30',
                'email'           => 'nullable|email|max:100',
                'tipocursada'     => 'required|in:regular,libre,recursa,intensifica',
                'curso_id'        => 'required|exists:cursos,id',
            ]);

            $alumno->update($request->only(
                'nombre', 'apellido', 'dni', 'fechanacimiento',
                'telefono', 'email', 'tipocursada', 'curso_id'
            ));

            return redirect()->route('alumnos.index', array_filter([
                'curso_id'    => $request->filtro_curso_id,
                'tipocursada' => $request->filtro_tipocursada,
                'buscar'      => $request->filtro_buscar,
            ]))->with('success', 'Alumno actualizado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('AlumnoController@update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el alumno.')->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AlumnoController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al actualizar.')->withInput();
        }
    }

    public function destroy(Alumno $alumno)
    {
        try {
            DB::beginTransaction();
            abort_if($alumno->user_id !== auth()->id(), 403);
            $cursoId = $alumno->curso_id;
            $alumno->delete();
            return redirect()->route('alumnos.index', ['curso_id' => $cursoId])
                             ->with('success', 'Alumno eliminado correctamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('AlumnoController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar el alumno porque tiene registros asociados.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AlumnoController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al eliminar.');
        }
    }
}
