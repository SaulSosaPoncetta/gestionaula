<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cue')->nullable()->unique();
            $table->enum('modalidad', ['comun', 'tecnico']);
            $table->foreignId('nivel_id')->constrained('niveles')->onDelete('cascade');
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};