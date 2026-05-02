<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materialteoricoarchivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tarea_id')->nullable()->constrained('tareas')->onDelete('cascade');
            $table->string('titulo');
            $table->string('descripcion')->nullable();
            $table->string('ruta');
            $table->integer('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materialteoricoarchivos');
    }
};