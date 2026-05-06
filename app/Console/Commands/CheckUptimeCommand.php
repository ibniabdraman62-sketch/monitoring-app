<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\CronLog;
use App\Services\MonitoringService;
use App\Jobs\CheckUptimeJob;


class CheckUptimeCommand extends Command {
    protected $signature   = 'monitor:check-uptime';
    protected $description = 'Vérifie la disponibilité de tous les sites actifs';

    public function handle(): void {
        $start = now();
        $sites = Site::where('is_active', true)->get();
        $errors = 0;
        $errorMsg = '';
        $service = new MonitoringService();

        // Dans handle()
$sites = \App\Models\Site::where('is_active', true)->get();
foreach ($sites as $site) {
    CheckUptimeJob::dispatch($site);
}

        CronLog::create([
            'command'       => 'monitor:check-uptime',
            'status'        => $errors === 0 ? 'success' : 'error',
            'duration_ms'   => now()->diffInMilliseconds($start),
            'sites_checked' => $sites->count(),
            'errors_count'  => $errors,
            'error_message' => $errorMsg ?: null,
            'executed_at'   => now(),
        ]);
        $this->info("Uptime vérifié : {$sites->count()} sites, {$errors} erreurs.");
    }
}