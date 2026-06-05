<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carpetacampoentradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carpeta_id')->constrained('carpetacampo')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['nota', 'documento', 'imagen', 'actividad', 'seguimiento'])->default('nota');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo')->nullable();
            $table->date('fecha');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carpetacampoentradas');
    }
};