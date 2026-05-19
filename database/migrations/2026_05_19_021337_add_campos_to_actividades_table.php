<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->integer('numeroactividad')->nullable()->after('numerounidad');
            // Hacer nullable los campos que sacamos del formulario
            $table->date('fechainicio')->nullable()->change();
            $table->date('fechaentrega')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn('numeroactividad');
        });
    }
};