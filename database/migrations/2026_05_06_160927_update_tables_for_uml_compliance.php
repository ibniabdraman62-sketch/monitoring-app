<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TABLE SITES
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'client_email'))
                $table->string('client_email')->nullable()->after('client_name');
            if (!Schema::hasColumn('sites', 'whois_check'))
                $table->boolean('whois_check')->default(true)->after('ssl_check');
        });

        // TABLE VERIFICATIONS
        Schema::table('verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('verifications', 'ssl_days_remaining'))
                $table->integer('ssl_days_remaining')->nullable()->after('ssl_valid');
        });

        // TABLE INCIDENTS
        Schema::table('incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('incidents', 'is_resolved'))
                $table->boolean('is_resolved')->default(false)->after('duration_min');
        });

        // TABLE ALERTES
Schema::table('alertes', function (Blueprint $table) {
    if (!Schema::hasColumn('alertes', 'incident_id'))
        $table->foreignId('incident_id')->nullable()->constrained('incidents')->nullOnDelete()->after('site_id');
    if (!Schema::hasColumn('alertes', 'is_resolved_alert'))
        $table->boolean('is_resolved_alert')->default(false);
});

        // TABLE WHOIS_INFOS
        Schema::table('whois_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('whois_infos', 'registered_at'))
                $table->date('registered_at')->nullable()->after('registrar');
        });

        // TABLE RAPPORTS
        Schema::table('rapports', function (Blueprint $table) {
            if (!Schema::hasColumn('rapports', 'pdf_path'))
                $table->string('pdf_path')->nullable()->after('avg_response_ms');
        });

        // TABLE CRON_LOGS
        Schema::table('cron_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('cron_logs', 'error_message'))
                $table->text('error_message')->nullable()->after('errors_count');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['client_email', 'whois_check']);
        });
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn('ssl_days_remaining');
        });
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('is_resolved');
        });
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropColumn(['incident_id', 'is_resolved_alert']);
        });
        Schema::table('whois_infos', function (Blueprint $table) {
            $table->dropColumn('registered_at');
        });
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
        Schema::table('cron_logs', function (Blueprint $table) {
            $table->dropColumn('error_message');
        });
    }
};