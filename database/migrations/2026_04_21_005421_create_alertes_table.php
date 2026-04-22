<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->timestamp('sent_at');
            $table->enum('type', ['down', 'slow', 'ssl', 'resolved']);
            $table->string('email_to');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('alertes');
    }
};