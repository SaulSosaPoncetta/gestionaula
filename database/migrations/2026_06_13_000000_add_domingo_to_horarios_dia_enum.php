<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE horarios MODIFY dia ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE horarios MODIFY dia ENUM('lunes','martes','miercoles','jueves','viernes','sabado') NOT NULL");
        }
    }
};
