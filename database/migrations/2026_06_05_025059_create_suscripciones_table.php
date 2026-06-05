<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('planes')->onDelete('set null');
            $table->decimal('montomensual', 10, 2)->default(0);
            $table->enum('estado', ['activa', 'suspendida', 'cancelada'])->default('activa');
            $table->date('fechainicio');
            $table->date('fechavencimiento')->nullable();
            $table->date('proximopago')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('suscripciones'); }
};
