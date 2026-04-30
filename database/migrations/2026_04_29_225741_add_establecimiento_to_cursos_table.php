<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->foreignId('establecimiento_id')->nullable()->after('nivel')
                  ->constrained('establecimientos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Establecimiento::class);
            $table->dropColumn('establecimiento_id');
        });
    }
};