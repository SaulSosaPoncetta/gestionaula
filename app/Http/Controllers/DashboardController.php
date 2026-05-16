<?php
namespace App\Http\Controllers;

use App\Models\Horario;

class DashboardController extends Controller
{
    public function index()
    {
        $ahora     = now();
        $diaActual = strtolower($ahora->locale('es')->isoFormat('dddd'));
        $diaActual = str_replace(
            ['lunes','martes','miércoles','jueves','viernes','sábado','domingo'],
            ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'],
            $diaActual
        );
        $horaActual = $ahora->format('H:i:s');

        // Horario activo ahora
        $horarioActivo = Horario::with(['materia', 'curso', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->where('dia', $diaActual)
            ->where('horainicio', '<=', $horaActual)
            ->where('horafin', '>=', $horaActual)
            ->first();

        // Datos del contexto actual
        $establecimientoActual = $horarioActivo?->establecimiento;
        $materiaActual         = $horarioActivo?->materia;
        $cursoActual           = $horarioActivo?->curso;

        // Todos los horarios para el dashboard JS
        $horarios = Horario::with(['materia', 'curso.alumnos', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->get()
            ->map(fn($h) => [
                'dia'            => $h->dia,
                'horainicio'     => substr($h->horainicio, 0, 5),
                'horafin'        => substr($h->horafin, 0, 5),
                'materia'        => $h->materia?->nombre,
                'materia_id'     => $h->materia_id,
                'curso_id'       => $h->curso_id,
                'curso'          => $h->curso?->nombre_completo,
                'establecimiento'=> $h->establecimiento?->nombre,
                'alumnos'        => $h->curso?->alumnos
                    ->where('user_id', auth()->id())
                    ->map(fn($a) => [
                        'id'     => $a->id,
                        'nombre' => $a->nombre_completo,
                    ])->values() ?? [],
            ]);

        return view('dashboard', compact(
            'horarios',
            'establecimientoActual',
            'materiaActual',
            'cursoActual'
        ));
    }
}