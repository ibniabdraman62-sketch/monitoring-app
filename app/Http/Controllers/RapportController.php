<?php
namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Rapport;
use App\Models\Verification;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RapportController extends Controller
{
    public function index()
    {
        $rapports = Rapport::whereHas('site', function($q) {
            $q->where('user_id', Auth::id());
        })->with('site')->latest()->get();

        return view('rapports.index', compact('rapports'));
    }

    public function generate(Site $site)
    {
        $periodStart = Carbon::now()->subDays(7)->startOfDay();
        $periodEnd   = Carbon::now()->endOfDay();

        // Calcul uptime
        $verifications = Verification::where('site_id', $site->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->get();

        $totalVerifs = $verifications->count();
        $uptimePct   = $totalVerifs > 0
            ? round($verifications->where('is_up', true)->count() / $totalVerifs * 100, 2)
            : 100;

        // Temps de réponse moyen
        $avgResponse = $verifications->avg('response_time_ms');

        // Incidents
        $incidents = Incident::where('site_id', $site->id)
            ->whereBetween('started_at', [$periodStart, $periodEnd])
            ->get();

        // Créer le rapport en base
        $rapport = Rapport::create([
            'site_id'      => $site->id,
            'period_start' => $periodStart->toDateString(),
            'period_end'   => $periodEnd->toDateString(),
            'uptime_pct'   => $uptimePct,
            'generated_at' => now(),
        ]);

        // Générer le PDF
        $pdf = Pdf::loadView('rapports.pdf', compact(
            'site', 'rapport', 'verifications',
            'incidents', 'uptimePct', 'avgResponse',
            'periodStart', 'periodEnd'
        ));

        $filename = "rapport_{$site->id}_" . now()->format('Ymd_His') . ".pdf";
        $path = storage_path("app/public/rapports/{$filename}");

        if (!file_exists(storage_path('app/public/rapports'))) {
            mkdir(storage_path('app/public/rapports'), 0755, true);
        }

        $pdf->save($path);
        $rapport->update(['pdf_path' => $filename]);

        return $pdf->download($filename);
    }

    public function download(Rapport $rapport)
    {
        $path = storage_path("app/public/rapports/{$rapport->pdf_path}");
        return response()->download($path);
    }
}