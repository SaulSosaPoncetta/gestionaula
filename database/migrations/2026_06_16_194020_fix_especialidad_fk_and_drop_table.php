<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Limpiar especialidad_id inválidos en materias y cursos
        //    (los que apuntan a IDs que no existen en especialidades)
        DB::statement("
            UPDATE materias
            SET especialidad_id = NULL
            WHERE especialidad_id IS NOT NULL
            AND especialidad_id NOT IN (SELECT id FROM especialidades)
        ");

        DB::statement("
            UPDATE cursos
            SET especialidad_id = NULL
            WHERE especialidad_id IS NOT NULL
            AND especialidad_id NOT IN (SELECT id FROM especialidades)
        ");

        // 2. Eliminar FK incorrecta de materias
        Schema::table('materias', function (Blueprint $table) {
            $table->dropForeign(['especialidad_id']);
        });

        // 3. Eliminar FK incorrecta de cursos si existe
        try {
            Schema::table('cursos', function (Blueprint $table) {
                $table->dropForeign(['especialidad_id']);
            });
        } catch (\Throwable $e) {}

        // 4. Eliminar tabla basura
        Schema::dropIfExists('especialidads');

        // 5. Recrear FK apuntando a la tabla correcta en materias
        Schema::table('materias', function (Blueprint $table) {
            $table->foreign('especialidad_id')
                  ->references('id')
                  ->on('especialidades')
                  ->onDelete('set null');
        });

        // 6. Recrear FK en cursos
        Schema::table('cursos', function (Blueprint $table) {
            $table->foreign('especialidad_id')
                  ->references('id')
                  ->on('especialidades')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // No revertimos: la tabla especialidads no debería volver a existir
    }
};
