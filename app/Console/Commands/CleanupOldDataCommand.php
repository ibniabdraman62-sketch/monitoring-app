<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Verification;
use App\Models\Incident;
use App\Models\CronLog;

class CleanupOldDataCommand extends Command {
    protected $signature   = 'monitor:cleanup';
    protected $description = 'Supprime les vérifications de plus de 90 jours';

    public function handle(): void {
        $start = now();
        $deleted = Verification::where('created_at', '<', now()->subDays(90))->delete();
        $archived = Incident::whereNotNull('resolved_at')->where('resolved_at', '<', now()->subDays(90))->count();

        CronLog::create([
            'command'       => 'monitor:cleanup',
            'status'        => 'success',
            'duration_ms'   => now()->diffInMilliseconds($start),
            'sites_checked' => $deleted,
            'errors_count'  => 0,
            'executed_at'   => now(),
        ]);
        $this->info("{$deleted} vérifications supprimées. {$archived} incidents archivés.");
    }
}