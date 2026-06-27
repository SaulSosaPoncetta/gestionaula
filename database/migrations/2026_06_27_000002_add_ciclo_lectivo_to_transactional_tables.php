<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tablas a las que se agrega la relación con ciclo lectivo
    private array $tablas = [
        'asistencias',
        'calificaciones',
        'actividades',
        'contenidos',
        'cierrenotas',
        'declaraciones',
        'horarios',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla) && !Schema::hasColumn($tabla, 'ciclo_lectivo_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->foreignId('ciclo_lectivo_id')
                          ->nullable()
                          ->after('user_id')
                          ->constrained('ciclos_lectivos')
                          ->onDelete('set null');
                });
            }
        }

        // Librotemas si existe
        if (Schema::hasTable('librotemas') && !Schema::hasColumn('librotemas', 'ciclo_lectivo_id')) {
            Schema::table('librotemas', function (Blueprint $table) {
                $table->foreignId('ciclo_lectivo_id')
                      ->nullable()
                      ->after('user_id')
                      ->constrained('ciclos_lectivos')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        foreach (array_merge($this->tablas, ['librotemas']) as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'ciclo_lectivo_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropForeign(['ciclo_lectivo_id']);
                    $table->dropColumn('ciclo_lectivo_id');
                });
            }
        }
    }
};
