<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('declaracionitems', function (Blueprint $table) {
            // Quitar actividad
            if (Schema::hasColumn('declaracionitems', 'actividad')) {
                $table->dropColumn('actividad');
            }
            // Agregar establecimiento
            $table->foreignId('establecimiento_id')->nullable()->after('declaracion_id')
                  ->constrained('establecimientos')->onDelete('set null');
        });

        // Agregar fecha a declaraciones (fecha del documento)
        Schema::table('declaraciones', function (Blueprint $table) {
            $table->date('fechadeclaracion')->nullable()->after('ciclo');
        });
    }

    public function down(): void
    {
        Schema::table('declaracionitems', function (Blueprint $table) {
            $table->dropForeign(['establecimiento_id']);
            $table->dropColumn('establecimiento_id');
            $table->string('actividad')->nullable();
        });
        Schema::table('declaraciones', function (Blueprint $table) {
            $table->dropColumn('fechadeclaracion');
        });
    }
};
