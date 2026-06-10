<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('distrito');
            $table->string('tipoestablecimiento');
            $table->string('numeroescuela');
            $table->string('nombreestablecimiento');
            $table->string('secuencia')->nullable();
            $table->enum('dependencia_tipo', ['oficial', 'dipregep'])->default('oficial');
            $table->string('regimenstatutario');
            $table->string('caracterderevista');
            $table->enum('tipohora', ['modulos', 'horas'])->default('modulos');
            $table->string('cupof')->nullable();
            $table->string('dependencia')->nullable();
            $table->string('turnodesempeno');
            $table->date('fechadesde')->nullable();
            $table->date('fechahasta')->nullable();
            $table->string('anodesignado');
            $table->string('divisiondesignada');
            $table->date('fechadesignacion')->nullable();
            $table->date('fechatomaposecion')->nullable();
            $table->string('nombremateria');
            $table->time('horaentrada');
            $table->time('horasalida');
            $table->string('diasemana');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designaciones');
    }
};