<?php
namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Rapport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportController extends Controller {

    public function index() {
        $sites   = Site::where('user_id', auth()->id())->get();
        $rapports = Rapport::with('site')
            ->whereHas('site', fn($q) => $q->where('user_id', auth()->id()))
            ->latest('generated_at')->get();
        return view('rapports.index', compact('sites', 'rapports'));
    }

    public function generate(Request $request, Site $site) {
        $verifications = $site->verifications()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('checked_at', 'desc')->get();

        $total    = $verifications->count();
        $up       = $verifications->where('is_up', true)->count();
        $uptime   = $total > 0 ? round($up / $total * 100, 2) : 100;
        $avgTime  = $verifications->avg('response_time_ms');
        $incidents = $site->incidents()->where('started_at', '>=', now()->subDays(30))->get();

        $pdf = Pdf::loadView('rapports.pdf', compact(
            'site', 'verifications', 'uptime', 'avgTime', 'incidents'
        ));

        // Sauvegarde dans la table rapports
        $filename = 'rapport_' . $site->id . '_' . now()->format('YmdHis') . '.pdf';
        Rapport::create([
            'site_id'       => $site->id,
            'period_start'  => now()->subDays(30)->toDateString(),
            'period_end'    => now()->toDateString(),
            'uptime_pct'    => $uptime,
            'incidents_count' => $incidents->count(),
            'avg_response_ms' => round($avgTime),
            'pdf_path'      => $filename,
            'generated_at'  => now(),
        ]);

        return $pdf->download("rapport_{$site->client_name}.pdf");
    }

    public function sendEmail(Request $request, Site $site) {
        $request->validate(['email' => 'required|email']);

        $verifications = $site->verifications()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('checked_at', 'desc')->get();

        $total   = $verifications->count();
        $up      = $verifications->where('is_up', true)->count();
        $uptime  = $total > 0 ? round($up / $total * 100, 2) : 100;
        $avgTime = $verifications->avg('response_time_ms');
        $incidents = $site->incidents()->where('started_at', '>=', now()->subDays(30))->get();

        $pdf = Pdf::loadView('rapports.pdf', compact(
            'site', 'verifications', 'uptime', 'avgTime', 'incidents'
        ));

        Mail::raw(
            "Veuillez trouver ci-joint le rapport de disponibilite de {$site->client_name}.\n\n-- MonitorPro | Soft Seven Art",
            function($m) use ($request, $site, $pdf) {
                $m->to($request->email)
                  ->subject("Rapport disponibilite — {$site->client_name}")
                  ->attachData($pdf->output(), "rapport_{$site->client_name}.pdf");
            }
        );

        return back()->with('success', "Rapport envoyé à {$request->email} !");
    }
}