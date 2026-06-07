<?php
namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Rapport;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportController extends Controller {

    public function index() {
        $user = auth()->user();

        if ($user->role === 'client') {
            $sites = \App\Models\Site::where('user_id', $user->id)->get();
        } else {
            $sites = \App\Models\Site::all();
        }

        $rapports = Rapport::with('site')
            ->whereHas('site', fn($q) => $q->where('user_id', auth()->id()))
            ->latest('generated_at')->get();

        return view('rapports.index', compact('sites', 'rapports'));
    }

    public function generate(Request $request, Site $site) {
        $periodStart = now()->subDays(30);
        $periodEnd   = now();

        $verifications = $site->verifications()
            ->where('checked_at', '>=', $periodStart)
            ->orderBy('checked_at', 'desc')->get();

        $total       = $verifications->count();
        $up          = $verifications->where('is_up', true)->count();
        $uptimePct   = $total > 0 ? round($up / $total * 100, 2) : 100;
        $avgResponse = $verifications->avg('response_time_ms') ?? 0;
        $incidents   = $site->incidents()
            ->where('started_at', '>=', $periodStart)->get();

        $pdf = Pdf::loadView('rapports.pdf', compact(
            'site', 'verifications', 'uptimePct',
            'avgResponse', 'incidents', 'periodStart', 'periodEnd'
        ));

        $rapport = Rapport::create([
            'site_id'         => $site->id,
            'period_start'    => $periodStart->toDateString(),
            'period_end'      => $periodEnd->toDateString(),
            'uptime_pct'      => $uptimePct,
            'incidents_count' => $incidents->count(),
            'avg_response_ms' => round($avgResponse),
            'pdf_path'        => 'rapport_'.$site->id.'_'.now()->format('YmdHis').'.pdf',
            'generated_at'    => now(),
        ]);

        // ═══ AUDIT LOG ═══
        AuditService::log(
            action:      'report_generated',
            category:    'report',
            description: "Génération du rapport PDF pour « {$site->client_name} » — Uptime: {$uptimePct}%, {$incidents->count()} incident(s)",
            model:       $rapport,
            newValues:   [
                'site'            => $site->client_name,
                'uptime_pct'      => $uptimePct,
                'incidents_count' => $incidents->count(),
                'avg_response_ms' => round($avgResponse),
            ]
        );

        return $pdf->download("rapport_{$site->client_name}.pdf");
    }

    public function sendEmail(Request $request, Site $site) {
        $request->validate(['email' => 'required|email']);

        $periodStart = now()->subDays(30);
        $periodEnd   = now();

        $verifications = $site->verifications()
            ->where('checked_at', '>=', $periodStart)
            ->orderBy('checked_at', 'desc')->get();

        $total       = $verifications->count();
        $up          = $verifications->where('is_up', true)->count();
        $uptimePct   = $total > 0 ? round($up / $total * 100, 2) : 100;
        $avgResponse = $verifications->avg('response_time_ms') ?? 0;
        $incidents   = $site->incidents()
            ->where('started_at', '>=', $periodStart)->get();

        $pdf = Pdf::loadView('rapports.pdf', compact(
            'site', 'verifications', 'uptimePct',
            'avgResponse', 'incidents', 'periodStart', 'periodEnd'
        ));

        Mail::raw(
            "Veuillez trouver ci-joint le rapport de disponibilite de {$site->client_name}.\n\n-- MonitorPro | Soft Seven Art",
            function($m) use ($request, $site, $pdf) {
                $m->to($request->email)
                  ->subject("Rapport disponibilite — {$site->client_name}")
                  ->attachData($pdf->output(), "rapport_{$site->client_name}.pdf");
            }
        );

        // ═══ AUDIT LOG ═══
        AuditService::log(
            action:      'report_emailed',
            category:    'report',
            description: "Envoi par email du rapport « {$site->client_name} » vers {$request->email}",
            model:       $site,
            newValues:   [
                'site'        => $site->client_name,
                'email_to'    => $request->email,
                'uptime_pct'  => $uptimePct,
            ]
        );

        return back()->with('success', "Rapport envoyé à {$request->email} !");
    }

    /**
     * Télécharger un rapport existant.
     * Le PDF est régénéré à la volée depuis les données stockées en BDD.
     */
    public function download(Rapport $rapport)
    {
        $site = $rapport->site;

        if (!$site) {
            abort(404, 'Site introuvable pour ce rapport.');
        }

        $periodStart = \Carbon\Carbon::parse($rapport->period_start);
        $periodEnd   = \Carbon\Carbon::parse($rapport->period_end);

        $verifications = $site->verifications()
            ->whereBetween('checked_at', [$periodStart, $periodEnd])
            ->orderBy('checked_at', 'desc')
            ->get();

        $incidents = $site->incidents()
            ->whereBetween('started_at', [$periodStart, $periodEnd])
            ->get();

        $uptimePct   = $rapport->uptime_pct;
        $avgResponse = $rapport->avg_response_ms;

        $pdf = Pdf::loadView('rapports.pdf', compact(
            'site', 'verifications', 'uptimePct',
            'avgResponse', 'incidents', 'periodStart', 'periodEnd', 'rapport'
        ));

        $filename = "rapport_{$site->client_name}_{$rapport->generated_at->format('YmdHis')}.pdf";

        // ═══ AUDIT LOG ═══
        AuditService::log(
            action:      'report_downloaded',
            category:    'report',
            description: "Téléchargement du rapport « {$site->client_name} » du " . $rapport->generated_at->format('d/m/Y à H:i'),
            model:       $rapport,
            newValues:   [
                'site'         => $site->client_name,
                'period_start' => $rapport->period_start,
                'period_end'   => $rapport->period_end,
                'uptime_pct'   => $rapport->uptime_pct,
            ]
        );

        return $pdf->download($filename);
    }
}