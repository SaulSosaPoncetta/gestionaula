<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('designaciones', function (Blueprint $table) {
            $table->string('diasemana')->nullable()->change();
            $table->time('horaentrada')->nullable()->change();
            $table->time('horasalida')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('designaciones', function (Blueprint $table) {
            $table->string('diasemana')->nullable(false)->change();
            $table->time('horaentrada')->nullable(false)->change();
            $table->time('horasalida')->nullable(false)->change();
        });
    }
};
