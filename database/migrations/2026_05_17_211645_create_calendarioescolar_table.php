<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendarioescolar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('periodo_id')->nullable()->constrained('periodos')->onDelete('set null');
            $table->date('fecha');
            $table->string('denominacion');
            $table->boolean('esferiado')->default(false);
            $table->date('fechainicio')->nullable();
            $table->date('fechafin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendarioescolar');
    }
};