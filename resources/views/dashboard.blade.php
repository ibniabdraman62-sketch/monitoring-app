@extends('layouts.monitoring')
@section('title', 'Tableau de bord')
@section('subtitle', 'Vue d\'ensemble de la supervision en temps réel')

@section('content')

{{-- ═══════ KPIs ═══════ --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-label">Total sites</div>
        <div class="kpi-value">{{ $totalSites }}</div>
        <i class="fas fa-globe kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Sites actifs</div>
        <div class="kpi-value">{{ $activeSites }}</div>
        <i class="fas fa-check-circle kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Incidents actifs</div>
        <div class="kpi-value">{{ $incidents }}</div>
        <i class="fas fa-exclamation-triangle kpi-icon"></i>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-label">Disponibilité moyenne</div>
        <div class="kpi-value">{{ $uptimeMoyen }}<span style="font-size:18px;">%</span></div>
        <i class="fas fa-chart-line kpi-icon"></i>
    </div>
</div>

{{-- ═══════ Graphiques ═══════ --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:24px;">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <i class="fas fa-chart-line" style="color:var(--primary);"></i>
                Temps de réponse — 24 heures
            </div>
            <div style="display:flex; gap:6px;">
                <button onclick="setChartType('line')" id="btn-line" class="btn-primary btn-xs">Courbe</button>
                <button onclick="setChartType('bar')" id="btn-bar" class="btn-secondary btn-xs">Barres</button>
            </div>
        </div>
        <canvas id="responseChart" style="max-height:280px;"></canvas>
    </div>

    <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
        <div class="card-title" style="text-align:center;">
            <i class="fas fa-chart-pie" style="color:var(--primary);"></i>
            Disponibilité 24h
        </div>
        <div style="position:relative; width:180px; height:180px;">
            <canvas id="uptimeDonut"></canvas>
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center;">
                <div style="font-size:28px; font-weight:700;
                            color:{{ $uptimeMoyen >= 99 ? 'var(--success)' : ($uptimeMoyen >= 95 ? 'var(--warning)' : 'var(--danger)') }};">
                    {{ $uptimeMoyen }}%
                </div>
                <div class="text-xs text-muted" style="font-weight:600; letter-spacing:1px;">UPTIME</div>
            </div>
        </div>
        <div style="margin-top:14px;">
            <span class="badge {{ $uptimeMoyen >= 99 ? 'badge-success' : ($uptimeMoyen >= 95 ? 'badge-warning' : 'badge-danger') }}">
                @if($uptimeMoyen >= 99) Excellent
                @elseif($uptimeMoyen >= 95) Acceptable
                @else Critique
                @endif
            </span>
        </div>
    </div>
</div>

{{-- ═══════ Disponibilité mois ═══════ --}}
<div class="card mb-24" style="padding:18px 26px; display:flex; align-items:center; gap:20px;
                                background:linear-gradient(135deg, var(--bg-soft), var(--bg-alt));">
    <div style="width:56px; height:56px; background:var(--primary-bg); color:var(--primary);
                border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:24px;">
        <i class="fas fa-calendar-check"></i>
    </div>
    <div style="flex:1;">
        <div class="text-xs" style="color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:1px;">
            Disponibilité globale — {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}
        </div>
        <div style="font-size:30px; font-weight:700; margin-top:2px;
                    color:{{ $uptimeMois >= 99 ? 'var(--success)' : ($uptimeMois >= 95 ? 'var(--warning)' : 'var(--danger)') }};">
            {{ $uptimeMois }}%
        </div>
    </div>
    <div class="text-sm text-muted">Calculé sur {{ now()->daysInMonth }} jours</div>
</div>

{{-- ═══════ HISTOGRAMME 7 JOURS — BARRES LARGES ═══════ --}}
@php
    $siteIds = $sitesStatus->pluck('id');
    $weekData = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = now()->subDays($i);
        $total = \App\Models\Verification::whereIn('site_id', $siteIds)->whereDate('checked_at', $day)->count();
        $up = \App\Models\Verification::whereIn('site_id', $siteIds)->whereDate('checked_at', $day)->where('is_up', true)->count();
        $weekData[] = [
            'x' => $day->locale('fr')->isoFormat('ddd DD/MM'),
            'y' => $total > 0 ? round($up / $total * 100, 1) : 0
        ];
    }
@endphp
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-chart-bar" style="color:var(--primary);"></i>
        Disponibilité globale — 7 derniers jours
    </div>
    <canvas id="weekChart" height="80"></canvas>
</div>

{{-- ═══════ Statut actuel des sites ═══════ --}}
<div class="table-wrapper mb-24">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-server" style="color:var(--primary);"></i>
            Statut actuel des sites
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <span class="text-xs text-muted">
                Actualisation dans <span id="countdown" class="font-mono" style="color:var(--primary); font-weight:700;">30</span> s
            </span>
            <a href="{{ route('sites.create') }}" class="btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>
    </div>
    <div class="table-scroll" style="max-height:520px;">
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">État</th>
                    <th>Client</th>
                    <th>URL</th>
                    <th>HTTP</th>
                    <th>Temps</th>
                    <th>SSL</th>
                    <th>Uptime 24h</th>
                    <th>Vérifié</th>
                    <th style="text-align:center;">Actions</th>
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
                    <td style="font-weight:600; color:var(--text);">{{ $site['client_name'] }}</td>
                    <td>
                        <a href="{{ $site['url'] }}" target="_blank"
                            style="color:var(--primary); text-decoration:none; font-size:12.5px;">
                            {{ Str::limit($site['url'], 32) }}
                        </a>
                    </td>
                    <td>
                        @if($site['http_code'])
                            <span class="badge {{ $site['http_code'] == 200 ? 'badge-success' : 'badge-danger' }} font-mono">
                                {{ $site['http_code'] }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($site['response_time'])
                            <span class="font-mono" style="font-weight:700;
                                color:{{ $site['response_time'] > 2000 ? 'var(--danger)' : ($site['response_time'] > 1000 ? 'var(--warning)' : 'var(--success)') }};">
                                {{ $site['response_time'] }} ms
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($site['ssl_valid'] === null)
                            <span class="text-muted">—</span>
                        @elseif($site['ssl_valid'])
                            <span class="badge badge-success">Valide</span>
                        @else
                            <span class="badge badge-danger">Invalide</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $siteModel = \App\Models\Site::find($site['id']);
                            $tot = $siteModel ? $siteModel->verifications()->where('checked_at', '>=', now()->subDay())->count() : 0;
                            $up  = $siteModel ? $siteModel->verifications()->where('checked_at', '>=', now()->subDay())->where('is_up', true)->count() : 0;
                            $upt = $tot > 0 ? round($up / $tot * 100, 1) : 100;
                            $upColor = $upt >= 99 ? 'var(--success)' : ($upt >= 95 ? 'var(--warning)' : 'var(--danger)');
                        @endphp
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="uptime-bar" style="width:55px;">
                                <div class="uptime-bar-fill" style="width:{{ $upt }}%; background:{{ $upColor }};"></div>
                            </div>
                            <span class="font-mono text-xs" style="font-weight:700; color:{{ $upColor }};">{{ $upt }}%</span>
                        </div>
                    </td>
                    <td class="text-xs text-muted font-mono">{{ $site['checked_at'] }}</td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex; gap:6px;">
                            <a href="{{ route('sites.show', $site['id']) }}" class="btn-primary btn-xs" title="Détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="checkNow({{ $site['id'] }}, this)" class="btn-primary btn-success btn-xs" title="Vérifier">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucun site configuré.
                    <a href="{{ route('sites.create') }}" style="color:var(--primary); font-weight:600;">Ajouter un site</a>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════ BLOC RAPPORT IA GEMINI (RESTAURÉ) ═══════ --}}
@if($aiRapport)
<div class="card mb-24" style="padding:0; overflow:hidden;">
    {{-- Header --}}
    <div style="background:linear-gradient(135deg, var(--primary-dark), var(--primary));
                padding:22px 26px; color:#FFFFFF;
                display:flex; align-items:center; gap:14px;">
        <div style="width:50px; height:50px; background:rgba(255,255,255,0.18);
                    border-radius:12px; display:flex; align-items:center; justify-content:center;
                    font-size:22px;">
            <i class="fas fa-robot"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:17px; font-weight:700;">Rapport d'analyse intelligente — Google Gemini AI</div>
            <div style="font-size:12px; opacity:0.85; margin-top:3px;">
                Généré automatiquement via n8n Automation ·
                @php
                    try { echo \Carbon\Carbon::parse($aiRapport['generated_at'])->format('d/m/Y à H:i'); }
                    catch(\Exception $e) { echo $aiRapport['generated_at'] ?? now()->format('d/m/Y à H:i'); }
                @endphp
            </div>
        </div>
        <div style="display:flex; gap:8px;">
            <span style="background:rgba(74,140,90,0.3); color:#FFFFFF;
                         padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700;">
                ● LIVE
            </span>
            <button onclick="window.print()"
                    style="background:rgba(255,255,255,0.18); color:#FFFFFF; border:1px solid rgba(255,255,255,0.3);
                           padding:5px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    {{-- Info bar --}}
    <div style="background:var(--bg-soft); padding:12px 26px; border-bottom:1px solid var(--border);
                display:flex; gap:24px; flex-wrap:wrap; font-size:12px; color:var(--text-muted);">
        <div><i class="fas fa-brain" style="color:var(--primary);"></i> Modèle : Google Gemini Pro</div>
        <div><i class="fas fa-database" style="color:var(--primary);"></i> Source : API temps réel</div>
        <div><i class="fas fa-clock" style="color:var(--primary);"></i> Fréquence : Toutes les heures</div>
        <div><i class="fas fa-globe" style="color:var(--primary);"></i> Sites analysés : {{ $totalSites }}</div>
    </div>

    {{-- Content --}}
    <div id="ai-rapport-content" style="padding:26px 30px; font-size:13.5px; color:var(--text);
                                         line-height:1.8; max-height:600px; overflow-y:auto;">
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
        rapportEl.innerHTML = '<p>' + html + '</p>';
    }
})();
</script>

