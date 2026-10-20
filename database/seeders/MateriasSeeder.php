<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriasSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('materias')->whereNull('user_id')->exists()) {
            $this->command->info('MateriasSeeder: ya existen materias globales, saltando.');
            return;
        }

        $now = now();

        // ── CICLO BÁSICO (1° a 3° año) ────────────────────────
        $basicas = [
            // Área: Lengua y Literatura
            ['nombre' => 'Lengua y Literatura',            'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            ['nombre' => 'Lengua y Literatura',            'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            ['nombre' => 'Lengua y Literatura',            'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            // Área: Matemática
            ['nombre' => 'Matemática',                     'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            ['nombre' => 'Matemática',                     'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            ['nombre' => 'Matemática',                     'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            // Área: Ciencias Naturales
            ['nombre' => 'Ciencias Naturales',             'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Biología',                       'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Física',                         'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Química',                        'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            // Área: Ciencias Sociales
            ['nombre' => 'Ciencias Sociales',              'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Historia',                       'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Geografía',                      'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Historia',                       'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Geografía',                      'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            // Área: Lenguas Extranjeras
            ['nombre' => 'Inglés',                         'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés',                         'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés',                         'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            // Arte y Tecnología
            ['nombre' => 'Educación Artística',            'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Educación Artística',            'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Educación Tecnológica',          'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Tecnológica',          'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Tecnológica',          'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            // Ed. Física
            ['nombre' => 'Educación Física',               'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            // Construcción de Ciudadanía
            ['nombre' => 'Construcción de Ciudadanía',     'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Construcción de Ciudadanía',     'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Construcción de Ciudadanía',     'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
        ];

        // ── CICLO SUPERIOR COMÚN (4° a 6°) ───────────────────
        $superior_comun = [
            ['nombre' => 'Literatura',                     'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Comunicación, Arte y Expresión', 'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Matemática',                     'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Matemática',                     'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Matemática',                     'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Biología',                       'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Física',                         'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Química',                        'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Historia',                       'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Historia',                       'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Historia',                       'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Geografía',                      'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Geografía',                      'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés',                         'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés',                         'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés',                         'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Psicología',                     'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Filosofía',                      'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Economía y Administración',      'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Economía y Administración',      'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Política y Ciudadanía',          'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Salud y Adolescencia',           'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Arte',                           'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Arte',                           'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
        ];

        // ── TÉCNICA / INDUSTRIAL ──────────────────────────────
        $tecnica = [
            ['nombre' => 'Dibujo Técnico',                 'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4, 'tipomateria' => 'taller'],
            ['nombre' => 'Dibujo Técnico',                 'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4, 'tipomateria' => 'taller'],
            ['nombre' => 'Tecnología de la Información',   'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Electrónica',                    'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Electrónica',                    'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Electrónica',                    'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Mecánica',                       'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Mecánica',                       'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Mecánica',                       'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Informática',                    'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Informática',                    'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Informática',                    'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Construcciones',                 'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
            ['nombre' => 'Laboratorio de Química',         'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4, 'tipomateria' => 'taller'],
            ['nombre' => 'Proyecto Tecnológico',           'anio' => '7°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 8, 'tipomateria' => 'taller'],
        ];

        // ── ADULTOS (CENS) ────────────────────────────────────
        $adultos = [
            ['nombre' => 'Lengua y Literatura (Adultos)',  'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Matemática (Adultos)',           'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Ciencias Sociales (Adultos)',    'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Naturales (Adultos)',   'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés (Adultos)',               'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Formación para el Trabajo',      'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Proyecto y Metodología',         'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
        ];

        // ── ARTÍSTICA ─────────────────────────────────────────
        $artistica = [
            ['nombre' => 'Lenguaje Visual',                'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Lenguaje Musical',               'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Lenguaje Corporal',              'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Lenguaje Teatral',               'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 4],
            ['nombre' => 'Historia del Arte',              'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Apreciación y Crítica de Arte',  'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Producción Artística',           'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6, 'tipomateria' => 'taller'],
        ];

        // ── PRIMARIA ──────────────────────────────────────────
        $primaria = [
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 8],
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 8],
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 7],
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 7],
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6],
            ['nombre' => 'Prácticas del Lenguaje',         'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6],
            ['nombre' => 'Matemática',                     'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 7],
            ['nombre' => 'Matemática',                     'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 7],
            ['nombre' => 'Matemática',                     'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6],
            ['nombre' => 'Matemática',                     'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 6],
            ['nombre' => 'Matemática',                     'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            ['nombre' => 'Matemática',                     'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 5],
            ['nombre' => 'Ciencias Sociales',              'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Sociales',              'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Sociales',              'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Sociales',              'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Naturales',             'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Naturales',             'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Naturales',             'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Ciencias Naturales',             'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Inglés',                         'anio' => '4°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Inglés',                         'anio' => '5°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Inglés',                         'anio' => '6°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Educación Física',               'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Física',               'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 3],
            ['nombre' => 'Educación Artística',            'anio' => '1°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Educación Artística',            'anio' => '2°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
            ['nombre' => 'Educación Artística',            'anio' => '3°', 'tipohora' => 'modulo', 'cargahorariasemanal' => 2],
        ];

        // Combinar todos los grupos
        $todos = array_merge($basicas, $superior_comun, $tecnica, $adultos, $artistica, $primaria);

        $data = [];
        foreach ($todos as $m) {
            $hs     = $m['cargahorariasemanal'];
            $anual  = $hs * 40; // aprox 40 semanas lectivas
            $data[] = [
                'user_id'              => null,
                'nombre'               => $m['nombre'],
                'anio'                 => $m['anio'] ?? null,
                'tipomateria'          => $m['tipomateria'] ?? 'aula',
                'tipohora'             => $m['tipohora'] ?? 'modulo',
                'cargahorariasemanal'  => $hs,
                'cargahorariaanual'    => $anual,
                'hsporclase'           => $m['tipohora'] === 'modulo' ? 2 : 1,
                'cantidadclasesanuales'=> $anual,
                'porcentajelimite'     => 75.00,
                'ciclo_id'             => null,
                'area_formacion_id'    => null,
                'especialidad_id'      => null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        foreach (array_chunk($data, 50) as $chunk) {
            DB::table('materias')->insert($chunk);
        }

        $this->command->info('MateriasSeeder: ' . count($data) . ' materias insertadas.');
    }
}
