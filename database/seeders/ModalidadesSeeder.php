<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder de Modalidades Educativas — Provincia de Buenos Aires
 * Basado en Ley de Educación Provincial N° 13688 — Art. 21
 *
 * user_id = null → registros globales disponibles para todos los usuarios
 */
class ModalidadesSeeder extends Seeder
{
    public function run(): void
    {
        $modalidades = [
            ['nombre' => 'Educación Técnico-Profesional',                                        'slug' => 'tecnico-profesional'],
            ['nombre' => 'Educación Artística',                                                  'slug' => 'artistica'],
            ['nombre' => 'Educación Especial',                                                   'slug' => 'especial'],
            ['nombre' => 'Educación Permanente de Jóvenes, Adultos y Formación Profesional',     'slug' => 'adultos'],
            ['nombre' => 'Educación Intercultural',                                              'slug' => 'intercultural'],
            ['nombre' => 'Educación Física',                                                     'slug' => 'fisica'],
            ['nombre' => 'Educación Ambiental',                                                  'slug' => 'ambiental'],
            ['nombre' => 'Psicología Comunitaria y Pedagogía Social',                            'slug' => 'psicologia-comunitaria'],
        ];

        foreach ($modalidades as $m) {
            DB::table('modalidades')->updateOrInsert(
                ['slug' => $m['slug'], 'user_id' => null],
                [
                    'nombre'     => $m['nombre'],
                    'slug'       => $m['slug'],
                    'user_id'    => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✔  ModalidadesSeeder: 8 modalidades (Ley 13688 PBA) insertadas/actualizadas.');
    }
}
