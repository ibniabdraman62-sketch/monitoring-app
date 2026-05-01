<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('rapports', function (Blueprint $table) {
            if (!Schema::hasColumn('rapports', 'period_start'))
                $table->date('period_start')->nullable()->after('site_id');
            if (!Schema::hasColumn('rapports', 'period_end'))
                $table->date('period_end')->nullable()->after('period_start');
            if (!Schema::hasColumn('rapports', 'uptime_pct'))
                $table->decimal('uptime_pct', 5, 2)->default(100)->after('period_end');
            if (!Schema::hasColumn('rapports', 'incidents_count'))
                $table->integer('incidents_count')->default(0)->after('uptime_pct');
            if (!Schema::hasColumn('rapports', 'avg_response_ms'))
                $table->integer('avg_response_ms')->default(0)->after('incidents_count');
            if (!Schema::hasColumn('rapports', 'pdf_path'))
                $table->string('pdf_path')->nullable()->after('avg_response_ms');
            if (!Schema::hasColumn('rapports', 'generated_at'))
                $table->timestamp('generated_at')->nullable()->after('pdf_path');
        });
    }
    public function down(): void {
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropColumn(['period_start','period_end','uptime_pct','incidents_count','avg_response_ms','pdf_path','generated_at']);
        });
    }
};