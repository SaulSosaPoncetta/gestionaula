<?php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\CalendarioEscolar;
use App\Http\Controllers\Concerns\DetectaHorarioActivo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use DetectaHorarioActivo;

    public function index()
    {
        $ahora      = Carbon::now('America/Argentina/Buenos_Aires');
        $horaActual = $ahora->format('H:i:s');

        $mapaDias = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles', 4 => 'jueves',
            5 => 'viernes', 6 => 'sabado', 7 => 'domingo',
        ];
        $diaActual = $mapaDias[$ahora->isoWeekday()];

        // Todos los horarios del docente (sin filtrar por establecimiento)
        $todosLosHorarios = Horario::with(['materia', 'curso', 'establecimiento'])
            ->where('user_id', auth()->id())
            ->get();

        // Horario activo ahora
        $horarioActivo = $todosLosHorarios
            ->where('dia', $diaActual)
            ->first(fn($h) =>
                $h->horainicio <= $horaActual &&
                $h->horafin    >= $horaActual
            );

        $establecimientoActual = $horarioActivo?->establecimiento;
        $materiaActual         = $horarioActivo?->materia;
        $cursoActual           = $horarioActivo?->curso;

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
                $proximoDia   = $ordenDias[($diaIndex + $i) % 7];
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

        // Próximos eventos del calendario del usuario
        $proximosEventos = CalendarioEscolar::where('user_id', auth()->id())
            ->where('fecha', '>=', $ahora->toDateString())
            ->orderBy('fecha')
            ->take(5)
            ->get();

        // Todos los horarios para el JS
        $horarios = $todosLosHorarios->map(fn($h) => [
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
            'establecimientoActual', 'materiaActual', 'cursoActual',
            'establecimientoProximo', 'materiaProxima', 'cursoProximo',
            'diaProximo', 'horaProximo',
            'proximosEventos'
        ));
    }
}
