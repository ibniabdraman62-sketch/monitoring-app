@extends('layouts.monitoring')
@section('title', 'Dashboard')
@section('subtitle', 'Vue d\'ensemble en temps réel')

@section('content')

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-label">TOTAL SITES</div>
        <div class="kpi-value">{{ $totalSites }}</div>
        <i class="fas fa-globe kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">SITES ACTIFS</div>
        <div class="kpi-value">{{ $activeSites }}</div>
        <i class="fas fa-check-circle kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">INCIDENTS ACTIFS</div>
        <div class="kpi-value">{{ $incidents }}</div>
        <i class="fas fa-exclamation-triangle kpi-icon"></i>
    </div>
    <div class="kpi-card purple">
        <div class="kpi-label">UPTIME MOYEN</div>
        <div class="kpi-value">{{ $uptimeMoyen }}%</div>
        <i class="fas fa-chart-line kpi-icon"></i>
    </div>
</div>

<!-- Graphiques -->
<div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:24px;">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div class="card-title" style="margin:0;">📈 Temps de réponse — 24h</div>
            <div style="display:flex; gap:8px;">
                <button onclick="setChartType('line')" id="btn-line"
                        style="padding:4px 12px; border-radius:6px; border:none; cursor:pointer;
                               background:#1697C2; color:#fff; font-size:11px; font-weight:700;">Courbe</button>
                <button onclick="setChartType('bar')" id="btn-bar"
                        style="padding:4px 12px; border-radius:6px; border:1px solid #CBD5E1;
                               background:#fff; color:#64748B; font-size:11px; font-weight:700; cursor:pointer;">Barres</button>
            </div>
        </div>
        <canvas id="responseChart" height="110"></canvas>
    </div>

    <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
        <div class="card-title" style="text-align:center; width:100%;">🎯 Disponibilité 24h</div>
        <div style="position:relative; width:160px; height:160px;">
            <canvas id="uptimeDonut"></canvas>
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center;">
                <div style="font-size:26px; font-weight:900;
                            color:{{ $uptimeMoyen >= 99 ? '#10B981' : ($uptimeMoyen >= 95 ? '#D97706' : '#EF4444') }}">
                    {{ $uptimeMoyen }}%
                </div>
                <div style="font-size:10px; color:#64748B; font-weight:700;">UPTIME</div>
            </div>
        </div>
        <div style="margin-top:12px; text-align:center;">
            <div style="font-size:12px; font-weight:700;
                        color:{{ $uptimeMoyen >= 99 ? '#10B981' : ($uptimeMoyen >= 95 ? '#D97706' : '#EF4444') }}">
                @if($uptimeMoyen >= 99) ✅ Excellent
                @elseif($uptimeMoyen >= 95) ⚠️ Acceptable
                @else 🔴 Critique
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Disponibilité mois en cours -->
<div class="card" style="margin-bottom:24px; padding:16px 24px;
     display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#F0F9FF,#E0F2FE);">
    <i class="fas fa-calendar-check" style="font-size:28px; color:#1697C2;"></i>
    <div>
        <div style="font-size:11px; color:#64748B; font-weight:700; text-transform:uppercase;">
            Disponibilité globale — mois en cours ({{ now()->format('F Y') }})
        </div>
        <div style="font-size:28px; font-weight:900;
            color:{{ $uptimeMois >= 99 ? '#059669' : ($uptimeMois >= 95 ? '#D97706' : '#DC2626') }}">
            {{ $uptimeMois }}%
        </div>
    </div>
    <div style="margin-left:auto; text-align:right;">
        <div style="font-size:12px; color:#64748B;">Calculé sur {{ now()->daysInMonth }} jours</div>
    </div>
</div>

<!-- Histogramme disponibilité 7 derniers jours -->
@php
    $siteIds = $sitesStatus->pluck('id');
    $weekData = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = now()->subDays($i);
        $total = \App\Models\Verification::whereIn('site_id', $siteIds)
            ->whereDate('checked_at', $day)->count();
        $up = \App\Models\Verification::whereIn('site_id', $siteIds)
            ->whereDate('checked_at', $day)->where('is_up', true)->count();
        $weekData[] = [
            'x' => $day->format('D d/m'),
            'y' => $total > 0 ? round($up / $total * 100, 1) : 0
        ];
    }
