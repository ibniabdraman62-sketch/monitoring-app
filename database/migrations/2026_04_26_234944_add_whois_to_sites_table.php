<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('domain_registrar')->nullable()->after('ssl_check');
            $table->date('domain_expires_at')->nullable()->after('domain_registrar');
            $table->date('domain_created_at')->nullable()->after('domain_expires_at');
            $table->timestamp('whois_checked_at')->nullable()->after('domain_created_at');
            $table->string('notify_emails')->nullable()->after('whois_checked_at');
        });
    }
    public function down(): void {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'domain_registrar',
                'domain_expires_at',
                'domain_created_at',
                'whois_checked_at',
                'notify_emails',
            ]);
        });
    }
};