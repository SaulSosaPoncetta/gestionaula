<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modalidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre', 150);
            $table->string('slug', 80)->nullable();
            $table->timestamps();

            $table->unique(['slug', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modalidades');
    }
};
