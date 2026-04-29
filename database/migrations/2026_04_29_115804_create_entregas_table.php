<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'entregado', 'aprobado', 'desaprobado'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->date('fechaentrega')->nullable();
            $table->timestamps();

            $table->unique(['tarea_id', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};