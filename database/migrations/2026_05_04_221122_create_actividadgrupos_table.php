<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividadgrupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->string('nombre'); // Grupo 1, Grupo 2, etc.
            $table->integer('numero');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividadgrupos');
    }
};