<style>
#ai-rapport-content h1 { font-size:18px; font-weight:700; color:var(--text); border-bottom:2px solid var(--primary); padding-bottom:8px; margin:18px 0 10px; }
#ai-rapport-content h2 { font-size:15px; font-weight:700; color:var(--primary-dark); margin:16px 0 8px; padding-left:10px; border-left:3px solid var(--primary); }
#ai-rapport-content h3 { font-size:13.5px; font-weight:700; color:var(--text); margin:14px 0 6px; }
#ai-rapport-content p { margin:7px 0; color:var(--text-secondary); }
#ai-rapport-content ul, #ai-rapport-content ol { padding-left:22px; margin:7px 0; }
#ai-rapport-content li { margin:5px 0; color:var(--text-secondary); }
#ai-rapport-content strong { color:var(--text); font-weight:700; }
#ai-rapport-content hr { border:none; border-top:1px solid var(--border); margin:16px 0; }
</style>
@else
{{-- Placeholder si pas de rapport IA --}}
<div class="card mb-24" style="text-align:center; padding:40px 32px;">
    <div style="width:64px; height:64px;
                background:linear-gradient(135deg, var(--primary), var(--primary-dark));
                color:#FFFFFF; border-radius:14px;
                display:flex; align-items:center; justify-content:center;
                font-size:24px; margin:0 auto 14px;">
        <i class="fas fa-robot"></i>
    </div>
    <div style="font-size:17px; font-weight:700; color:var(--text); margin-bottom:6px;">
        Analyse IA en cours de configuration
    </div>
    <div class="text-sm text-muted" style="max-width:480px; margin:0 auto;">
        Le rapport intelligent Google Gemini sera généré automatiquement toutes les heures via n8n.
    </div>
