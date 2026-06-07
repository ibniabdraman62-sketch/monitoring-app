<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alerte;
use App\Models\Site;

class AlerteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ═══ Base query avec filtrage selon le rôle ═══
        $baseQuery = Alerte::query();

        if ($user->role === 'client') {
            // Le client ne voit QUE les alertes de SES sites
            $baseQuery->whereHas('incident.site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // ═══ Appliquer les filtres de la requête ═══
        $filtered = (clone $baseQuery)->with(['incident.site'])
            ->orderBy('sent_at', 'desc');

        if (request('site_id')) {
            $filtered->whereHas('incident', fn($q) => $q->where('site_id', request('site_id')));
        }
        if (request('type')) {
            $filtered->where('type', request('type'));
        }
        if (request('date_from')) {
            $filtered->where('sent_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $filtered->where('sent_at', '<=', request('date_to') . ' 23:59:59');
        }
        if (request('search')) {
            $filtered->whereHas('incident.site', fn($q) =>
                $q->where('client_name', 'LIKE', '%' . request('search') . '%')
                  ->orWhere('url', 'LIKE', '%' . request('search') . '%')
            );
        }

        $alertes = $filtered->paginate(20)->withQueryString();

        // ═══ Liste des sites pour le filtre dropdown ═══
        if ($user->role === 'client') {
            $sites = Site::where('user_id', $user->id)->get();
        } else {
            $sites = Site::all();
        }

        // ═══ Statistiques (filtrées selon le rôle) ═══
        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'down'     => (clone $baseQuery)->where('type', 'down')->count(),
            'slow'     => (clone $baseQuery)->where('type', 'slow')->count(),
            'resolved' => (clone $baseQuery)->where('type', 'resolved')->count(),
        ];

        return view('alertes.index', compact('alertes', 'sites', 'stats'));
    }
}