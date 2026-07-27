<?php
namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Establecimiento;
use App\Models\Nivel;
use App\Models\Especialidad;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CursoController extends Controller
{
    const ANIOS = ['1ro', '2do', '3ro', '4to', '5to', '6to', '7mo'];

    public function index()
    {
        try {
            $cursos = Curso::with(['establecimiento', 'nivel', 'especialidad'])
                ->where('user_id', auth()->id())
                ->withCount(['alumnos', 'materias'])
                ->orderBy('anio')->orderBy('division')
                ->paginate(15);

            return view('cursos.index', compact('cursos'));
        } catch (QueryException $e) {
            Log::error('CursoController@index - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los cursos.');
        } catch (\Throwable $e) {
            Log::error('CursoController@index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
            $niveles          = Nivel::where('user_id', auth()->id())->orderBy('nombre')->get();
            $especialidades   = Especialidad::where('user_id', auth()->id())->orderBy('nombre')->get();
            $anios            = self::ANIOS;

            return view('cursos.create', compact('establecimientos', 'niveles', 'especialidades', 'anios'));
        } catch (\Throwable $e) {
            Log::error('CursoController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'anio'               => 'nullable|string|max:10',
                'division'           => 'nullable|string|max:10',
                'turno'              => 'nullable|string|max:50',
                'nivel_id'           => 'nullable|exists:niveles,id',
                'especialidad_id'    => 'nullable|exists:especialidades,id',
                'establecimiento_id' => 'nullable|exists:establecimientos,id',
            ]);

            $nombre = trim(($request->anio ?? '') . ' ' . ($request->division ?? ''));

            Curso::create([
                'user_id'            => auth()->id(),
                'nombre'             => $nombre ?: 'Sin nombre',
                'anio'               => $request->anio,
                'division'           => $request->division,
                'turno'              => $request->turno,
                'nivel_id'           => $request->nivel_id,
                'especialidad_id'    => $request->especialidad_id,
                'establecimiento_id' => $request->establecimiento_id,
            ]);

            return redirect()->route('cursos.index')->with('success', 'Curso creado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('CursoController@store - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el curso. Intentá de nuevo.')->withInput();
        } catch (\Throwable $e) {
            Log::error('CursoController@store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar.')->withInput();
        }
    }

    public function edit(Curso $curso)
    {
        try {
            abort_if($curso->user_id !== auth()->id(), 403);

            $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
            $niveles          = Nivel::where('user_id', auth()->id())->orderBy('nombre')->get();
            $especialidades   = Especialidad::where('user_id', auth()->id())->orderBy('nombre')->get();
            $anios            = self::ANIOS;

            return view('cursos.edit', compact('curso', 'establecimientos', 'niveles', 'especialidades', 'anios'));
        } catch (\Throwable $e) {
            Log::error('CursoController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function update(Request $request, Curso $curso)
    {
        try {
            abort_if($curso->user_id !== auth()->id(), 403);

            $request->validate([
                'anio'               => 'nullable|string|max:10',
                'division'           => 'nullable|string|max:10',
                'turno'              => 'nullable|string|max:50',
                'nivel_id'           => 'nullable|exists:niveles,id',
                'especialidad_id'    => 'nullable|exists:especialidades,id',
                'establecimiento_id' => 'nullable|exists:establecimientos,id',
            ]);

            $nombre = trim(($request->anio ?? '') . ' ' . ($request->division ?? ''));

            $curso->update([
                'nombre'             => $nombre ?: $curso->nombre,
                'anio'               => $request->anio,
                'division'           => $request->division,
                'turno'              => $request->turno,
                'nivel_id'           => $request->nivel_id,
                'especialidad_id'    => $request->especialidad_id,
                'establecimiento_id' => $request->establecimiento_id,
            ]);

            return redirect()->route('cursos.index')->with('success', 'Curso actualizado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('CursoController@update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el curso.')->withInput();
        } catch (\Throwable $e) {
            Log::error('CursoController@update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al actualizar.')->withInput();
        }
    }

    public function destroy(Curso $curso)
    {
        try {
            abort_if($curso->user_id !== auth()->id(), 403);
            $curso->delete();
            return redirect()->route('cursos.index')->with('success', 'Curso eliminado correctamente.');
        } catch (QueryException $e) {
            Log::error('CursoController@destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'No se puede eliminar el curso porque tiene alumnos o materias asociados.');
        } catch (\Throwable $e) {
            Log::error('CursoController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al eliminar.');
        }
    }
}
