<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('designaciones', function (Blueprint $table) {
            $table->string('ige')->nullable()->after('cupof');
            $table->string('cantmodulos')->nullable()->after('ige');
            $table->enum('tipohorario', ['unificado', 'dividido'])
                   ->default('unificado')
                   ->after('cantmodulos');
        });

        // En modo "dividido" el horario se carga por dia en designacion_horarios,
        // por lo que estos campos a nivel cabecera deben poder quedar vacios.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE designaciones MODIFY diasemana VARCHAR(20) NULL");
            DB::statement("ALTER TABLE designaciones MODIFY horaentrada TIME NULL");
            DB::statement("ALTER TABLE designaciones MODIFY horasalida TIME NULL");
        }
    }

    public function down(): void
    {
        Schema::table('designaciones', function (Blueprint $table) {
            $table->dropColumn(['ige', 'cantmodulos', 'tipohorario']);
        });
    }
};
