<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\CalendarioEscolar;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Asistencia;
use App\Models\ActividadAsignacion;
use App\Models\ActividadNota;
use App\Models\CierreNota;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function resumen(Request $request)
    {
        $userId = $request->user()->id;
        $ahora = Carbon::now('America/Argentina/Buenos_Aires');
        $horaActual = $ahora->format('H:i:s');

        $mapaDias = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles', 4 => 'jueves',
            5 => 'viernes', 6 => 'sabado', 7 => 'domingo',
        ];
        $diaActual = $mapaDias[$ahora->isoWeekday()];

        $todosLosHorarios = Horario::with(['materia', 'curso', 'establecimiento'])
            ->where('user_id', $userId)
            ->get();

        $horarioActivo = $todosLosHorarios
            ->where('dia', $diaActual)
            ->first(fn($h) => $h->horainicio <= $horaActual && $h->horafin >= $horaActual);

        $ordenDias = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $diaIndex = array_search($diaActual, $ordenDias);

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
                $proximoEnDia = $todosLosHorarios->where('dia', $proximoDia)->sortBy('horainicio')->first();
                if ($proximoEnDia) {
                    $proximoHorario = $proximoEnDia;
                    break;
                }
            }
        }

        $proximosEventos = CalendarioEscolar::where('user_id', $userId)
            ->where('fecha', '>=', $ahora->toDateString())
            ->orderBy('fecha')
            ->take(3)
            ->get();

        return response()->json([
            'clase_activa' => $horarioActivo ? $this->formatearHorario($horarioActivo) : null,
            'clase_proxima' => $proximoHorario ? $this->formatearHorario($proximoHorario) : null,
            'proximos_eventos' => $proximosEventos,
        ]);
    }

    private function formatearHorario($h)
    {
        return [
            'dia' => $h->dia,
            'horainicio' => substr($h->horainicio, 0, 5),
            'horafin' => substr($h->horafin, 0, 5),
            'materia_id' => $h->materia_id,
            'materia' => $h->materia?->nombre,
            'curso_id' => $h->curso_id,
            'curso' => $h->curso?->nombre_completo,
            'establecimiento' => $h->establecimiento ? [
                'nombre' => $h->establecimiento->nombre,
                'direccion' => $h->establecimiento->direccion,
                'localidad' => $h->establecimiento->localidad,
                'provincia' => $h->establecimiento->provincia,
                'telefono' => $h->establecimiento->telefono,
            ] : null,
        ];
    }

    public function stats(Request $request, int $cursoId, int $materiaId)
    {
        $userId = $request->user()->id;

        $curso = Curso::where('id', $cursoId)->where('user_id', $userId)->first();
        $materia = Materia::where('id', $materiaId)->where('user_id', $userId)->first();

        if (!$curso || !$materia) {
            return response()->json(['error' => 'No encontrado'], 404);
        }

        $alumnos = $curso->alumnos()->where('alumnos.user_id', $userId)->get();
        $asignaciones = ActividadAsignacion::where('materia_id', $materia->id)
            ->where('curso_id', $curso->id)
            ->where('user_id', $userId)
            ->pluck('id');

        $stats = $alumnos->map(function ($alumno) use ($materia, $asignaciones, $userId) {
            $asistencias = Asistencia::where('alumno_id', $alumno->id)
                ->where('materia_id', $materia->id)
                ->where('user_id', $userId)
                ->get();

            $presentes = $asistencias->whereIn('estado', ['presente', 'tarde'])->count();
            $ausentes = $asistencias->where('estado', 'ausente')->count();
            $justificados = $asistencias->where('estado', 'justificado')->count();
            $totalAsignadas = $asignaciones->count();

            $notas = ActividadNota::where('alumno_id', $alumno->id)
                ->whereIn('asignacion_id', $asignaciones)
                ->where('user_id', $userId)
                ->get();

            $entregadas = $notas->where('estado', 'entregado')->count();
            $vencidas = $notas->where('estado', 'vencido')->count();

            $ultimoCierre = CierreNota::where('alumno_id', $alumno->id)
                ->where('materia_id', $materia->id)
                ->where('user_id', $userId)
                ->orderBy('fecharegistro', 'desc')
                ->first();

            return [
                'id' => $alumno->id,
                'nombre' => $alumno->nombre_completo,
                'presentes' => $presentes,
                'ausentes' => $ausentes,
                'justificados' => $justificados,
                'asignadas' => $totalAsignadas,
                'entregadas' => $entregadas,
                'vencidas' => $vencidas,
                'ultimaValoracion' => $ultimoCierre?->notavalorativa ?? '-',
                'ultimaNota' => $ultimoCierre?->notanumerica ?? null,
            ];
        });

        return response()->json(['alumnos' => $stats]);
    }
}