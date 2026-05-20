<?php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\CalendarioEscolar;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $ahora      = Carbon::now();
        $horaActual = $ahora->format('H:i:s');

        $mapaDias = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo',
        ];

        $diaActual = $mapaDias[$ahora->isoWeekday()];

        // Horario activo ahora
        $horarioActivo = Horario::with(['materia', 'curso', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->where('dia', $diaActual)
            ->where('horainicio', '<=', $horaActual)
            ->where('horafin', '>=', $horaActual)
            ->first();

        $establecimientoActual = $horarioActivo?->establecimiento;
        $materiaActual         = $horarioActivo?->materia;
        $cursoActual           = $horarioActivo?->curso;

        // Próximos eventos del calendario escolar (desde hoy)
        $proximosEventos = CalendarioEscolar::where('user_id', auth()->id())
            ->where('fecha', '>=', $ahora->toDateString())
            ->orderBy('fecha')
            ->take(5)
            ->get();

        // Evento más próximo
        $proximoEvento = $proximosEventos->first();

        // Todos los horarios para el JS
        $horarios = Horario::with(['materia', 'curso.alumnos', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->get()
            ->map(fn($h) => [
                'dia'             => $h->dia,
                'horainicio'      => substr($h->horainicio, 0, 5),
                'horafin'         => substr($h->horafin, 0, 5),
                'materia'         => $h->materia?->nombre,
                'materia_id'      => $h->materia_id,
                'curso_id'        => $h->curso_id,
                'curso'           => $h->curso?->nombre_completo,
                'establecimiento' => $h->establecimiento?->nombre,
                'alumnos'         => $h->curso?->alumnos
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
            'cursoActual',
            'proximoEvento',
            'proximosEventos'
        ));
    }
}