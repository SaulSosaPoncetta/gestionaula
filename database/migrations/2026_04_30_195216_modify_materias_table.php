<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropForeign(['curso_id']);
            $table->dropColumn('curso_id');

            $table->foreignId('ciclo_id')->nullable()->after('nombre')
                  ->constrained('ciclos')->onDelete('set null');
            $table->foreignId('area_formacion_id')->nullable()->after('ciclo_id')
                  ->constrained('areasformacion')->onDelete('set null');
            $table->foreignId('especialidad_id')->nullable()->after('area_formacion_id')
                  ->constrained('especialidades')->onDelete('set null');
            $table->foreignId('establecimiento_id')->nullable()->after('especialidad_id')
                  ->constrained('establecimientos')->onDelete('set null');
            $table->string('anio')->nullable()->after('establecimiento_id');
            $table->enum('tipomateria', ['general', 'tecnicoespecifica', 'cientificotecnologica'])->nullable()->after('anio');
            $table->enum('tipohora', ['catedra', 'modulo'])->nullable()->after('tipomateria');
            $table->integer('cargahorariasemanal')->nullable()->after('tipohora');
            $table->integer('cargahorariaanual')->nullable()->after('cargahorariasemanal');
            $table->integer('cantidadhoras')->nullable()->after('cargahorariaanual');
        });
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropForeign(['ciclo_id']);
            $table->dropForeign(['area_formacion_id']);
            $table->dropForeign(['especialidad_id']);
            $table->dropForeign(['establecimiento_id']);
            $table->dropColumn([
                'ciclo_id', 'area_formacion_id', 'especialidad_id',
                'establecimiento_id', 'anio', 'tipomateria', 'tipohora',
                'cargahorariasemanal', 'cargahorariaanual', 'cantidadhoras'
            ]);
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
        });
    }
};