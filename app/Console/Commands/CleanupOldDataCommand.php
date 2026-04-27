<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Verification;
use App\Models\Incident;

class CleanupOldDataCommand extends Command
{
    protected $signature   = 'monitor:cleanup';
    protected $description = 'Supprime les vérifications de plus de 90 jours';

    public function handle(): void
    {
        $deleted = Verification::where('created_at', '<', now()->subDays(90))->delete();
        $this->info("🗑️ {$deleted} vérifications supprimées (> 90 jours).");

        $archived = Incident::whereNotNull('resolved_at')
            ->where('resolved_at', '<', now()->subDays(90))
            ->count();
        $this->info("📦 {$archived} incidents anciens archivés.");
    }
}