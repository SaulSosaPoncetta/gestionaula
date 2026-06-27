<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropForeign(['establecimiento_id']);
            $table->dropColumn('establecimiento_id');
        });
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->foreignId('establecimiento_id')
                  ->nullable()
                  ->constrained('establecimientos')
                  ->onDelete('set null');
        });
    }
};
