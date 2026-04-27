<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Services\AlerteService;

class CheckSslCommand extends Command
{
    protected $signature   = 'monitor:check-ssl';
    protected $description = 'Vérifie les certificats SSL de tous les sites actifs';

    public function handle(): void
    {
        $sites = Site::where('is_active', true)->where('ssl_check', true)->get();
        $alerteService = new AlerteService();
        $this->info("Vérification SSL de {$sites->count()} sites...");

        foreach ($sites as $site) {
            $lastVerif = $site->verifications()->latest('checked_at')->first();
            if ($lastVerif && $lastVerif->ssl_expires_at) {
                $daysLeft = now()->diffInDays($lastVerif->ssl_expires_at, false);
                if ($daysLeft <= 30 && $daysLeft >= 0) {
                    $this->warn("⚠️ SSL expire dans {$daysLeft}j — {$site->client_name}");
                    // Envoie alerte SSL
                    $emails = $site->notify_emails
                        ? explode(',', $site->notify_emails)
                        : [config('mail.from.address')];
                    foreach ($emails as $email) {
                        \Illuminate\Support\Facades\Mail::raw(
                            "⚠️ ALERTE SSL — {$site->client_name}\n\n" .
                            "Le certificat SSL du site {$site->url} expire dans {$daysLeft} jours.\n" .
                            "Date d'expiration : {$lastVerif->ssl_expires_at}\n\n" .
                            "Veuillez renouveler ce certificat rapidement.\n\n" .
                            "— MonitorPro | Soft Seven Art",
                            fn($m) => $m->to(trim($email))
                                         ->subject("⚠️ SSL expire bientôt — {$site->client_name}")
                        );
                    }
                }
            }
            $this->line("✅ SSL vérifié — {$site->client_name}");
        }
        $this->info('Vérification SSL terminée.');
    }
}