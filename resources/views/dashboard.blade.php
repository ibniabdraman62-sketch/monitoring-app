@extends('layouts.monitoring')

@section('title', 'Dashboard')
@section('subtitle', 'Vue d\'ensemble en temps réel')

@section('content')

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-label">Total Sites</div>
        <div class="kpi-value">{{ $totalSites }}</div>
        <i class="fas fa-globe kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Sites Actifs</div>
        <div class="kpi-value">{{ $activeSites }}</div>
        <i class="fas fa-check-circle kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Incidents Actifs</div>
        <div class="kpi-value">{{ $incidents }}</div>
        <i class="fas fa-exclamation-triangle kpi-icon"></i>
    </div>
    <div class="kpi-card purple">
        <div class="kpi-label">Uptime Moyen</div>
        <div class="kpi-value">{{ $uptimeMoyen }}%</div>
        <i class="fas fa-chart-line kpi-icon"></i>
    </div>
</div>

<!-- Graphique + Donut -->
<div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:24px;">

    <!-- Graphique temps de réponse -->
    <div class="card">
        <div class="card-title">📈 Temps de réponse — 24 dernières heures</div>
        <canvas id="responseChart" height="120"></canvas>
    </div>

    <!-- Donut uptime -->
    <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
        <div class="card-title" style="text-align:center;">🎯 Disponibilité</div>
        <canvas id="uptimeDonut" width="160" height="160"></canvas>
        <div style="text-align:center; margin-top:12px;">
            <div style="font-size:32px; font-weight:800; color:#10B981;">{{ $uptimeMoyen }}%</div>
            <div style="font-size:12px; color:#6B7280; margin-top:4px;">Uptime 24h</div>
        </div>
    </div>
</div>

<!-- Tableau statut sites -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:600; color:#fff;">
            🌐 Statut actuel des sites
        </div>
        <a href="{{ route('sites.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Ajouter un site
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Statut</th>
                <th>Client</th>
                <th>URL</th>
                <th>HTTP</th>
                <th>Temps réponse</th>
                <th>SSL</th>
                <th>Dernière vérif.</th>
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
                <td style="font-weight:600; color:#fff;">{{ $site['client_name'] }}</td>
                <td>
                    <a href="{{ $site['url'] }}" target="_blank"
                       style="color:#818CF8; text-decoration:none; font-size:12px;">
                        {{ $site['url'] }}
                    </a>
                </td>
                <td>
                    @if($site['http_code'])
                        <span class="badge {{ $site['http_code'] == 200 ? 'badge-green' : 'badge-red' }}">
                            {{ $site['http_code'] }}
                        </span>
                    @else
                        <span class="badge badge-gray">—</span>
                    @endif
                </td>
                <td>
                    @if($site['response_time'])
                        <span style="color: {{ $site['response_time'] > 2000 ? '#EF4444' : ($site['response_time'] > 1000 ? '#F59E0B' : '#10B981') }}; font-weight:600;">
                            {{ $site['response_time'] }} ms
                        </span>
                    @else
                        <span style="color:#6B7280;">—</span>
                    @endif
                </td>
                <td>
                    @if($site['ssl_valid'] === null)
                        <span style="color:#6B7280;">—</span>
                    @elseif($site['ssl_valid'])
                        <span class="ssl-ok">🔒 Valide</span>
                    @else
                        <span class="ssl-danger">🔓 Invalide</span>
                    @endif
                </td>
                <td style="color:#6B7280; font-size:12px;">{{ $site['checked_at'] }}</td>
                <td>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('sites.show', $site['id']) }}" class="btn-primary" style="padding:5px 10px; font-size:11px;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="checkNow({{ $site['id'] }})" class="btn-primary btn-success" style="padding:5px 10px; font-size:11px;">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; padding:40px; color:#6B7280;">
                    Aucun site surveillé.
                    <a href="{{ route('sites.create') }}" style="color:#818CF8;">Ajouter un site</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
// Graphique temps de réponse
const graphData = @json($graphData);
const colors = ['#4F46E5','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899'];
new Chart(document.getElementById('responseChart'), {
    type: 'line',
    data: {
        datasets: graphData.map((site, i) => ({
            label: site.label,
            data: site.data,
            borderColor: colors[i % colors.length],
            backgroundColor: colors[i % colors.length] + '20',
            tension: 0.4, fill: true, pointRadius: 3,
        }))
    },
    options: {
        responsive: true,
        parsing: { xAxisKey: 'x', yAxisKey: 'y' },
        plugins: { legend: { labels: { color: '#9CA3AF' } } },
        scales: {
            x: { ticks: { color: '#6B7280' }, grid: { color: '#1E2235' } },
            y: { ticks: { color: '#6B7280' }, grid: { color: '#1E2235' }, beginAtZero: true }
        }
    }
});

// Donut uptime
new Chart(document.getElementById('uptimeDonut'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $uptimeMoyen }}, {{ 100 - $uptimeMoyen }}],
            backgroundColor: ['#10B981', '#1E2235'],
            borderWidth: 0,
        }]
    },
    options: {
        cutout: '75%',
        plugins: { legend: { display: false }, tooltip: { enabled: false } }
    }
});

// Auto-refresh toutes les 30 secondes
setTimeout(() => location.reload(), 30000);

// Vérification instantanée
function checkNow(siteId) {
    fetch(`/sites/${siteId}/check-now`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(`✅ Vérification effectuée !\nStatut : ${data.is_up ? 'EN LIGNE' : 'HORS LIGNE'}\nTemps : ${data.response_time} ms`);
        location.reload();
    })
    .catch(() => alert('Erreur lors de la vérification'));
}
</script>

@endsection