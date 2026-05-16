<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ceses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('establecimiento_id')->constrained('establecimientos')->onDelete('cascade');
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->onDelete('set null');
            $table->date('fechatomapossesion');
            $table->date('fechacese');
            $table->string('numerosecuencia')->nullable();
            $table->string('dia')->nullable();
            $table->string('horainicio')->nullable();
            $table->string('horafin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ceses');
    }
};