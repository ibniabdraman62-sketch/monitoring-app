<?php
namespace App\Console;

use App\Jobs\CheckSiteJob;
use App\Models\Site;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
{
    // Cron Job 1 — Uptime toutes les 5 minutes
    $schedule->command('monitor:check-uptime')
             ->everyFiveMinutes()
             ->withoutOverlapping()
             ->runInBackground();

    // Cron Job 2 — SSL toutes les heures
    $schedule->command('monitor:check-ssl')
             ->hourly()
             ->withoutOverlapping();

    // Cron Job 3 — WHOIS chaque semaine
    $schedule->command('monitor:check-whois')
             ->weekly()
             ->withoutOverlapping();

    // Cron Job 4 — Rapport hebdo chaque lundi à 8h
    $schedule->command('monitor:send-weekly-report')
             ->weeklyOn(1, '08:00');

    // Cron Job 5 — Nettoyage chaque jour à minuit
    $schedule->command('monitor:cleanup')
             ->dailyAt('00:00');
}

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}