@endphp
<div class="card" style="margin-bottom:24px;">
    <div class="card-title">📊 Disponibilité globale — 7 derniers jours</div>
    <canvas id="weekChart" height="80"></canvas>
</div>

<!-- Tableau statut -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            🌐 Statut actuel des sites
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <span style="font-size:12px; color:#64748B;">Auto-refresh dans <span id="countdown">30</span>s</span>
            <a href="{{ route('sites.create') }}" class="btn-primary" style="padding:7px 14px; font-size:12px;">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Statut</th>
                <th>Client</th>
                <th>URL</th>
                <th>HTTP</th>
                <th>Temps</th>
                <th>SSL</th>
                <th>Uptime 24h</th>
                <th>Vérifié</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sitesStatus as $site)
            <tr>
                <td>
                    @if($site['is_up'] === null)
                        <span class="status-dot unknown"></span>
                    @elseif($site['is_up'])
                        <span class="status-dot online"></span>
                    @else
                        <span class="status-dot offline"></span>
                    @endif
                </td>
                <td style="font-weight:700; color:#0C3547; font-size:13px;">
                    {{ $site['client_name'] }}
                </td>
                <td>
                    <a href="{{ $site['url'] }}" target="_blank"
                       style="color:#1697C2; text-decoration:none; font-size:12px;">
                        {{ Str::limit($site['url'], 30) }}
                    </a>
                </td>
                <td>
                    @if($site['http_code'])
                        <span class="badge {{ $site['http_code'] == 200 ? 'badge-green' : 'badge-red' }}">
                            {{ $site['http_code'] }}
                        </span>
                    @else
                        <span style="color:#94A3B8;">—</span>
                    @endif
                </td>
                <td>
                    @if($site['response_time'])
                        <span style="font-weight:700;
                            color:{{ $site['response_time'] > 2000 ? '#DC2626' : ($site['response_time'] > 1000 ? '#D97706' : '#059669') }}">
                            {{ $site['response_time'] }} ms
                        </span>
                    @else
                        <span style="color:#94A3B8;">—</span>
                    @endif
                </td>
                <td>
                    @if($site['ssl_valid'] === null)
                        <span style="color:#94A3B8;">—</span>
                    @elseif($site['ssl_valid'])
                        <span style="color:#059669; font-weight:700;">🔒 OK</span>
                    @else
                        <span style="color:#DC2626; font-weight:700;">🔓 KO</span>
                    @endif
                </td>
                <td>
                    @php
                        $siteModel = App\Models\Site::find($site['id']);
                        $tot = $siteModel->verifications()->where('checked_at', '>=', now()->subDay())->count();
                        $up  = $siteModel->verifications()->where('checked_at', '>=', now()->subDay())->where('is_up', true)->count();
                        $upt = $tot > 0 ? round($up / $tot * 100, 1) : 100;
                    @endphp
                    <div style="display:flex; align-items:center; gap:6px;">
                        <div class="uptime-bar" style="width:60px;">
                            <div class="uptime-bar-fill"
                                 style="width:{{ $upt }}%;
                                        background:{{ $upt >= 99 ? '#10B981' : ($upt >= 95 ? '#D97706' : '#EF4444') }}">
                            </div>
                        </div>
                        <span style="font-size:11px; font-weight:700;
                                     color:{{ $upt >= 99 ? '#059669' : ($upt >= 95 ? '#D97706' : '#DC2626') }}">
                            {{ $upt }}%
                        </span>
                    </div>
                </td>
                <td style="font-size:11px; color:#64748B; font-weight:600;">
                    {{ $site['checked_at'] }}
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('sites.show', $site['id']) }}"
                           class="btn-primary" style="padding:5px 10px; font-size:11px;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="checkNow({{ $site['id'] }}, this)"
                                class="btn-primary btn-success" style="padding:5px 10px; font-size:11px;">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:40px; color:#64748B;">
                    Aucun site. <a href="{{ route('sites.create') }}" style="color:#1697C2;">Ajouter un site</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Graphique temps de réponse 24h ──────────────────────────────────────────
const graphData = @json($graphData);
const colors = ['#1697C2','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899'];
let myChart = null;

