<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenidosubtemas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contenido_id')->constrained('contenidos')->onDelete('cascade');
            $table->string('subtema');
            $table->unsignedTinyInteger('orden')->default(1); // 1, 2 o 3
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contenidosubtemas');
    }
};