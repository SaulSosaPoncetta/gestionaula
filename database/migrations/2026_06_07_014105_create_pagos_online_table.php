<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_online', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('suscripcion_id')->nullable()->constrained('suscripciones')->onDelete('set null');
            $table->string('plataforma'); // mercadopago, paypal
            $table->string('external_id')->nullable(); // ID de la plataforma
            $table->string('preference_id')->nullable(); // MP preference_id
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 3)->default('ARS');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'cancelado', 'reembolsado'])->default('pendiente');
            $table->string('metodo_pago')->nullable();
            $table->json('datos_extra')->nullable();
            $table->date('periododesde')->nullable();
            $table->date('periodohasta')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_online');
    }
};