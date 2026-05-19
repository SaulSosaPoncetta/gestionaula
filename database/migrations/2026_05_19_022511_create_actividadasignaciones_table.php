<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividadasignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('fechainicio');
            $table->date('fechaentrega');
            $table->boolean('esgrupal')->default(false);
            $table->integer('integrantesporgrupo')->nullable();
            $table->enum('modogrupo', ['aleatorio', 'manual'])->nullable();
            $table->enum('estado', ['activa', 'cerrada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividadasignaciones');
    }
};