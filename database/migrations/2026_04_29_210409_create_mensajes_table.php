<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('destinatario');
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->onDelete('set null');
            $table->foreignId('alumno_id')->nullable()->constrained('alumnos')->onDelete('set null');
            $table->string('asunto');
            $table->text('cuerpo');
            $table->enum('tipo', ['general', 'curso', 'alumno'])->default('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};