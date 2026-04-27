<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('client_email')->nullable()->after('client_name');
            $table->boolean('whois_check')->default(true)->after('ssl_check');
        });
    }
    public function down(): void {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['client_email', 'whois_check']);
        });
    }
};