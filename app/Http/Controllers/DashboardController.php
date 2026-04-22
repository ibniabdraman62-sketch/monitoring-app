<?php
namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Verification;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $sites = Site::where('user_id', Auth::id())->get();

        // KPIs
        $totalSites    = $sites->count();
        $activeSites = $sites->where('is_active', 1)->count();
        $incidents     = Incident::whereIn('site_id', $sites->pluck('id'))
                            ->whereNull('resolved_at')->count();

        // Uptime moyen (dernières 24h)
        $uptimeData = Verification::whereIn('site_id', $sites->pluck('id'))
                        ->where('created_at', '>=', Carbon::now()->subDay())
                        ->get();
        $uptimeMoyen = $uptimeData->count() > 0
            ? round($uptimeData->where('is_up', true)->count() / $uptimeData->count() * 100, 1)
            : 100;

        // Données graphique (temps de réponse 24h)
        $graphData = [];
        foreach ($sites as $site) {
            $verifs = Verification::where('site_id', $site->id)
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->orderBy('created_at')
                ->get();
            $graphData[] = [
                'label' => $site->client_name,
                'data'  => $verifs->map(fn($v) => [
                    'x' => $v->checked_at->format('H:i'),
                    'y' => $v->response_time_ms
                ])->values()->toArray()
            ];
        }

        // Statut actuel de chaque site
        $sitesStatus = $sites->map(function($site) {
            $lastVerif = Verification::where('site_id', $site->id)
                ->latest('checked_at')->first();
            return [
                'id'            => $site->id,
                'client_name'   => $site->client_name,
                'url'           => $site->url,
                'is_active'     => $site->is_active,
                'is_up'         => $lastVerif ? $lastVerif->is_up : null,
                'response_time' => $lastVerif ? $lastVerif->response_time_ms : null,
                'http_code'     => $lastVerif ? $lastVerif->http_code : null,
                'ssl_valid'     => $lastVerif ? $lastVerif->ssl_valid : null,
                'checked_at'    => $lastVerif ? $lastVerif->checked_at->diffForHumans() : 'Jamais',
            ];
        });

        return view('dashboard', compact(
            'totalSites', 'activeSites', 'incidents',
            'uptimeMoyen', 'graphData', 'sitesStatus'
        ));
    }
}