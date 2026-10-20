<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder de Niveles Educativos — Provincia de Buenos Aires
 * Basado en Ley de Educación Provincial N° 13688 — Art. 24
 *
 * user_id = null → registros globales disponibles para todos los usuarios
 */
class NivelesSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = [
            ['nombre' => 'Educación Inicial',    'tipo' => 'inicial',     'user_id' => null],
            ['nombre' => 'Educación Primaria',   'tipo' => 'primario',    'user_id' => null],
            ['nombre' => 'Educación Secundaria', 'tipo' => 'secundario',  'user_id' => null],
            ['nombre' => 'Educación Superior',   'tipo' => 'terciario',   'user_id' => null],
        ];

        foreach ($niveles as $nivel) {
            DB::table('niveles')->updateOrInsert(
                ['tipo' => $nivel['tipo'], 'user_id' => null],
                array_merge($nivel, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✔  NivelesSeeder: 4 niveles (Ley 13688 PBA) insertados/actualizados.');
    }
}
