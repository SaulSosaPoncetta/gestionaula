<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\LibroTema;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Recibe un lote de operaciones pendientes desde la PWA.
     * Cada operación tiene un UUID único — si ya fue procesada, retorna OK sin duplicar.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'operaciones'   => 'required|array|max:100',
            'operaciones.*.uuid'   => 'required|uuid',
            'operaciones.*.tabla'  => 'required|string|in:asistencias,calificaciones,librotemas,actividades',
            'operaciones.*.accion' => 'required|string|in:insert,update,delete',
            'operaciones.*.datos'  => 'required|array',
        ]);

        $resultados = [];
        $userId     = auth()->id();

        foreach ($request->operaciones as $op) {
            $uuid   = $op['uuid'];
            $tabla  = $op['tabla'];
            $accion = $op['accion'];
            $datos  = $op['datos'];

            try {
                // ── Idempotencia: si este UUID ya fue procesado, retornar OK ──
                $yaExiste = DB::table('sync_log')
                    ->where('uuid', $uuid)
                    ->where('user_id', $userId)
                    ->exists();

                if ($yaExiste) {
                    $resultados[] = ['uuid' => $uuid, 'estado' => 'ya_sincronizado'];
                    continue;
                }

                DB::beginTransaction();

                $this->procesarOperacion($userId, $tabla, $accion, $datos, $uuid);

                // Registrar en sync_log para idempotencia futura
                DB::table('sync_log')->insert([
                    'user_id'         => $userId,
                    'uuid'            => $uuid,
                    'tabla'           => $tabla,
                    'operacion'       => $accion,
                    'sincronizado_at' => now(),
                ]);

                DB::commit();
                $resultados[] = ['uuid' => $uuid, 'estado' => 'ok'];

            } catch (QueryException $e) {
                DB::rollBack();
                Log::error("SyncController BD [{$tabla}@{$accion}] UUID:{$uuid} — " . $e->getMessage());
                $resultados[] = ['uuid' => $uuid, 'estado' => 'error', 'mensaje' => 'Error de base de datos'];
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("SyncController [{$tabla}@{$accion}] UUID:{$uuid} — " . $e->getMessage());
                $resultados[] = ['uuid' => $uuid, 'estado' => 'error', 'mensaje' => 'Error inesperado'];
            }
        }

        $ok      = collect($resultados)->where('estado', 'ok')->count();
        $yaSync  = collect($resultados)->where('estado', 'ya_sincronizado')->count();
        $errores = collect($resultados)->where('estado', 'error')->count();

        return response()->json([
            'sincronizados'     => $ok,
            'ya_sincronizados'  => $yaSync,
            'errores'           => $errores,
            'detalle'           => $resultados,
        ]);
    }

    private function procesarOperacion(int $userId, string $tabla, string $accion, array $datos, string $uuid): void
    {
        match ($tabla) {
            'asistencias'    => $this->syncAsistencia($userId, $accion, $datos, $uuid),
            'calificaciones' => $this->syncCalificacion($userId, $accion, $datos, $uuid),
            'librotemas'     => $this->syncLibroTema($userId, $accion, $datos, $uuid),
            'actividades'    => $this->syncActividad($userId, $accion, $datos, $uuid),
        };
    }

    // ── Asistencia ────────────────────────────────────────────────────
    private function syncAsistencia(int $userId, string $accion, array $d, string $uuid): void
    {
        if ($accion === 'delete') {
            Asistencia::where('uuid', $uuid)->where('user_id', $userId)->delete();
            return;
        }

        Asistencia::updateOrCreate(
            ['uuid' => $uuid, 'user_id' => $userId],
            [
                'alumno_id'   => $d['alumno_id'],
                'curso_id'    => $d['curso_id'],
                'materia_id'  => $d['materia_id'],
                'fecha'       => $d['fecha'],
                'estado'      => $d['estado'] ?? 'presente',
                'horallegada' => $d['horallegada'] ?? null,
                'observacion' => $d['observacion'] ?? null,
                'user_id'     => $userId,
            ]
        );
    }

    // ── Calificaciones ────────────────────────────────────────────────
    private function syncCalificacion(int $userId, string $accion, array $d, string $uuid): void
    {
        if ($accion === 'delete') {
            Calificacion::where('uuid', $uuid)->where('user_id', $userId)->delete();
            return;
        }

        Calificacion::updateOrCreate(
            ['uuid' => $uuid, 'user_id' => $userId],
            [
                'alumno_id'         => $d['alumno_id'],
                'curso_id'          => $d['curso_id'],
                'materia_id'        => $d['materia_id'],
                'nota'              => $d['nota'],
                'observacion'       => $d['observacion'] ?? null,
                'periodo_id'        => $d['periodo_id'] ?? null,
                'tipoevaluacion_id' => $d['tipoevaluacion_id'] ?? null,
                'user_id'           => $userId,
            ]
        );
    }

    // ── Libro de temas ────────────────────────────────────────────────
    private function syncLibroTema(int $userId, string $accion, array $d, string $uuid): void
    {
        if ($accion === 'delete') {
            LibroTema::where('uuid', $uuid)->where('user_id', $userId)->delete();
            return;
        }

        LibroTema::updateOrCreate(
            ['uuid' => $uuid, 'user_id' => $userId],
            [
                'curso_id'    => $d['curso_id'],
                'materia_id'  => $d['materia_id'],
                'fecha'       => $d['fecha'],
                'tema'        => $d['tema'],
                'observacion' => $d['observacion'] ?? null,
                'user_id'     => $userId,
            ]
        );
    }

    // ── Actividades ───────────────────────────────────────────────────
    private function syncActividad(int $userId, string $accion, array $d, string $uuid): void
    {
        if ($accion === 'delete') {
            \App\Models\Actividad::where('uuid', $uuid)->where('user_id', $userId)->delete();
            return;
        }

        \App\Models\Actividad::updateOrCreate(
            ['uuid' => $uuid, 'user_id' => $userId],
            [
                'materia_id'  => $d['materia_id'],
                'curso_id'    => $d['curso_id'],
                'titulo'      => $d['titulo'],
                'descripcion' => $d['descripcion'] ?? null,
                'fecha'       => $d['fecha'] ?? now()->toDateString(),
                'user_id'     => $userId,
            ]
        );
    }

    /**
     * Devuelve estado del servidor — útil para verificar conectividad
     */
    public function estado()
    {
        return response()->json([
            'user_id'    => auth()->id(),
            'server_ok'  => true,
            'timestamp'  => now()->toIso8601String(),
        ]);
    }
}
