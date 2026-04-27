<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\Verification;
use App\Models\Incident;

class SendWeeklyReportCommand extends Command
{
    protected $signature   = 'monitor:send-weekly-report';
    protected $description = 'Envoie le rapport hebdomadaire par email chaque lundi';

    public function handle(): void
    {
        $sites = Site::where('is_active', true)->get();
        $this->info("Génération du rapport hebdomadaire pour {$sites->count()} sites...");

        foreach ($sites as $site) {
            $total    = $site->verifications()->where('created_at', '>=', now()->subWeek())->count();
            $up       = $site->verifications()->where('created_at', '>=', now()->subWeek())->where('is_up', true)->count();
            $uptime   = $total > 0 ? round($up / $total * 100, 1) : 100;
            $avgTime  = $site->verifications()->where('created_at', '>=', now()->subWeek())->avg('response_time_ms');
            $incidents = $site->incidents()->where('started_at', '>=', now()->subWeek())->count();

            $emails = $site->notify_emails
                ? explode(',', $site->notify_emails)
                : [config('mail.from.address')];

            foreach ($emails as $email) {
                \Illuminate\Support\Facades\Mail::raw(
                    "📊 RAPPORT HEBDOMADAIRE — {$site->client_name}\n\n" .
                    "Période : " . now()->subWeek()->format('d/m/Y') . " → " . now()->format('d/m/Y') . "\n\n" .
                    "• Uptime : {$uptime}%\n" .
                    "• Temps réponse moyen : " . round($avgTime) . "ms\n" .
                    "• Incidents : {$incidents}\n" .
                    "• URL : {$site->url}\n\n" .
                    "— MonitorPro | Soft Seven Art",
                    fn($m) => $m->to(trim($email))
                                 ->subject("📊 Rapport hebdo — {$site->client_name}")
                );
            }
            $this->line("✅ Rapport envoyé — {$site->client_name}");
        }
        $this->info('Rapports hebdomadaires envoyés.');
    }
}