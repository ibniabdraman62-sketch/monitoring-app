<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerte_lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alerte_id')->constrained('alertes')->cascadeOnDelete();
            $table->timestamp('lu_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'alerte_id']);
            $table->index(['user_id', 'lu_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerte_lectures');
    }
};