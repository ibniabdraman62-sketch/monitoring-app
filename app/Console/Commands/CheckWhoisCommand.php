<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\CronLog;
use App\Services\WhoisService;

class CheckWhoisCommand extends Command {
    protected $signature   = 'monitor:check-whois';
    protected $description = 'Vérifie les informations WHOIS et expiration des domaines';

    public function handle(): void {
        $start = now();
        $sites = Site::where('is_active', true)->get();
        $errors = 0;
        $errorMsg = '';
        $service = new WhoisService();

        foreach ($sites as $site) {
            try {
                $result = $service->checkDomain($site);
                if ($result['success']) {
                    $this->line("OK WHOIS: {$site->client_name}");
                } else {
                    $errors++;
                    $errorMsg .= "{$site->client_name}: {$result['error']}\n";
                    $this->warn("ERR WHOIS: {$site->client_name}");
                }
            } catch (\Exception $e) {
                $errors++;
                $errorMsg .= "{$site->client_name}: {$e->getMessage()}\n";
            }
        }

        CronLog::create([
            'command'       => 'monitor:check-whois',
            'status'        => $errors === 0 ? 'success' : 'error',
            'duration_ms'   => now()->diffInMilliseconds($start),
            'sites_checked' => $sites->count(),
            'errors_count'  => $errors,
            'error_message' => $errorMsg ?: null,
            'executed_at'   => now(),
        ]);
        $this->info("WHOIS terminé : {$sites->count()} domaines, {$errors} erreurs.");
    }
}