function buildDatasets(type) {
    return graphData.map((site, i) => ({
        label: site.label,
        data: site.data,
        borderColor: colors[i % colors.length],
        backgroundColor: type === 'bar'
            ? colors[i % colors.length] + '99'
            : colors[i % colors.length] + '20',
        tension: 0.4,
        fill: type === 'line',
        pointRadius: 3,
        borderWidth: 2,
    }));
}

function createChart(type) {
    if (myChart) myChart.destroy();
    myChart = new Chart(document.getElementById('responseChart'), {
        type: type,
        data: { datasets: buildDatasets(type) },
        options: {
            responsive: true,
            parsing: { xAxisKey: 'x', yAxisKey: 'y' },
            plugins: {
                legend: { labels: { color: '#334155', font: { weight: '600' } } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { ticks: { color: '#64748B' }, grid: { color: '#F1F5F9' } },
                y: {
                    ticks: { color: '#64748B' },
                    grid: { color: '#F1F5F9' },
                    beginAtZero: true,
                    title: { display: true, text: 'Temps (ms)', color: '#64748B' }
                }
            }
        }
    });
}

function setChartType(type) {
    createChart(type);
    document.getElementById('btn-line').style.background  = type === 'line' ? '#1697C2' : '#fff';
    document.getElementById('btn-line').style.color       = type === 'line' ? '#fff' : '#64748B';
    document.getElementById('btn-line').style.border      = type === 'line' ? 'none' : '1px solid #CBD5E1';
    document.getElementById('btn-bar').style.background   = type === 'bar'  ? '#1697C2' : '#fff';
    document.getElementById('btn-bar').style.color        = type === 'bar'  ? '#fff' : '#64748B';
    document.getElementById('btn-bar').style.border       = type === 'bar'  ? 'none' : '1px solid #CBD5E1';
}

createChart('line');

// ── Donut uptime ─────────────────────────────────────────────────────────────
new Chart(document.getElementById('uptimeDonut'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $uptimeMoyen }}, {{ 100 - $uptimeMoyen }}],
            backgroundColor: [
                '{{ $uptimeMoyen >= 99 ? "#10B981" : ($uptimeMoyen >= 95 ? "#D97706" : "#EF4444") }}',
                '#E0F2FE'
            ],
            borderWidth: 0,
        }]
    },
    options: {
        cutout: '75%',
        plugins: { legend: { display: false }, tooltip: { enabled: false } }
    }
});

// ── Histogramme 7 jours ───────────────────────────────────────────────────────
const weekData = @json($weekData);
new Chart(document.getElementById('weekChart'), {
    type: 'bar',
    data: {
        datasets: [{
            label: 'Disponibilité globale (%)',
            data: weekData,
            backgroundColor: weekData.map(d => d.y >= 99 ? '#10B981' : d.y >= 95 ? '#D97706' : '#EF4444'),
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        parsing: { xAxisKey: 'x', yAxisKey: 'y' },
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#64748B' }, grid: { display: false } },
            y: {
                min: 0, max: 100,
                ticks: { color: '#64748B', callback: v => v + '%' },
                grid: { color: '#F1F5F9' }
            }
        }
    }
});

// ── Countdown auto-refresh ────────────────────────────────────────────────────
let countdown = 30;
const el = document.getElementById('countdown');
setInterval(() => {
    countdown--;
    if (el) el.textContent = countdown;
    if (countdown <= 0) location.reload();
}, 1000);

// ── Check Now ─────────────────────────────────────────────────────────────────
function checkNow(siteId, btn) {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    fetch(`/sites/${siteId}/check-now`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(() => {
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; location.reload(); }, 1500);
    })
    .catch(() => { btn.innerHTML = orig; btn.disabled = false; });
}


</script>

