<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ciclo');
            $table->enum('estado', ['borrador', 'presentada', 'aprobada', 'rechazada'])->default('borrador');
            $table->text('observacion')->nullable();
            $table->timestamp('fechapresentacion')->nullable();
            $table->timestamp('fecharesolucion')->nullable();
            $table->foreignId('resueltopor')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaraciones');
    }
};