</div>
@endif

{{-- ═══════ 10 derniers incidents ═══════ --}}
@php
    $recentIncidents = \App\Models\Incident::with('site')
        ->whereIn('site_id', $sitesStatus->pluck('id'))
        ->latest('started_at')->take(10)->get();
@endphp
@if($recentIncidents->count() > 0)
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-history" style="color:var(--primary);"></i>
            10 derniers incidents
        </div>
        <a href="{{ route('incidents.index') }}" class="btn-secondary btn-sm">Voir tout</a>
    </div>
    <div class="table-scroll" style="max-height:420px;">
        <table>
            <thead><tr><th>Site</th><th>Type</th><th>Début</th><th>Durée</th><th>Statut</th></tr></thead>
            <tbody>
            @foreach($recentIncidents as $inc)
            <tr>
                <td style="font-weight:600; color:var(--text);">{{ $inc->site->client_name }}</td>
                <td><span class="badge {{ $inc->type == 'offline' ? 'badge-danger' : 'badge-warning' }}">{{ ucfirst($inc->type) }}</span></td>
                <td class="text-sm font-mono">{{ $inc->started_at->format('d/m/Y H:i') }}</td>
                <td class="font-mono">
                    @if($inc->duration_min) {{ $inc->duration_min }} min
                    @else <span class="text-muted">En cours</span>
                    @endif
                </td>
                <td>
                    @if($inc->resolved_at)
                        <span class="badge badge-success">Résolu</span>
                    @else
                        <span class="badge badge-danger badge-dot">Actif</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
