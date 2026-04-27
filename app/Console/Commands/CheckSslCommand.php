<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\CronLog;

class CheckSslCommand extends Command {
    protected $signature   = 'monitor:check-ssl';
    protected $description = 'Vérifie les certificats SSL de tous les sites actifs';

    public function handle(): void {
        $start = now();
        $sites = Site::where('is_active', true)->where('ssl_check', true)->get();
        $errors = 0; $errorMsg = '';

        foreach ($sites as $site) {
            try {
                $lastVerif = $site->verifications()->latest('checked_at')->first();
                if ($lastVerif && $lastVerif->ssl_expires_at) {
                    $daysLeft = now()->diffInDays($lastVerif->ssl_expires_at, false);
                    if ($daysLeft <= 30 && $daysLeft >= 0) {
                        $emails = $site->notify_emails ? explode(',', $site->notify_emails) : [config('mail.from.address')];
                        foreach ($emails as $email) {
                            \Illuminate\Support\Facades\Mail::raw(
                                "ALERTE SSL — {$site->client_name}\nSSL expire dans {$daysLeft} jours.\nURL: {$site->url}\n-- MonitorPro",
                                fn($m) => $m->to(trim($email))->subject("SSL expire bientot — {$site->client_name}")
                            );
                        }
                    }
                }
                $this->line("OK SSL: {$site->client_name}");
            } catch (\Exception $e) {
                $errors++; $errorMsg .= "{$site->client_name}: {$e->getMessage()}\n";
            }
        }

        CronLog::create([
            'command' => 'monitor:check-ssl', 'status' => $errors === 0 ? 'success' : 'error',
            'duration_ms' => now()->diffInMilliseconds($start), 'sites_checked' => $sites->count(),
            'errors_count' => $errors, 'error_message' => $errorMsg ?: null, 'executed_at' => now(),
        ]);
        $this->info("SSL verifie : {$sites->count()} sites, {$errors} erreurs.");
    }
}