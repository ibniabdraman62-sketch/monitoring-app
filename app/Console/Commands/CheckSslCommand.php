<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\CronLog;
use App\Services\AlerteService;

class CheckSslCommand extends Command
{
    protected $signature   = 'monitor:check-ssl';
    protected $description = 'Vérifie les certificats SSL de tous les sites actifs';

    public function handle(): void
    {
        $start  = now();
        $sites  = Site::where('is_active', true)->where('ssl_check', true)->get();
        $errors = 0;
        $errorMsg = '';
        $alerteService = new AlerteService();

        foreach ($sites as $site) {
            try {
                $lastVerif = $site->verifications()->latest('checked_at')->first();

                if ($lastVerif && $lastVerif->ssl_expires_at) {
                    $daysLeft  = (int) now()->diffInDays($lastVerif->ssl_expires_at, false);
                    $expiresAt = $lastVerif->ssl_expires_at;

                    if ($daysLeft <= 30 && $daysLeft >= 0) {
                        $alerteService->sendSslAlert($site, $daysLeft, $expiresAt);
                    }
                }

                $this->line("OK SSL: {$site->client_name}");

            } catch (\Exception $e) {
                $errors++;
                $errorMsg .= "{$site->client_name}: {$e->getMessage()}\n";
                $this->warn("ERR SSL: {$site->client_name}");
            }
        }

        CronLog::create([
            'command'       => 'monitor:check-ssl',
            'status'        => $errors === 0 ? 'success' : 'error',
            'duration_ms'   => now()->diffInMilliseconds($start),
            'sites_checked' => $sites->count(),
            'errors_count'  => $errors,
            'error_message' => $errorMsg ?: null,
            'executed_at'   => now(),
        ]);

        $this->info("SSL vérifié : {$sites->count()} sites, {$errors} erreurs.");
    }
}