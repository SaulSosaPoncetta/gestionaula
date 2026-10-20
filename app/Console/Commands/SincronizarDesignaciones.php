<?php

namespace App\Console\Commands;

use App\Models\Curso;
use App\Models\Designacion;
use App\Models\Establecimiento;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Console\Command;

class SincronizarDesignaciones extends Command
{
    protected $signature = 'gestionaula:sincronizar-designaciones
                            {--user= : ID de usuario específico}';

    protected $description = 'Crea Materias y Cursos a partir de las Designaciones existentes';

    public function handle(): int
    {
        $query = Designacion::query();

        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
            $this->info("Procesando designaciones del usuario ID {$userId}...");
        } else {
            $this->info('Procesando designaciones de todos los usuarios...');
        }

        $designaciones = $query->get();
        $this->info("Total designaciones: {$designaciones->count()}");

        $cMateria = 0;
        $cCurso   = 0;

        foreach ($designaciones as $d) {
            // ── Materia ──────────────────────────────────────────────────────
            if ($d->nombremateria) {
                $created = Materia::firstOrCreate(
                    [
                        'user_id' => $d->user_id,
                        'nombre'  => trim($d->nombremateria),
                    ],
                    [
                        'tipomateria' => 'aula',
                        'tipohora'    => 'catedra',
                    ]
                );
                if ($created->wasRecentlyCreated) {
                    $cMateria++;
                    $this->line("  + Materia: {$created->nombre}");
                }
            }

            // ── Curso ─────────────────────────────────────────────────────────
            $anio     = $d->anodesignado     ?? null;
            $division = $d->divisiondesignada ?? null;

            if ($anio || $division) {
                // Buscar establecimiento global por nombre similar
                $establecimientoId = null;
                if ($d->nombreestablecimiento) {
                    $est = Establecimiento::where(function ($q) use ($d) {
                        $q->whereNull('user_id')
                          ->orWhere('user_id', $d->user_id);
                    })
                    ->where('nombre', 'like', '%' . substr($d->nombreestablecimiento, 0, 30) . '%')
                    ->first();
                    $establecimientoId = $est?->id;
                }

                $created = Curso::firstOrCreate(
                    [
                        'user_id'  => $d->user_id,
                        'anio'     => $anio,
                        'division' => $division,
                    ],
                    [
                        'turno'             => $d->turnodesempeno ?? null,
                        'establecimiento_id'=> $establecimientoId,
                    ]
                );
                if ($created->wasRecentlyCreated) {
                    $cCurso++;
                    $this->line("  + Curso: {$anio}° {$division}");
                }
            }
        }

        $this->info("Materias creadas: {$cMateria}");
        $this->info("Cursos creados:   {$cCurso}");
        $this->info('Listo.');

        return self::SUCCESS;
    }
}
