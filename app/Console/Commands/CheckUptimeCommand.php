<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Services\MonitoringService;

class CheckUptimeCommand extends Command
{
    protected $signature   = 'monitor:check-uptime';
    protected $description = 'Vérifie la disponibilité et le temps de réponse de tous les sites actifs';

    public function handle(): void
    {
        $sites = Site::where('is_active', true)->get();
        $this->info("Vérification de {$sites->count()} sites...");
        $service = new MonitoringService();
        foreach ($sites as $site) {
            $service->checkSite($site);
            $this->line("✅ {$site->client_name}");
        }
        $this->info('Vérification uptime terminée.');
    }
}