<?php
namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoAlumno;
use App\Models\CarpetaCampo;
use App\Models\CarpetaCampoEntrada;
use App\Models\Actividad;
use App\Models\ActividadItem;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Establecimiento;
use App\Models\TipoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $materias = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos   = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();

        $query = Proyecto::with(['materia', 'curso', 'establecimiento', 'alumnos'])
            ->where('user_id', auth()->id())
            ->orderBy('fecha', 'desc');

        if ($request->filled('materia_id')) $query->where('materia_id', $request->materia_id);
        if ($request->filled('curso_id'))   $query->where('curso_id', $request->curso_id);
        if ($request->filled('estado'))     $query->where('estado', $request->estado);

        $proyectos = $query->paginate(15);

        return view('proyectos.index', compact('proyectos', 'materias', 'cursos'));
    }

    public function create()
    {
        $materias       = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos         = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
        $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
        $tiposactividad = TipoActividad::orderBy('denominacion')->get();

        return view('proyectos.create', compact('materias', 'cursos', 'establecimientos', 'tiposactividad'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'             => 'required|string|max:300',
            'materia_id'         => 'required|exists:materias,id',
            'curso_id'           => 'required|exists:cursos,id',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
            'fecha'              => 'nullable|date',
            'hora'               => 'nullable|date_format:H:i',
            'fechapresentacion'  => 'nullable|date',
            'descripcion'        => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'alumnos'            => 'required|array|min:1',
            'alumnos.*'          => 'exists:alumnos,id',
            'tipoactividad_id'   => 'required|exists:tiposactividad,id',
            'numerounidad'       => 'required|integer|min:1',
        ]);

        // 1. Crear la actividad automáticamente
        $actividad = Actividad::create([
            'user_id'          => auth()->id(),
            'materia_id'       => $request->materia_id,
            'tipoactividad_id' => $request->tipoactividad_id,
            'titulo'           => $request->titulo,
            'numerounidad'     => $request->numerounidad,
            'tema'             => $request->titulo,
            'descripcion'      => $request->descripcion,
            'estado'           => 'activa',
        ]);

        // 2. Crear el proyecto
        $proyecto = Proyecto::create([
            'user_id'            => auth()->id(),
            'materia_id'         => $request->materia_id,
            'curso_id'           => $request->curso_id,
            'establecimiento_id' => $request->establecimiento_id,
            'actividad_id'       => $actividad->id,
            'titulo'             => $request->titulo,
            'descripcion'        => $request->descripcion,
            'fecha'              => $request->fecha,
            'hora'               => $request->hora,
            'fechapresentacion'  => $request->fechapresentacion,
            'observaciones'      => $request->observaciones,
            'estado'             => 'activo',
        ]);

        // 3. Asignar alumnos y crear carpeta de campo por alumno
        foreach ($request->alumnos as $alumnoId) {
            // Asignar alumno al proyecto
            ProyectoAlumno::create([
                'proyecto_id' => $proyecto->id,
                'alumno_id'   => $alumnoId,
            ]);

            // Crear carpeta de campo para el alumno
            CarpetaCampo::create([
                'user_id'     => auth()->id(),
                'proyecto_id' => $proyecto->id,
                'alumno_id'   => $alumnoId,
                'titulo'      => $request->titulo,
                'subtitulo'   => 'Carpeta de campo — ' . $request->titulo,
                'descripcion' => $request->descripcion,
            ]);
        }

        return redirect()->route('proyectos.show', $proyecto)
                         ->with('success', 'Proyecto creado correctamente. Actividad y carpetas de campo generadas automáticamente.');
    }

    public function show(Proyecto $proyecto)
    {
        abort_if($proyecto->user_id !== auth()->id(), 403);
        $proyecto->load([
            'materia', 'curso', 'establecimiento', 'actividad',
            'alumnos', 'carpetas.alumno', 'carpetas.entradas',
        ]);
        return view('proyectos.show', compact('proyecto'));
    }

    public function edit(Proyecto $proyecto)
    {
        abort_if($proyecto->user_id !== auth()->id(), 403);
        $materias         = Materia::where('user_id', auth()->id())->orderBy('nombre')->get();
        $cursos           = Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get();
        $establecimientos = Establecimiento::where('user_id', auth()->id())->orderBy('nombre')->get();
        $proyecto->load('alumnos');
        return view('proyectos.edit', compact('proyecto', 'materias', 'cursos', 'establecimientos'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        abort_if($proyecto->user_id !== auth()->id(), 403);

        $request->validate([
            'titulo'             => 'required|string|max:300',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
            'fecha'              => 'nullable|date',
            'hora'               => 'nullable|date_format:H:i',
            'fechapresentacion'  => 'nullable|date',
            'descripcion'        => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'estado'             => 'required|in:borrador,activo,presentado,cerrado',
        ]);

        $proyecto->update($request->only(
            'titulo', 'establecimiento_id', 'fecha', 'hora',
            'fechapresentacion', 'descripcion', 'observaciones', 'estado'
        ));

        return redirect()->route('proyectos.show', $proyecto)
                         ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        abort_if($proyecto->user_id !== auth()->id(), 403);
        $proyecto->delete();
        return redirect()->route('proyectos.index')
                         ->with('success', 'Proyecto eliminado correctamente.');
    }

    // --- Carpeta de campo ---

    public function carpeta(CarpetaCampo $carpeta)
    {
        abort_if($carpeta->user_id !== auth()->id(), 403);
        $carpeta->load(['proyecto', 'alumno', 'entradas']);
        return view('proyectos.carpeta', compact('carpeta'));
    }

    public function agregarEntrada(Request $request, CarpetaCampo $carpeta)
    {
        abort_if($carpeta->user_id !== auth()->id(), 403);

        $request->validate([
            'tipo'        => 'required|in:nota,documento,imagen,actividad,seguimiento',
            'titulo'      => 'required|string|max:300',
            'descripcion' => 'nullable|string',
            'fecha'       => 'required|date',
            'archivo'     => 'nullable|file|max:10240',
        ]);

        $archivoRuta = null;
        if ($request->hasFile('archivo')) {
            $archivoRuta = $request->file('archivo')
                ->store("carpetacampo/{$carpeta->id}", 'public');
        }

        $orden = $carpeta->entradas()->count() + 1;

        CarpetaCampoEntrada::create([
            'carpeta_id'  => $carpeta->id,
            'user_id'     => auth()->id(),
            'tipo'        => $request->tipo,
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
            'archivo'     => $archivoRuta,
            'fecha'       => $request->fecha,
            'orden'       => $orden,
        ]);

        return redirect()->route('proyectos.carpeta', $carpeta)
                         ->with('success', 'Entrada agregada correctamente.');
    }

    public function eliminarEntrada(CarpetaCampoEntrada $entrada)
    {
        abort_if($entrada->user_id !== auth()->id(), 403);
        if ($entrada->archivo) {
            Storage::disk('public')->delete($entrada->archivo);
        }
        $entrada->delete();
        return redirect()->back()->with('success', 'Entrada eliminada correctamente.');
    }
}