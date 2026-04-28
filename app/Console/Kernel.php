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
    $schedule->command('monitor:check-uptime')
             ->everyFiveMinutes()
             ->withoutOverlapping()
             ->runInBackground();

    $schedule->command('monitor:check-ssl')
             ->hourly()
             ->withoutOverlapping()
             ->runInBackground();

    $schedule->command('monitor:check-whois')
             ->weekly()
             ->withoutOverlapping();

    $schedule->command('monitor:send-weekly-report')
             ->weeklyOn(1, '08:00');

    $schedule->command('monitor:cleanup')
             ->dailyAt('00:00');
}

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}