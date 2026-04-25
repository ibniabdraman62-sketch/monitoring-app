<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Incident;

Route::get('/monitoring-data', function (Request $request) {
    $key = $request->header('X-API-Key') ?? $request->query('api_key');
    if ($key !== env('API_MONITORING_KEY')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $sites = Site::with(['verifications' => function($q) {
        $q->latest()->take(10);
    }, 'incidents' => function($q) {
        $q->whereNull('resolved_at');
    }])->get();

    return response()->json([
        'total_sites'       => $sites->count(),
        'sites_actifs'      => $sites->where('is_active', true)->count(),
        'incidents_actifs'  => Incident::whereNull('resolved_at')->count(),
        'sites_data'        => $sites->map(function($site) {
            $last  = $site->verifications->first();
            $total = $site->verifications()->where('created_at','>=',now()->subDay())->count();
            $up    = $site->verifications()->where('created_at','>=',now()->subDay())->where('is_up',true)->count();
            return [
                'id'               => $site->id,
                'client_name'      => $site->client_name,
                'url'              => $site->url,
                'is_up'            => $last ? $last->is_up : null,
                'response_time'    => $last ? $last->response_time_ms : null,
                'http_code'        => $last ? $last->http_code : null,
                'ssl_valid'        => $last ? $last->ssl_valid : null,
                'ssl_expires_at'   => $last ? $last->ssl_expires_at : null,
                'uptime_24h'       => $total > 0 ? round($up/$total*100, 1) : 100,
                'incidents_actifs' => $site->incidents->count(),
            ];
        }),
        'incidents_recents' => Incident::with('site')
            ->latest('started_at')->take(5)->get()
            ->map(fn($i) => [
                'site'       => $i->site->client_name,
                'type'       => $i->type,
                'started_at' => $i->started_at->format('d/m/Y H:i'),
                'resolu'     => $i->resolved_at ? true : false,
                'duree'      => $i->duration_min,
            ]),
        'generated_at' => now()->format('d/m/Y H:i:s'),
    ]);
});

Route::post('/ai-rapport', function(Request $request) {
    $key = $request->header('X-API-Key') ?? $request->query('api_key');
    if ($key !== env('API_MONITORING_KEY')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    cache()->put('ai_rapport', $request->all(), now()->addHours(2));
    return response()->json(['status' => 'ok']);
});

Route::get('/ai-rapport', function(Request $request) {
    $key = $request->header('X-API-Key') ?? $request->query('api_key');
    if ($key !== env('API_MONITORING_KEY')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    return response()->json(
        cache()->get('ai_rapport', ['rapport' => 'Aucun rapport IA généré encore.'])
    );
});