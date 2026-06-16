<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designacion_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designacion_id')->constrained('designaciones')->onDelete('cascade');
            $table->string('dia');
            $table->string('cantmodulos')->nullable();
            $table->time('horaentrada');
            $table->time('horasalida');
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designacion_horarios');
    }
};
