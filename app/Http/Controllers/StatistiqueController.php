<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Verification;
use App\Models\Incident;
use App\Models\Alerte;
use App\Models\Rapport;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        // RBAC : admin uniquement
        abort_if(auth()->user()->role === 'client', 403);

        // ═══════════ KPIs GLOBAUX ═══════════
        $kpis = [
            'sites_total'    => Site::count(),
            'sites_actifs'   => Site::where('is_active', true)->count(),
            'clients'        => User::where('role', 'client')->count(),
            'agents'         => User::where('role', 'agent')->count(),
            'incidents_mois' => Incident::where('started_at', '>=', now()->startOfMonth())->count(),
            'alertes_mois'   => Alerte::where('created_at', '>=', now()->startOfMonth())->count(),
            'rapports_mois'  => Rapport::where('generated_at', '>=', now()->startOfMonth())->count(),
            'verifs_total'   => Verification::count(),
        ];

        // Uptime moyen 30 derniers jours
        $verifs30j = Verification::where('checked_at', '>=', now()->subDays(30))
            ->selectRaw('SUM(CASE WHEN is_up = 1 THEN 1 ELSE 0 END) as up_count')
            ->selectRaw('COUNT(*) as total')
            ->first();
        $kpis['uptime_moyen'] = ($verifs30j && $verifs30j->total > 0)
            ? round($verifs30j->up_count / $verifs30j->total * 100, 2)
            : 100;

        // ═══════════ DATA CHARTS ═══════════

        // Évolution uptime 30 jours
        $uptimeEvolution = Verification::where('checked_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(checked_at) as date')
            ->selectRaw('ROUND(SUM(is_up) / COUNT(*) * 100, 2) as uptime')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Activité 7 jours (vérifications/jour)
        $activite7j = Verification::where('checked_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(checked_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Incidents par type (30j)
        $incidentsParType = Incident::where('started_at', '>=', now()->subDays(30))
            ->selectRaw('type, COUNT(*) as count')
            ->whereNotNull('type')
            ->groupBy('type')
            ->get();

        // Utilisateurs par rôle
        $usersParRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        // ═══════════ TABLES ═══════════

        // Sites actuellement DOWN (dernière vérif = is_up false)
        $sitesDown = Site::where('is_active', true)
            ->get()
            ->filter(function ($site) {
                $last = $site->verifications()->latest('checked_at')->first();
                return $last && !$last->is_up;
            })
            ->values();

        // Top 5 sites les plus instables (30j)
        $topInstables = Site::withCount(['incidents' => function ($q) {
                $q->where('started_at', '>=', now()->subDays(30));
            }])
            ->having('incidents_count', '>', 0)
            ->orderByDesc('incidents_count')
            ->take(5)
            ->get();

        // Top 5 sites les plus fiables (30j)
        $topFiables = Site::select('sites.*')
            ->selectSub(function ($q) {
                $q->from('verifications')
                  ->selectRaw('IFNULL(ROUND(SUM(is_up) / COUNT(*) * 100, 2), 100)')
                  ->whereColumn('site_id', 'sites.id')
                  ->where('checked_at', '>=', now()->subDays(30));
            }, 'uptime_pct')
            ->where('is_active', true)
            ->orderByDesc('uptime_pct')
            ->take(5)
            ->get();

        // 5 derniers incidents
        $derniersIncidents = Incident::with('site')
            ->latest('started_at')
            ->take(5)
            ->get();

        return view('statistiques.index', compact(
            'kpis', 'uptimeEvolution', 'activite7j',
            'incidentsParType', 'usersParRole',
            'sitesDown', 'topInstables', 'topFiables', 'derniersIncidents'
        ));
    }
}