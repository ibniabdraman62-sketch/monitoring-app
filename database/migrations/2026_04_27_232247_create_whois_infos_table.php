<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('whois_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('registrar')->nullable();
            $table->date('registered_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('domain_days_remaining')->nullable();
            $table->timestamp('checked_at')->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('whois_infos');
    }
};