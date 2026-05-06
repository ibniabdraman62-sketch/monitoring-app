<?php
namespace App\Jobs;

use App\Models\Site;
use App\Models\Rapport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(public Site $site) {}

    public function handle(): void
    {
        $rapport = Rapport::where('site_id', $this->site->id)
            ->latest('generated_at')->first();
        $rapport?->sendByEmail();
    }
}