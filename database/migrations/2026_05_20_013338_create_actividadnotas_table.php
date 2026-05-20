<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividadnotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asignacion_id')->constrained('actividadasignaciones')->onDelete('cascade');
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->decimal('notaindividual', 4, 2)->nullable();
            $table->decimal('notagrupal', 4, 2)->nullable();
            $table->enum('estado', ['pendiente', 'enproceso', 'entregado', 'vencido'])->default('pendiente');
            $table->date('fechaestado')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividadnotas');
    }
};