<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Services\WhoisService;

class CheckWhoisCommand extends Command
{
    protected $signature   = 'monitor:check-whois';
    protected $description = 'Vérifie les informations WHOIS et expiration des domaines';

    public function handle(): void
    {
        $sites = Site::where('is_active', true)->get();
        $service = new WhoisService();
        $this->info("Vérification WHOIS de {$sites->count()} domaines...");

        foreach ($sites as $site) {
            $result = $service->checkDomain($site);
            if ($result['success']) {
                $this->line("✅ WHOIS OK — {$site->client_name} — Expire : {$result['expires_at']}");
            } else {
                $this->warn("⚠️ WHOIS échoué — {$site->client_name} — {$result['error']}");
            }
        }
        $this->info('Vérification WHOIS terminée.');
    }
}