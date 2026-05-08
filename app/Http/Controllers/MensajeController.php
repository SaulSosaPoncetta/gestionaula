<?php
namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Curso;
use App\Models\Alumno;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        $query = Mensaje::with(['curso', 'alumno'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('asunto', 'like', '%' . $request->buscar . '%')
                  ->orWhere('destinatario', 'like', '%' . $request->buscar . '%');
            });
        }

        $mensajes = $query->paginate(15);
        return view('comunicacion.index', compact('mensajes'));
    }

    public function create()
    {
        $cursos = Curso::where('user_id', auth()->id())
            ->with('alumnos')
            ->orderBy('anio')->orderBy('division')->get();

        return view('comunicacion.create', compact('cursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo'         => 'required|in:general,curso,alumno',
            'asunto'       => 'required|string|max:255',
            'cuerpo'       => 'required|string',
            'curso_id'     => 'required_if:tipo,curso,alumno|nullable|exists:cursos,id',
            'alumno_id'    => 'required_if:tipo,alumno|nullable|exists:alumnos,id',
            'destinatario' => 'required|string|max:255',
        ]);

        Mensaje::create([
            'user_id'      => auth()->id(),
            'tipo'         => $request->tipo,
            'asunto'       => $request->asunto,
            'cuerpo'       => $request->cuerpo,
            'curso_id'     => $request->curso_id,
            'alumno_id'    => $request->alumno_id,
            'destinatario' => $request->destinatario,
        ]);

        return redirect()->route('comunicacion.index')
                         ->with('success', 'Mensaje enviado correctamente.');
    }

    public function show(Mensaje $mensaje)
    {
        abort_if($mensaje->user_id !== auth()->id(), 403);
        $mensaje->load(['curso', 'alumno']);
        return view('comunicacion.show', compact('mensaje'));
    }

    public function destroy(Mensaje $mensaje)
    {
        abort_if($mensaje->user_id !== auth()->id(), 403);
        $mensaje->delete();
        return redirect()->route('comunicacion.index')
                         ->with('success', 'Mensaje eliminado correctamente.');
    }
}