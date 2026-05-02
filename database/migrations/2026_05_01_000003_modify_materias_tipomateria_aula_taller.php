<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            // Quitar campo cantidadhoras
            if (Schema::hasColumn('materias', 'cantidadhoras')) {
                $table->dropColumn('cantidadhoras');
            }
            // Cambiar tipomateria a aula/taller (se hace dropping y recreando)
            if (Schema::hasColumn('materias', 'tipomateria')) {
                $table->dropColumn('tipomateria');
            }
        });

        Schema::table('materias', function (Blueprint $table) {
            $table->enum('tipomateria', ['aula', 'taller'])->nullable()->after('anio');
        });
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropColumn('tipomateria');
        });
        Schema::table('materias', function (Blueprint $table) {
            $table->enum('tipomateria', ['general', 'tecnicoespecifica', 'cientificotecnologica'])->nullable();
            $table->integer('cantidadhoras')->nullable();
        });
    }
};
