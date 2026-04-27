<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\CronLog;
use App\Services\MonitoringService;

class CheckUptimeCommand extends Command {
    protected $signature   = 'monitor:check-uptime';
    protected $description = 'Vérifie la disponibilité de tous les sites actifs';

    public function handle(): void {
        $start = now();
        $sites = Site::where('is_active', true)->get();
        $errors = 0;
        $errorMsg = '';
        $service = new MonitoringService();

        foreach ($sites as $site) {
            try {
                $service->checkSite($site);
                $this->line("OK: {$site->client_name}");
            } catch (\Exception $e) {
                $errors++;
                $errorMsg .= "{$site->client_name}: {$e->getMessage()}\n";
                $this->warn("ERR: {$site->client_name}");
            }
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