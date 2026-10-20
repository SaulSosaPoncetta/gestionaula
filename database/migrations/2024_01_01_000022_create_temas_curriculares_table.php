<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temas_curriculares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nucleo_curricular_id')->constrained('nucleos_curriculares')->cascadeOnDelete();
            $table->string('nombre', 500);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temas_curriculares');
    }
};