<!-- ===== RAPPORT IA GEMINI ===== -->
@if($aiRapport)
<div style="margin-top:24px;">
    <div style="background:linear-gradient(135deg, #0C3547 0%, #1697C2 100%);
                border-radius:16px 16px 0 0; padding:22px 28px;
                display:flex; align-items:center; gap:16px;">
        <div style="width:50px; height:50px; background:rgba(255,255,255,0.15);
                    border-radius:12px; display:flex; align-items:center;
                    justify-content:center; font-size:26px; flex-shrink:0;">🤖</div>
        <div style="flex:1;">
            <div style="font-size:18px; font-weight:800; color:#fff;">
                Rapport d'Analyse Intelligente — Google Gemini AI
            </div>
            <div style="font-size:12px; color:rgba(255,255,255,0.65); margin-top:4px;">
                Généré automatiquement via n8n Automation ·
                @php
                    try { echo \Carbon\Carbon::parse($aiRapport['generated_at'])->format('d/m/Y à H:i'); }
                    catch(\Exception $e) { echo $aiRapport['generated_at'] ?? now()->format('d/m/Y à H:i'); }
                @endphp
            </div>
        </div>
        <div style="display:flex; gap:8px; flex-shrink:0;">
            <span style="background:rgba(16,185,129,0.25); color:#6EE7B7;
                         border:1px solid rgba(16,185,129,0.4);
                         padding:5px 14px; border-radius:20px; font-size:11px; font-weight:700;">● LIVE</span>
            <span style="background:rgba(255,255,255,0.15); color:#fff;
                         padding:5px 14px; border-radius:20px; font-size:11px; font-weight:700;">Intelligence Artificielle</span>
        </div>
    </div>
    <div style="background:#F0F9FF; padding:12px 28px; border-left:1px solid #E0F2FE;
                border-right:1px solid #E0F2FE; border-bottom:1px solid #E0F2FE;
                display:flex; align-items:center; gap:28px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:7px;">
            <i class="fas fa-robot" style="color:#1697C2; font-size:13px;"></i>
            <span style="font-size:12px; font-weight:600; color:#1697C2;">Modèle : Google Gemini Pro</span>
        </div>
        <div style="display:flex; align-items:center; gap:7px;">
            <i class="fas fa-database" style="color:#64748B; font-size:13px;"></i>
            <span style="font-size:12px; font-weight:600; color:#64748B;">Source : API MonitorPro temps réel</span>
        </div>
        <div style="display:flex; align-items:center; gap:7px;">
            <i class="fas fa-clock" style="color:#64748B; font-size:13px;"></i>
            <span style="font-size:12px; font-weight:600; color:#64748B;">Fréquence : Toutes les heures</span>
        </div>
        <div style="display:flex; align-items:center; gap:7px;">
            <i class="fas fa-globe" style="color:#64748B; font-size:13px;"></i>
            <span style="font-size:12px; font-weight:600; color:#64748B;">Sites analysés : {{ $totalSites }}</span>
        </div>
        <div style="margin-left:auto;">
            <button onclick="window.print()"
                    style="background:linear-gradient(135deg,#1697C2,#4BC3EB); color:#fff;
                           border:none; padding:7px 16px; border-radius:8px;
                           font-size:11px; font-weight:700; cursor:pointer;">
                <i class="fas fa-print"></i> Imprimer le rapport
            </button>
        </div>
    </div>
    <div style="background:#fff; border:1px solid #E0F2FE; border-top:none;
                border-radius:0 0 16px 16px; overflow:hidden;">
        <div id="ai-rapport-content"
             style="padding:28px 32px; font-size:13.5px; color:#1e293b;
                    line-height:1.9; max-height:620px; overflow-y:auto;">
        </div>
    </div>
</div>

