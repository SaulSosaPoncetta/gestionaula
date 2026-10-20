<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nucleos_curriculares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eje_curricular_id')->constrained('ejes_curriculares')->cascadeOnDelete();
            $table->string('nombre', 300);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nucleos_curriculares');
    }
};
