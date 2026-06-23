<?php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\CalendarioEscolar;
use App\Models\Establecimiento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $ahora      = Carbon::now('America/Argentina/Buenos_Aires');
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

        // IDs de establecimientos del usuario logueado
        $establecimientosDelUsuario = Establecimiento::where('user_id', auth()->id())
            ->pluck('id')
            ->toArray();

        // Todos los horarios del docente
        $todosLosHorarios = Horario::with(['materia', 'curso', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->whereIn('establecimiento_id', $establecimientosDelUsuario)
            ->get();

        // Horario activo ahora
        $horarioActivo = $todosLosHorarios
            ->where('dia', $diaActual)
            ->filter(fn($h) =>
                $h->horainicio <= $horaActual &&
                $h->horafin    >= $horaActual
            )->first();

        $establecimientoActual = $horarioActivo?->establecimiento;
        $materiaActual         = $horarioActivo?->materia;
        $cursoActual           = $horarioActivo?->curso;

        // Verificar que el establecimiento pertenece al usuario
        if ($establecimientoActual && !in_array($establecimientoActual->id, $establecimientosDelUsuario)) {
            $establecimientoActual = null;
        }

        // Próxima clase
        $ordenDias = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $diaIndex  = array_search($diaActual, $ordenDias);

        $proximoHorario = null;

        $proximoHoy = $todosLosHorarios
            ->where('dia', $diaActual)
            ->filter(fn($h) => $h->horainicio > $horaActual)
            ->sortBy('horainicio')
            ->first();

        if ($proximoHoy) {
            $proximoHorario = $proximoHoy;
        } else {
            for ($i = 1; $i <= 7; $i++) {
                $proximoDia = $ordenDias[($diaIndex + $i) % 7];
                $proximoEnDia = $todosLosHorarios
                    ->where('dia', $proximoDia)
                    ->sortBy('horainicio')
                    ->first();
                if ($proximoEnDia) {
                    $proximoHorario = $proximoEnDia;
                    break;
                }
            }
        }

        $establecimientoProximo = $proximoHorario?->establecimiento;
        $materiaProxima         = $proximoHorario?->materia;
        $cursoProximo           = $proximoHorario?->curso;
        $diaProximo             = $proximoHorario?->dia;
        $horaProximo            = $proximoHorario ? substr($proximoHorario->horainicio, 0, 5) : null;

        // Verificar que el establecimiento próximo pertenece al usuario
        if ($establecimientoProximo && !in_array($establecimientoProximo->id, $establecimientosDelUsuario)) {
            $establecimientoProximo = null;
        }

        // Próximos eventos del calendario del usuario
        $proximosEventos = CalendarioEscolar::where('user_id', auth()->id())
            ->where('fecha', '>=', $ahora->toDateString())
            ->orderBy('fecha')
            ->take(5)
            ->get();

        // Todos los horarios para el JS — solo alumnos del usuario
        $horarios = $todosLosHorarios->map(fn($h) => [
            'dia'             => $h->dia,
            'horainicio'      => substr($h->horainicio, 0, 5),
            'horafin'         => substr($h->horafin, 0, 5),
            'materia'         => $h->materia?->nombre,
            'materia_id'      => $h->materia_id,
            'curso_id'        => $h->curso_id,
            'curso'           => $h->curso?->nombre_completo,
            'establecimiento' => in_array($h->establecimiento_id, $establecimientosDelUsuario)
                                    ? $h->establecimiento?->nombre
                                    : null,
            'alumnos'         => $h->curso?->alumnos
                ->where('user_id', auth()->id())
                ->map(fn($a) => [
                    'id'     => $a->id,
                    'nombre' => $a->nombre_completo,
                ])->values() ?? [],
        ]);

        return view('dashboard', compact(
            'horarios',
            'establecimientoActual', 'materiaActual', 'cursoActual',
            'establecimientoProximo', 'materiaProxima', 'cursoProximo',
            'diaProximo', 'horaProximo',
            'proximosEventos'
        ));
    }
}