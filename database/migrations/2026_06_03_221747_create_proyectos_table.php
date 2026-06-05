<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('establecimiento_id')->nullable()->constrained('establecimientos')->onDelete('set null');
            $table->foreignId('actividad_id')->nullable()->constrained('actividades')->onDelete('set null');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->date('fechapresentacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['borrador', 'activo', 'presentado', 'cerrado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};