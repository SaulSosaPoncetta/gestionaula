<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (Schema::hasColumn('cursos', 'nivel')) {
                $table->dropColumn('nivel');
            }
            $table->string('anio')->nullable()->after('nombre');
            $table->foreignId('nivel_id')->nullable()->after('anio')
                  ->constrained('niveles')->onDelete('set null');
            $table->foreignId('especialidad_id')->nullable()->after('nivel_id')
                  ->constrained('especialidades')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeign(['nivel_id']);
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn(['anio', 'nivel_id', 'especialidad_id']);
            $table->string('nivel')->nullable();
        });
    }
};