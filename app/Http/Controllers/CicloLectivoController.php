<?php

namespace App\Http\Controllers;

use App\Models\CicloLectivo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CicloLectivoController extends Controller
{
    public function index()
    {
        $ciclos = CicloLectivo::where('user_id', auth()->id())
            ->orderBy('anio', 'desc')
            ->get();

        $cicloActivo = $ciclos->firstWhere('activo', true);

        return view('ciclos_lectivos.index', compact('ciclos', 'cicloActivo'));
    }

    public function create()
    {
        $anioSugerido = Carbon::now('America/Argentina/Buenos_Aires')->year;

        // Si ya existe uno para este año, sugerir el siguiente
        $existe = CicloLectivo::where('user_id', auth()->id())
            ->where('anio', $anioSugerido)
            ->exists();

        if ($existe) {
            $anioSugerido++;
        }

        return view('ciclos_lectivos.create', compact('anioSugerido'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anio'        => 'required|digits:4|min:2000|max:2100',
            'fechainicio' => 'required|date',
            'fechafin'    => 'required|date|after:fechainicio',
        ]);

        // Solo puede haber un ciclo activo por usuario
        if ($request->boolean('activo')) {
            CicloLectivo::where('user_id', auth()->id())
                ->where('activo', true)
                ->update(['activo' => false]);
        }

        CicloLectivo::create([
            'user_id'     => auth()->id(),
            'anio'        => $request->anio,
            'fechainicio' => $request->fechainicio,
            'fechafin'    => $request->fechafin,
            'activo'      => $request->boolean('activo', true),
        ]);

        return redirect()->route('ciclos_lectivos.index')
                         ->with('success', "Ciclo lectivo {$request->anio} creado correctamente.");
    }

    public function edit(CicloLectivo $ciclosLectivo)
    {
        abort_if($ciclosLectivo->user_id !== auth()->id(), 403);
        return view('ciclos_lectivos.edit', ['ciclo' => $ciclosLectivo]);
    }

    public function update(Request $request, CicloLectivo $ciclosLectivo)
    {
        abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

        $request->validate([
            'anio'        => 'required|digits:4|min:2000|max:2100',
            'fechainicio' => 'required|date',
            'fechafin'    => 'required|date|after:fechainicio',
        ]);

        if ($request->boolean('activo') && !$ciclosLectivo->activo) {
            CicloLectivo::where('user_id', auth()->id())
                ->where('id', '!=', $ciclosLectivo->id)
                ->where('activo', true)
                ->update(['activo' => false]);
        }

        $ciclosLectivo->update([
            'anio'        => $request->anio,
            'fechainicio' => $request->fechainicio,
            'fechafin'    => $request->fechafin,
            'activo'      => $request->boolean('activo'),
        ]);

        return redirect()->route('ciclos_lectivos.index')
                         ->with('success', "Ciclo lectivo {$request->anio} actualizado correctamente.");
    }

    public function destroy(CicloLectivo $ciclosLectivo)
    {
        abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

        if ($ciclosLectivo->activo) {
            return back()->with('error', 'No podés eliminar el ciclo lectivo activo. Activá otro primero.');
        }

        $ciclosLectivo->delete();

        return redirect()->route('ciclos_lectivos.index')
                         ->with('success', 'Ciclo lectivo eliminado correctamente.');
    }

    /**
     * Genera el siguiente ciclo lectivo basado en el actual.
     */
    public function generarSiguiente(CicloLectivo $ciclosLectivo)
    {
        abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

        $anioSiguiente = (int) $ciclosLectivo->anio + 1;

        $existe = CicloLectivo::where('user_id', auth()->id())
            ->where('anio', $anioSiguiente)
            ->first();

        if ($existe) {
            return redirect()->route('ciclos_lectivos.index')
                ->with('info', "Ya existe un ciclo lectivo para el año {$anioSiguiente}.");
        }

        return view('ciclos_lectivos.crear_siguiente', [
            'cicloActual'  => $ciclosLectivo,
            'anioSiguiente' => $anioSiguiente,
        ]);
    }

    public function activar(CicloLectivo $ciclosLectivo)
    {
        abort_if($ciclosLectivo->user_id !== auth()->id(), 403);

        CicloLectivo::where('user_id', auth()->id())
            ->where('activo', true)
            ->update(['activo' => false]);

        $ciclosLectivo->update(['activo' => true]);

        return redirect()->route('ciclos_lectivos.index')
                         ->with('success', "Ciclo lectivo {$ciclosLectivo->anio} activado.");
    }
}
