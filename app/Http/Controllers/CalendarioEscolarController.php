<?php

namespace App\Http\Controllers;

use App\Models\CalendarioEscolar;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CalendarioEscolarController extends Controller
{
    public function index()
    {
        try {
            $eventos = CalendarioEscolar::with('periodo')
                ->where('user_id', auth()->id())
                ->orderBy('fecha')
                ->paginate(20);

            return view('calendarioescolar.index', compact('eventos'));

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
            $periodos = Periodo::orderBy('orden')->get();
            return view('calendarioescolar.create', compact('periodos'));

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
                'fecha'        => 'required|date',
                'denominacion' => 'required|string|max:300',
                'periodo_id'   => 'nullable|exists:periodos,id',
                'esferiado'    => 'nullable|boolean',
                'fechainicio'  => 'nullable|date',
                'fechafin'     => 'nullable|date|after_or_equal:fechainicio',
            ]);

            CalendarioEscolar::create([
                'user_id'      => auth()->id(),
                'fecha'        => $request->fecha,
                'denominacion' => $request->denominacion,
                'periodo_id'   => $request->periodo_id,
                'esferiado'    => $request->boolean('esferiado'),
                'fechainicio'  => $request->fechainicio,
                'fechafin'     => $request->fechafin,
            ]);

            return redirect()->route('calendarioescolar.index')
                             ->with('success', 'Evento creado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.store: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function edit(CalendarioEscolar $calendarioescolar)
    {
        try {
            abort_if($calendarioescolar->user_id !== auth()->id(), 403);
            $periodos = Periodo::orderBy('orden')->get();
            return view('calendarioescolar.edit', compact('calendarioescolar', 'periodos'));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'El registro solicitado no existe.');
        } catch (\Throwable $e) {
            Log::error('Controllers.edit: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function update(Request $request, CalendarioEscolar $calendarioescolar)
    {
        try {
            abort_if($calendarioescolar->user_id !== auth()->id(), 403);

            $request->validate([
                'fecha'        => 'required|date',
                'denominacion' => 'required|string|max:300',
                'periodo_id'   => 'nullable|exists:periodos,id',
                'esferiado'    => 'nullable|boolean',
                'fechainicio'  => 'nullable|date',
                'fechafin'     => 'nullable|date|after_or_equal:fechainicio',
            ]);

            $calendarioescolar->update([
                'fecha'        => $request->fecha,
                'denominacion' => $request->denominacion,
                'periodo_id'   => $request->periodo_id,
                'esferiado'    => $request->boolean('esferiado'),
                'fechainicio'  => $request->fechainicio,
                'fechafin'     => $request->fechafin,
            ]);

            return redirect()->route('calendarioescolar.index')
                             ->with('success', 'Evento actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('Controllers.update - BD: ' . $e->getMessage());
            return back()->with('error', 'Error en la base de datos. Intentá de nuevo.')->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El registro no existe o no te pertenece.');
        } catch (\Throwable $e) {
            Log::error('Controllers.update: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado.')->withInput();
        }
    }

    public function destroy(CalendarioEscolar $calendarioescolar)
    {
        try {
            abort_if($calendarioescolar->user_id !== auth()->id(), 403);
            $calendarioescolar->delete();
            return redirect()->route('calendarioescolar.index')
                             ->with('success', 'Evento eliminado correctamente.');

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