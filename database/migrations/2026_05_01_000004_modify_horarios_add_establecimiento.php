<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->foreignId('establecimiento_id')->nullable()->after('user_id')
                  ->constrained('establecimientos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropForeign(['establecimiento_id']);
            $table->dropColumn('establecimiento_id');
        });
    }
};
