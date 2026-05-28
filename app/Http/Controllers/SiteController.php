<?php
namespace App\Http\Controllers;

use App\Services\MonitoringService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function index() {
    $search = request('search');
    $statut = request('statut');

    $user = Auth::user();

    // ─── Choix de la requête selon le rôle ───
    if ($user->role === 'client') {
        // Client : voit UNIQUEMENT ses propres sites
        $query = Site::where('user_id', $user->id);
    } else {
        // Super Admin + Agent : voient TOUS les sites
        $query = Site::query();
    }

    $sites = $query
        ->when($search, function($q) use ($search) {
            $q->where(function($q2) use ($search) {
                $q2->where('client_name', 'LIKE', "%{$search}%")
                   ->orWhere('url', 'LIKE', "%{$search}%");
            });
        })
        ->get();

    // Filtre par statut (appliqué en PHP après récupération)
    if ($statut) {
        $sites = $sites->filter(function($site) use ($statut) {
            $lastVerif = $site->verifications()->latest('checked_at')->first();
            switch ($statut) {
                case 'online':
                    return $lastVerif && $lastVerif->is_up &&
                           $lastVerif->response_time_ms <= $site->response_threshold_ms;
                case 'offline':
                    return $lastVerif && !$lastVerif->is_up;
                case 'slow':
                    return $lastVerif && $lastVerif->is_up &&
                           $lastVerif->response_time_ms > $site->response_threshold_ms;
                case 'ssl':
                    if (!$lastVerif || !$lastVerif->ssl_expires_at) return false;
                    return now()->diffInDays($lastVerif->ssl_expires_at, false) < 30;
                case 'inactive':
                    return !$site->is_active;
            }
            return true;
        });
    }

    return view('sites.index', compact('sites'));
}

    public function create() {
        if (auth()->user()->role === 'client') {
        abort(403, 'Action réservée aux administrateurs.');
    }
    return view('sites.create');
        
    }

    public function store(Request $request) {
        if (auth()->user()->role === 'client') {
        abort(403, 'Action réservée aux administrateurs.');
    }
    // return view('sites.create');
        $validated = $request->validate([
        'user_id'                => 'required|exists:users,id',
        'client_name'            => 'required|string|max:255',
        'client_email'           => 'nullable|email',
        'url'                    => 'required|url|max:500',
        'frequency_min'          => 'required|integer|in:5,10,15,30',
        'response_threshold_ms'  => 'required|integer|min:500|max:30000',
        'notify_emails'          => 'nullable|string',
        'ssl_check'              => 'nullable|boolean',
        'whois_check'            => 'nullable|boolean',
        ]);

        Site::create([
        'user_id'               => $validated['user_id'],
        'client_name'           => $validated['client_name'],
        'client_email'          => $validated['client_email'] ?? null,
        'url'                   => $validated['url'],
        'frequency_min'         => $validated['frequency_min'],
        'response_threshold_ms' => $validated['response_threshold_ms'],
        'notify_emails'         => $validated['notify_emails'] ?? null,
        'ssl_check'             => $request->boolean('ssl_check'),
        'whois_check'           => $request->boolean('whois_check'),
        'is_active'             => true,
    ]);

            return redirect()->route('sites.index')->with('success', 'Site ajouté avec succès.');

    }

    public function show(Site $site) {
        $verifications = $site->verifications()
            ->latest('checked_at')->take(50)->get();
        $incidents = $site->incidents()
            ->latest()->take(10)->get();
        return view('sites.show', compact('site', 'verifications', 'incidents'));
    }

    public function edit(Site $site) {
        if (auth()->user()->role === 'client') {
        abort(403, 'Action réservée aux administrateurs.');
    }
    // return view('sites.create');
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site) {
        if (auth()->user()->role === 'client') {
        abort(403, 'Action réservée aux administrateurs.');
    }
    // return view('sites.create');
        $request->validate([
            'client_name'           => 'required|string|max:255',
            'url'                   => 'required|url',
            'frequency_min'         => 'required|integer|min:1',
            'response_threshold_ms' => 'required|integer|min:100',
        ]);

        $site->update([
            'client_name'           => $request->client_name,
            'url'                   => $request->url,
            'frequency_min'         => $request->frequency_min,
            'response_threshold_ms' => $request->response_threshold_ms,
            'ssl_check'             => $request->has('ssl_check'),
            'notify_emails'         => $request->notify_emails,
            'client_email'          => $request->client_email,
            'whois_check'           => $request->has('whois_check') ? 1 : 0,
        ]);

        return redirect()->route('sites.index')
            ->with('success', 'Site modifié avec succès !');
    }

    public function destroy(Site $site) {
        if (auth()->user()->role === 'client') {
        abort(403, 'Action réservée aux administrateurs.');
    }
    // return view('sites.create');
        $site->delete();
        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès !');
    }

    public function toggle(Site $site) {
        if (auth()->user()->role === 'client') {
        abort(403, 'Action réservée aux administrateurs.');
    }
    //E return view('sites.create');
        $site->update(['is_active' => !$site->is_active]);
        return back()->with('success', 'Statut mis à jour !');
    }

    public function checkNow(Site $site, MonitoringService $service) {
        $service->checkSite($site);
        $lastVerif = $site->verifications()->latest('checked_at')->first();
        return response()->json([
            'is_up'         => $lastVerif->is_up,
            'response_time' => $lastVerif->response_time_ms,
            'http_code'     => $lastVerif->http_code,
        ]);
    }
}