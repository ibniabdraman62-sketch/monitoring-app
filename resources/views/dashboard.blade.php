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
                        $tot = $siteModel->verifications()->where('created_at','>=',now()->subDay())->count();
                        $up = $siteModel->verifications()->where('created_at','>=',now()->subDay())->where('is_up',true)->count();
                        $upt = $tot > 0 ? round($up/$tot*100,1) : 100;
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
        tension: 0.4, fill: type === 'line', pointRadius: 3,
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
                y: { ticks: { color: '#64748B' }, grid: { color: '#F1F5F9' }, beginAtZero: true,
                     title: { display: true, text: 'Temps (ms)', color: '#64748B' } }
            }
        }
    });
}

function setChartType(type) {
    createChart(type);
    document.getElementById('btn-line').style.background = type === 'line' ? '#1697C2' : '#fff';
    document.getElementById('btn-line').style.color = type === 'line' ? '#fff' : '#64748B';
    document.getElementById('btn-line').style.border = type === 'line' ? 'none' : '1px solid #CBD5E1';
    document.getElementById('btn-bar').style.background = type === 'bar' ? '#1697C2' : '#fff';
    document.getElementById('btn-bar').style.color = type === 'bar' ? '#fff' : '#64748B';
    document.getElementById('btn-bar').style.border = type === 'bar' ? 'none' : '1px solid #CBD5E1';
}

createChart('line');

// Donut
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

// Countdown refresh
let countdown = 30;
const el = document.getElementById('countdown');
setInterval(() => {
    countdown--;
    if (el) el.textContent = countdown;
    if (countdown <= 0) location.reload();
}, 1000);

// Check Now
function checkNow(siteId, btn) {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    fetch(`/sites/${siteId}/check-now`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; location.reload(); }, 1500);
    })
    .catch(() => { btn.innerHTML = orig; btn.disabled = false; });
}
</script>

@endsection