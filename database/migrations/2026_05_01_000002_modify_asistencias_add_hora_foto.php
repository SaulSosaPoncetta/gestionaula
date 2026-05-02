<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Hora de llegada (se registra automáticamente si el estado es tarde)
            $table->time('horallegada')->nullable()->after('estado');
            // Ruta de foto del documento justificante
            $table->string('fotojustificacion')->nullable()->after('horallegada');
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropColumn(['horallegada', 'fotojustificacion']);
        });
    }
};
