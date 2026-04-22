<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('resolved_at')->nullable();
            $table->enum('type', ['offline', 'slow', 'ssl'])->default('offline');
            $table->integer('duration_min')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('incidents');
    }
};