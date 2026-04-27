<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->enum('status', ['success', 'error'])->default('success');
            $table->integer('duration_ms')->default(0);
            $table->integer('sites_checked')->default(0);
            $table->integer('errors_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('cron_logs');
    }
};