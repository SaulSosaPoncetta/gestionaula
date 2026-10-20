<?php

namespace App\Console\Commands;

use App\Models\Designacion;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\Establecimiento;
use Illuminate\Console\Command;

class SincronizarDesignaciones extends Command
{
    protected $signature   = 'gestionaula:sincronizar-designaciones {--user= : ID de usuario específico}';
    protected $description = 'Crea materias y cursos faltantes a partir de las designaciones existentes';

    public function handle(): int
    {
        $query = Designacion::query();
        if ($this->option('user')) {
            $query->where('user_id', $this->option('user'));
        }

        $designaciones = $query->get();
        $this->info("Procesando {$designaciones->count()} designaciones...");

        $materias = 0;
        $cursos   = 0;

        foreach ($designaciones as $d) {
            $userId = $d->user_id;

            // Crear Materia
            $created = Materia::firstOrCreate(
                ['user_id' => $userId, 'nombre' => trim($d->nombremateria)],
                ['user_id' => $userId, 'nombre' => trim($d->nombremateria)]
            );
            if ($created->wasRecentlyCreated) $materias++;

            // Crear Curso
            if ($d->anodesignado || $d->divisiondesignada) {
                $establecimiento = null;
                if ($d->nombreestablecimiento) {
                    $establecimiento = Establecimiento::where(function($q) use ($userId) {
                        $q->where('user_id', $userId)->orWhereNull('user_id');
                    })->where('nombre', 'like', '%' . substr($d->nombreestablecimiento, 0, 30) . '%')->first();
                }

                $curso = Curso::firstOrCreate(
                    ['user_id' => $userId, 'anio' => $d->anodesignado ?? '', 'division' => $d->divisiondesignada ?? ''],
                    [
                        'user_id'            => $userId,
                        'nombre'             => trim(($d->anodesignado ?? '') . ' ' . ($d->divisiondesignada ?? '')),
                        'anio'               => $d->anodesignado ?? '',
                        'division'           => $d->divisiondesignada ?? '',
                        'turno'              => $d->turnodesempeno ?? null,
                        'establecimiento_id' => $establecimiento?->id,
                    ]
                );
                if ($curso->wasRecentlyCreated) $cursos++;
            }
        }

        $this->info("✅ Creadas: {$materias} materias, {$cursos} cursos.");
        return self::SUCCESS;
    }
}
