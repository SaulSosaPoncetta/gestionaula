<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaracionitems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaracion_id')->constrained('declaraciones')->onDelete('cascade');
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->onDelete('set null');
            $table->foreignId('materia_id')->nullable()->constrained('materias')->onDelete('set null');
            $table->enum('dia', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']);
            $table->time('horainicio');
            $table->time('horafin');
            $table->string('actividad')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaracionitems');
    }
};
