<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn(['dni', 'telefono', 'email']);
            $table->string('codigo', 8)->unique()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('codigo');
            $table->string('dni')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
        });
    }
};