<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('librotemas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('tipoclase_id')->nullable()->constrained('tiposclase')->onDelete('set null');
            $table->foreignId('contenido_id')->nullable()->constrained('contenidos')->onDelete('set null');
            $table->foreignId('actividad_id')->nullable()->constrained('actividades')->onDelete('set null');
            $table->date('fecha');
            $table->integer('numeroclase');
            $table->integer('numerounidad')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('librotemas');
    }
};