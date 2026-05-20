<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierrenotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->string('tipocierre'); // prenota, nota final, etc
            $table->decimal('notanumerica', 4, 2);
            $table->string('notavalorativa')->nullable();
            $table->decimal('promedioactividades', 4, 2)->nullable();
            $table->decimal('promediocalificaciones', 4, 2)->nullable();
            $table->decimal('notaasistencia', 4, 2)->nullable();
            $table->decimal('porcentajeasistencia', 5, 2)->nullable();
            $table->date('fecharegistro');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierrenotas');
    }
};