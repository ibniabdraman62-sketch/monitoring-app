<?php
use Illuminate\Support\Facades\Route;
use App\Models\Site;
use App\Models\Incident;
use App\Models\Verification;

Route::get('/monitoring-data', function () {
    $sites = Site::with(['verifications' => function($q) {
        $q->latest()->take(10);
    }, 'incidents' => function($q) {
        $q->whereNull('resolved_at');
    }])->get();

    $stats = [
        'total_sites'     => $sites->count(),
        'sites_actifs'    => $sites->where('is_active', true)->count(),
        'incidents_actifs'=> Incident::whereNull('resolved_at')->count(),
        'sites_data'      => $sites->map(function($site) {
            $last = $site->verifications->first();
            $total = $site->verifications()->where('created_at', '>=', now()->subDay())->count();
            $up = $site->verifications()->where('created_at', '>=', now()->subDay())->where('is_up', true)->count();
            return [
                'id'            => $site->id,
                'client_name'   => $site->client_name,
                'url'           => $site->url,
                'is_up'         => $last ? $last->is_up : null,
                'response_time' => $last ? $last->response_time_ms : null,
                'http_code'     => $last ? $last->http_code : null,
                'ssl_valid'     => $last ? $last->ssl_valid : null,
                'uptime_24h'    => $total > 0 ? round($up/$total*100, 1) : 100,
                'incidents_actifs' => $site->incidents->count(),
            ];
        }),
        'incidents_recents' => Incident::with('site')
            ->latest('started_at')
            ->take(5)
            ->get()
            ->map(fn($i) => [
                'site'       => $i->site->client_name,
                'type'       => $i->type,
                'started_at' => $i->started_at->format('d/m/Y H:i'),
                'resolu'     => $i->resolved_at ? true : false,
                'duree'      => $i->duration_min,
            ]),
    ];

    return response()->json($stats);
});

Route::post('/ai-rapport', function(\Illuminate\Http\Request $request) {
    // Endpoint pour recevoir le rapport IA de n8n
    $data = $request->all();
    // Stocker dans cache pour affichage dashboard
    cache()->put('ai_rapport', $data, now()->addHours(2));
    return response()->json(['status' => 'ok']);
});

Route::get('/ai-rapport', function() {
    return response()->json(cache()->get('ai_rapport', ['rapport' => 'Aucun rapport IA généré encore.']));
});