// ─── Chart temps de réponse ────────────────────
const graphData = @json($graphData);
const colors = ['#5B95C4','#4A8C5A','#C48A4A','#B66258','#7B6BAE','#2C5F8B','#C9A876','#65A06A','#A6783A','#8D5F4F'];
let myChart = null;

function buildDatasets(type) {
    return graphData.map((site, i) => ({
        label: site.label,
        data: site.data,
        borderColor: colors[i % colors.length],
        backgroundColor: type === 'bar' ? colors[i % colors.length] + 'AA' : colors[i % colors.length] + '22',
        tension: 0.4,
        fill: type === 'line',
        pointRadius: 2,
        pointHoverRadius: 5,
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
            maintainAspectRatio: false,
            parsing: { xAxisKey: 'x', yAxisKey: 'y' },
            plugins: {
                legend: { position: 'bottom', labels: { color: '#5C4B36', font: { size: 11 }, boxWidth: 10, padding: 12 } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { ticks: { color: '#8B7855', font: { size: 11 } }, grid: { color: '#F0E8D4' } },
                y: { ticks: { color: '#8B7855', font: { size: 11 } }, grid: { color: '#F0E8D4' }, beginAtZero: true, title: { display: true, text: 'Temps (ms)', color: '#8B7855' } }
            }
        }
    });
}

function setChartType(type) {
    createChart(type);
    document.getElementById('btn-line').className = type === 'line' ? 'btn-primary btn-xs' : 'btn-secondary btn-xs';
    document.getElementById('btn-bar').className  = type === 'bar'  ? 'btn-primary btn-xs' : 'btn-secondary btn-xs';
}

createChart('line');

// ─── Donut uptime ──────────────────────────────
new Chart(document.getElementById('uptimeDonut'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $uptimeMoyen }}, {{ 100 - $uptimeMoyen }}],
            backgroundColor: [
                '{{ $uptimeMoyen >= 99 ? "#4A8C5A" : ($uptimeMoyen >= 95 ? "#C48A4A" : "#B66258") }}',
                '#F0E8D4'
            ],
            borderWidth: 0,
        }]
    },
    options: { cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } }, responsive: true, maintainAspectRatio: false }
});

// ─── HISTOGRAMME 7 JOURS — BARRES LARGES (comme avant) ─────
const weekData = @json($weekData);
new Chart(document.getElementById('weekChart'), {
    type: 'bar',
    data: {
        datasets: [{
            label: 'Disponibilité globale (%)',
            data: weekData,
            backgroundColor: weekData.map(d => d.y >= 99 ? '#4A8C5A' : (d.y >= 95 ? '#C48A4A' : '#B66258')),
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        parsing: { xAxisKey: 'x', yAxisKey: 'y' },
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#5C4B36', font: { size: 12, weight: '600' } }, grid: { display: false } },
            y: { min: 0, max: 100, ticks: { color: '#8B7855', callback: v => v + '%' }, grid: { color: '#F0E8D4' } }
        }
    }
});

// ─── Countdown auto-refresh ────────────────────
let countdown = 30;
const el = document.getElementById('countdown');
setInterval(() => {
    countdown--;
    if (el) el.textContent = countdown;
    if (countdown <= 0) location.reload();
}, 1000);

// ─── Check Now ─────────────────────────────────
function checkNow(siteId, btn) {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    fetch('/sites/' + siteId + '/check-now', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(() => {
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; location.reload(); }, 1200);
    })
    .catch(() => { btn.innerHTML = orig; btn.disabled = false; });
}
</script>
@endsection