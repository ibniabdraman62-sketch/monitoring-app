<?php
namespace App\Jobs;

use App\Models\Site;
use App\Services\MonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSslJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(public Site $site) {}

    public function handle(MonitoringService $service): void
    {
        $result = $service->checkSSL($this->site->url);
        $this->site->verifications()->latest('checked_at')->first()?->update([
            'ssl_valid'          => $result['valid'],
            'ssl_days_remaining' => $result['days_remaining'],
        ]);
    }
}