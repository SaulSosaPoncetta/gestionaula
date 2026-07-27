<?php
namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Curso;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        try {
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

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function create()
    {
        try {
            $cursos = Curso::where('user_id', auth()->id())
                ->with('alumnos')
                ->orderBy('anio')->orderBy('division')->get();

            return view('comunicacion.create', compact('cursos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.create: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function store(Request $request)
    {
        try {
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

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function show(Mensaje $mensaje)
    {
        try {
            abort_if($mensaje->user_id !== auth()->id(), 403);
            $mensaje->load(['curso', 'alumno']);
            return view('comunicacion.show', compact('mensaje'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.show: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function destroy(Mensaje $mensaje)
    {
        try {
            abort_if($mensaje->user_id !== auth()->id(), 403);
            $mensaje->delete();
            return redirect()->route('comunicacion.index')
                             ->with('success', 'Mensaje eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('Controllers.destroy - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.destroy: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }
}