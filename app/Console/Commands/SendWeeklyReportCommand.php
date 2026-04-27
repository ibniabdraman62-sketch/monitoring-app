<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\CronLog;

class SendWeeklyReportCommand extends Command {
    protected $signature   = 'monitor:send-weekly-report';
    protected $description = 'Envoie le rapport hebdomadaire par email chaque lundi';

    public function handle(): void {
        $start = now();
        $sites = Site::where('is_active', true)->get();
        $errors = 0;
        $errorMsg = '';

        foreach ($sites as $site) {
            try {
                $total   = $site->verifications()->where('created_at', '>=', now()->subWeek())->count();
                $up      = $site->verifications()->where('created_at', '>=', now()->subWeek())->where('is_up', true)->count();
                $uptime  = $total > 0 ? round($up/$total*100, 1) : 100;
                $avgTime = $site->verifications()->where('created_at', '>=', now()->subWeek())->avg('response_time_ms');
                $incidents = $site->incidents()->where('started_at', '>=', now()->subWeek())->count();

                $emails = $site->notify_emails
                    ? explode(',', $site->notify_emails)
                    : [config('mail.from.address')];

                foreach ($emails as $email) {
                    \Illuminate\Support\Facades\Mail::raw(
                        "RAPPORT HEBDOMADAIRE — {$site->client_name}\n\n" .
                        "Periode : " . now()->subWeek()->format('d/m/Y') . " -> " . now()->format('d/m/Y') . "\n\n" .
                        "Uptime : {$uptime}%\n" .
                        "Temps reponse moyen : " . round($avgTime) . "ms\n" .
                        "Incidents : {$incidents}\n" .
                        "URL : {$site->url}\n\n" .
                        "-- MonitorPro | Soft Seven Art",
                        fn($m) => $m->to(trim($email))
                                     ->subject("Rapport hebdo — {$site->client_name}")
                    );
                }
                $this->line("OK rapport: {$site->client_name}");
            } catch (\Exception $e) {
                $errors++;
                $errorMsg .= "{$site->client_name}: {$e->getMessage()}\n";
            }
        }

        CronLog::create([
            'command'       => 'monitor:send-weekly-report',
            'status'        => $errors === 0 ? 'success' : 'error',
            'duration_ms'   => now()->diffInMilliseconds($start),
            'sites_checked' => $sites->count(),
            'errors_count'  => $errors,
            'error_message' => $errorMsg ?: null,
            'executed_at'   => now(),
        ]);
        $this->info("Rapports envoyes : {$sites->count()} sites, {$errors} erreurs.");
    }
}