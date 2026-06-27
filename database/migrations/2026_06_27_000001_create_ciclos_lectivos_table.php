<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos_lectivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('anio', 10);
            $table->date('fechainicio');
            $table->date('fechafin');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_lectivos');
    }
};