<script>
(function() {
    const rapportText = @json($aiRapport['rapport'] ?? '');
    const rapportEl = document.getElementById('ai-rapport-content');
    if (rapportEl && rapportText) {
        let html = rapportText
            .replace(/^#### (.+)$/gm, '<h3>$1</h3>')
            .replace(/^### (.+)$/gm, '<h3>$1</h3>')
            .replace(/^## (.+)$/gm, '<h2>$1</h2>')
            .replace(/^# (.+)$/gm, '<h1>$1</h1>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/^---$/gm, '<hr>')
            .replace(/^\*   (.+)$/gm, '<li style="margin-left:24px">$1</li>')
            .replace(/^\*  (.+)$/gm, '<li style="margin-left:12px">$1</li>')
            .replace(/^\* (.+)$/gm, '<li>$1</li>')
            .replace(/^(\d+)\.\s+(.+)$/gm, '<li>$2</li>')
            .replace(/\n\n/g, '</p><p>')
            .replace(/\n/g, '<br>');
        rapportEl.innerHTML = html;
    }
})();
</script>

<style>
#ai-rapport-content h1 { font-size:20px; font-weight:800; color:#0C3547; border-bottom:3px solid #1697C2; padding-bottom:10px; margin:24px 0 14px; }
#ai-rapport-content h2 { font-size:16px; font-weight:700; color:#1697C2; margin:20px 0 10px; padding-left:12px; border-left:4px solid #1697C2; }
#ai-rapport-content h3 { font-size:14px; font-weight:700; color:#0C3547; margin:16px 0 8px; }
#ai-rapport-content p { margin:8px 0; color:#334155; }
#ai-rapport-content ul, #ai-rapport-content ol { padding-left:24px; margin:8px 0; }
#ai-rapport-content li { margin:6px 0; color:#334155; }
#ai-rapport-content strong { color:#0C3547; font-weight:700; }
#ai-rapport-content hr { border:none; border-top:1px solid #E0F2FE; margin:20px 0; }
#ai-rapport-content blockquote { border-left:4px solid #1697C2; margin:12px 0; padding:10px 16px; background:#F0F9FF; border-radius:0 8px 8px 0; color:#1697C2; font-weight:600; }
</style>

@else
<div style="margin-top:24px; background:linear-gradient(135deg, #F0F9FF, #E0F2FE);
            border:1px solid #BAE6FD; border-radius:16px; padding:48px 32px; text-align:center;">
    <div style="width:72px; height:72px; background:linear-gradient(135deg,#1697C2,#53EAFD);
                border-radius:18px; display:flex; align-items:center; justify-content:center;
                font-size:32px; margin:0 auto 20px; box-shadow:0 8px 24px rgba(22,151,194,0.3);">🤖</div>
    <div style="font-size:20px; font-weight:800; color:#0C3547; margin-bottom:10px;">Analyse IA en cours de configuration</div>
    <div style="font-size:13px; color:#64748B; max-width:480px; margin:0 auto 24px; line-height:1.7;">
        Le rapport intelligent Google Gemini sera généré automatiquement toutes les heures via n8n.
    </div>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <span style="background:#fff; color:#1697C2; border:1px solid #BAE6FD; padding:7px 18px; border-radius:20px; font-size:12px; font-weight:700;"><i class="fas fa-robot"></i> Google Gemini Pro</span>
        <span style="background:#fff; color:#1697C2; border:1px solid #BAE6FD; padding:7px 18px; border-radius:20px; font-size:12px; font-weight:700;"><i class="fas fa-project-diagram"></i> n8n Automation</span>
        <span style="background:#fff; color:#1697C2; border:1px solid #BAE6FD; padding:7px 18px; border-radius:20px; font-size:12px; font-weight:700;"><i class="fas fa-clock"></i> Toutes les heures</span>
    </div>
</div>
@endif

<!-- Fil des 10 derniers incidents -->
@php
    $recentIncidents = \App\Models\Incident::with('site')
        ->whereIn('site_id', $sitesStatus->pluck('id'))
        ->latest('started_at')->take(10)->get();
@endphp
@if($recentIncidents->count() > 0)
<div class="table-wrapper" style="margin-top:24px;">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            ⚠️ 10 derniers incidents
        </div>
        <a href="{{ route('incidents.index') }}" class="btn-primary" style="padding:6px 14px; font-size:12px;">
            Voir tout
        </a>
    </div>
    <table>
        <thead>
            <tr><th>Site</th><th>Type</th><th>Début</th><th>Durée</th><th>Statut</th></tr>
        </thead>
        <tbody>
        @foreach($recentIncidents as $inc)
        <tr>
            <td style="font-weight:700; color:#0C3547;">{{ $inc->site->client_name }}</td>
            <td><span class="badge {{ $inc->type == 'offline' ? 'badge-red' : 'badge-yellow' }}">{{ strtoupper($inc->type) }}</span></td>
            <td style="font-size:11px; color:#64748B;">{{ $inc->started_at->format('d/m/Y H:i') }}</td>
            <td style="font-weight:600; color:#D97706;">{{ $inc->duration_min ? $inc->duration_min.'min' : 'En cours' }}</td>
            <td>
                @if($inc->resolved_at)
                    <span class="badge badge-green">✅ Résolu</span>
                @else
                    <span class="badge badge-red">🔴 Actif</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection