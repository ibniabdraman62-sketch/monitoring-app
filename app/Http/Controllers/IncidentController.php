<?php
namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Base query selon le rôle
        $baseQuery = Incident::query();
        if ($user->role === 'client') {
            $baseQuery->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // ═══ Statistiques globales (pas paginées) ═══
        $totalIncidents    = (clone $baseQuery)->count();
        $activeCount       = (clone $baseQuery)->whereNull('resolved_at')->count();
        $resolvedCount     = (clone $baseQuery)->whereNotNull('resolved_at')->count();

        $avgDuration = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->whereNotNull('duration_min')
            ->avg('duration_min');
        $avgDuration = $avgDuration ? round($avgDuration) : 0;

        // ═══ Liste paginée ═══
        $incidents = $baseQuery->with('site')
            ->latest('started_at')
            ->paginate(20);

        return view('incidents.index', compact(
            'incidents',
            'totalIncidents',
            'activeCount',
            'resolvedCount',
            'avgDuration'
        ));
    }
}