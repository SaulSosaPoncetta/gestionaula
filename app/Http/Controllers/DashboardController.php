<?php

namespace App\Http\Controllers;

use App\Models\Horario;

class DashboardController extends Controller
{
    public function index()
    {
        $horarios = Horario::with(['materia', 'curso.alumnos'])
            ->where('user_id', auth()->id())
            ->get()
            ->map(fn($h) => [
                'dia'        => $h->dia,
                'horainicio' => substr($h->horainicio, 0, 5),
                'horafin'    => substr($h->horafin, 0, 5),
                'materia'    => $h->materia?->nombre,
                'materia_id' => $h->materia_id,
                'curso_id'   => $h->curso_id,
                'curso'      => $h->curso?->nombre_completo,
                'alumnos'    => $h->curso?->alumnos->map(fn($a) => [
                    'id'     => $a->id,
                    'nombre' => $a->nombre_completo,
                ]) ?? [],
            ]);

        return view('dashboard', compact('horarios'));
    }
}