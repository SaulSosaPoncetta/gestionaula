<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Asistencias
        if (Schema::hasTable('asistencias') && !Schema::hasColumn('asistencias', 'uuid')) {
            Schema::table('asistencias', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        // Calificaciones
        if (Schema::hasTable('calificaciones') && !Schema::hasColumn('calificaciones', 'uuid')) {
            Schema::table('calificaciones', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        // Libro de temas
        if (Schema::hasTable('librotemas') && !Schema::hasColumn('librotemas', 'uuid')) {
            Schema::table('librotemas', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        // Actividades
        if (Schema::hasTable('actividades') && !Schema::hasColumn('actividades', 'uuid')) {
            Schema::table('actividades', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        // Cola de sincronización (registra qué se sincronizó y cuándo)
        if (!Schema::hasTable('sync_log')) {
            Schema::create('sync_log', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('uuid', 36)->unique();
                $table->string('tabla', 50);
                $table->string('operacion', 20); // insert, update, delete
                $table->timestamp('sincronizado_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        foreach (['asistencias','calificaciones','librotemas','actividades'] as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'uuid')) {
                Schema::table($tabla, fn($t) => $t->dropColumn('uuid'));
            }
        }
        Schema::dropIfExists('sync_log');
    }
};
