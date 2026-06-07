@extends('layouts.app')

@section('content')
<div class="stats-page">

    {{-- ═══ HEADER ═══ --}}
    <div class="stats-header">
        <div>
            <h1 class="stats-title">
                <span class="stats-title-icon">📊</span> Statistiques globales
            </h1>
            <p class="stats-subtitle">Vue exécutive du parc — données consolidées sur 30 jours</p>
        </div>
        <div class="stats-period-badge">
            <span class="stats-period-dot"></span>
            {{ now()->subDays(30)->format('d/m/Y') }} → {{ now()->format('d/m/Y') }}
        </div>
    </div>

    {{-- ═══ ALERTE SITES DOWN ═══ --}}
    @if($sitesDown->isNotEmpty())
        <div class="stats-alert">
            <span class="stats-alert-icon">⚠️</span>
            <div>
                <strong>{{ $sitesDown->count() }} site(s) actuellement indisponible(s)</strong>
                <div class="stats-alert-list">
                    @foreach($sitesDown as $s)
                        <a href="{{ route('sites.show', $s) }}">{{ $s->client_name ?? $s->name ?? $s->url }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ KPI ROW 1 ═══ --}}
    <div class="stats-kpi-grid">
        <div class="stats-kpi stats-kpi--gold">
            <div class="stats-kpi-label">Sites monitorés</div>
            <div class="stats-kpi-value">{{ $kpis['sites_total'] }}</div>
            <div class="stats-kpi-hint">{{ $kpis['sites_actifs'] }} actifs</div>
        </div>
        <div class="stats-kpi stats-kpi--success">
            <div class="stats-kpi-label">Uptime moyen (30j)</div>
            <div class="stats-kpi-value">{{ $kpis['uptime_moyen'] }}%</div>
            <div class="stats-kpi-hint">disponibilité globale</div>
        </div>
        <div class="stats-kpi stats-kpi--warning">
            <div class="stats-kpi-label">Incidents ce mois</div>
            <div class="stats-kpi-value">{{ $kpis['incidents_mois'] }}</div>
            <div class="stats-kpi-hint">depuis le {{ now()->startOfMonth()->format('d/m') }}</div>
        </div>
        <div class="stats-kpi stats-kpi--info">
            <div class="stats-kpi-label">Alertes envoyées</div>
            <div class="stats-kpi-value">{{ $kpis['alertes_mois'] }}</div>
            <div class="stats-kpi-hint">ce mois</div>
        </div>
    </div>

    {{-- ═══ KPI ROW 2 ═══ --}}
    <div class="stats-kpi-grid">
        <div class="stats-kpi">
            <div class="stats-kpi-label">Clients</div>
            <div class="stats-kpi-value">{{ $kpis['clients'] }}</div>
            <div class="stats-kpi-hint">comptes actifs</div>
        </div>
        <div class="stats-kpi">
            <div class="stats-kpi-label">Agents</div>
            <div class="stats-kpi-value">{{ $kpis['agents'] }}</div>
            <div class="stats-kpi-hint">opérateurs</div>
        </div>
        <div class="stats-kpi">
            <div class="stats-kpi-label">Rapports générés</div>
            <div class="stats-kpi-value">{{ $kpis['rapports_mois'] }}</div>
            <div class="stats-kpi-hint">ce mois</div>
        </div>
        <div class="stats-kpi">
            <div class="stats-kpi-label">Vérifications totales</div>
            <div class="stats-kpi-value">{{ number_format($kpis['verifs_total'], 0, ',', ' ') }}</div>
            <div class="stats-kpi-hint">all-time</div>
        </div>
    </div>

    {{-- ═══ CHARTS ROW 1 ═══ --}}
    <div class="stats-charts-row">
        <div class="stats-card stats-card--wide">
            <div class="stats-card-header">
                <h3>📈 Évolution du taux de disponibilité</h3>
                <span class="stats-card-badge">30 jours</span>
            </div>
            <canvas id="chartUptime" height="100"></canvas>
        </div>
        <div class="stats-card">
            <div class="stats-card-header">
                <h3>🚨 Incidents par type</h3>
                <span class="stats-card-badge">30 jours</span>
            </div>
            <canvas id="chartIncidents" height="180"></canvas>
        </div>
    </div>

    {{-- ═══ CHARTS ROW 2 ═══ --}}
    <div class="stats-charts-row">
        <div class="stats-card stats-card--wide">
            <div class="stats-card-header">
                <h3>⚡ Activité des vérifications</h3>
                <span class="stats-card-badge">7 jours</span>
            </div>
            <canvas id="chartActivite" height="100"></canvas>
        </div>
        <div class="stats-card">
            <div class="stats-card-header">
                <h3>👥 Utilisateurs par rôle</h3>
            </div>
            <canvas id="chartUsers" height="180"></canvas>
        </div>
    </div>

    {{-- ═══ TABLES TOP SITES ═══ --}}
    <div class="stats-tables-row">
        <div class="stats-card">
            <div class="stats-card-header">
                <h3>🔴 Sites les plus instables</h3>
                <span class="stats-card-badge">30 jours</span>
            </div>
            @if($topInstables->isEmpty())
                <div class="stats-empty">Aucun incident enregistré 🎉</div>
            @else
                <table class="stats-table">
                    <thead>
                        <tr><th>#</th><th>Site</th><th class="stats-num">Incidents</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topInstables as $i => $s)
                            <tr>
                                <td><span class="stats-rank stats-rank--bad">{{ $i + 1 }}</span></td>
                                <td>
                                    <a href="{{ route('sites.show', $s) }}" class="stats-link">
                                        {{ $s->client_name ?? $s->name ?? $s->url }}
                                    </a>
                                </td>
                                <td class="stats-num"><strong class="stats-danger">{{ $s->incidents_count }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="stats-card">
            <div class="stats-card-header">
                <h3>🟢 Sites les plus fiables</h3>
                <span class="stats-card-badge">30 jours</span>
            </div>
            @if($topFiables->isEmpty())
                <div class="stats-empty">Aucune donnée</div>
            @else
                <table class="stats-table">
                    <thead>
                        <tr><th>#</th><th>Site</th><th class="stats-num">Uptime</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topFiables as $i => $s)
                            <tr>
                                <td><span class="stats-rank stats-rank--good">{{ $i + 1 }}</span></td>
                                <td>
                                    <a href="{{ route('sites.show', $s) }}" class="stats-link">
                                        {{ $s->client_name ?? $s->name ?? $s->url }}
                                    </a>
                                </td>
                                <td class="stats-num"><strong class="stats-success">{{ $s->uptime_pct }}%</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ═══ DERNIERS INCIDENTS ═══ --}}
    <div class="stats-card">
        <div class="stats-card-header">
            <h3>🕐 Derniers incidents</h3>
            <span class="stats-card-badge">5 plus récents</span>
        </div>
        @if($derniersIncidents->isEmpty())
            <div class="stats-empty">Aucun incident à signaler ✨</div>
        @else
            <table class="stats-table">
                <thead>
                    <tr><th>Site</th><th>Type</th><th>Début</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    @foreach($derniersIncidents as $inc)
                        <tr>
                            <td>
                                <a href="{{ route('sites.show', $inc->site) }}" class="stats-link">
                                    {{ $inc->site->client_name ?? $inc->site->name ?? $inc->site->url ?? 'Site supprimé' }}
                                </a>
                            </td>
                            <td><span class="stats-chip">{{ ucfirst($inc->type ?? 'incident') }}</span></td>
                            <td>{{ optional($inc->started_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($inc->resolved_at ?? $inc->ended_at ?? false)
                                    <span class="stats-badge stats-badge--success">Résolu</span>
                                @else
                                    <span class="stats-badge stats-badge--danger">En cours</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

{{-- ═══ STYLES ═══ --}}
<style>
    .stats-page { padding: 24px; max-width: 1400px; margin: 0 auto; }

    .stats-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
    }
    .stats-title {
        font-size: 28px; font-weight: 800; color: #1E3A5F;
        margin: 0; display: flex; align-items: center; gap: 12px;
    }
    .stats-title-icon { font-size: 32px; }
    .stats-subtitle { color: #64748b; margin: 4px 0 0; font-size: 14px; }
    .stats-period-badge {
        background: linear-gradient(135deg, #1E3A5F, #2c4a73);
        color: #D4A857; padding: 10px 18px; border-radius: 999px;
        font-size: 13px; font-weight: 600;
        display: flex; align-items: center; gap: 10px;
        box-shadow: 0 4px 12px rgba(30,58,95,0.2);
    }
    .stats-period-dot {
        width: 8px; height: 8px; background: #D4A857; border-radius: 50%;
        box-shadow: 0 0 8px #D4A857; animation: stats-pulse 2s infinite;
    }
    @keyframes stats-pulse {
        0%,100% { opacity: 1; } 50% { opacity: 0.4; }
    }

    .stats-alert {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border-left: 4px solid #ef4444; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 24px;
        display: flex; gap: 14px; align-items: flex-start;
    }
    .stats-alert-icon { font-size: 24px; }
    .stats-alert strong { color: #991b1b; display: block; margin-bottom: 6px; }
    .stats-alert-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .stats-alert-list a {
        background: rgba(239,68,68,0.1); color: #991b1b;
        padding: 4px 10px; border-radius: 6px; font-size: 13px;
        text-decoration: none; font-weight: 500;
    }
    .stats-alert-list a:hover { background: rgba(239,68,68,0.2); }

    .stats-kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 16px; margin-bottom: 20px;
    }
    .stats-kpi {
        background: #fff; border-radius: 14px; padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all .2s; position: relative; overflow: hidden;
    }
    .stats-kpi::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; background: #cbd5e1;
    }
    .stats-kpi:hover {
        transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stats-kpi--gold::before    { background: linear-gradient(90deg, #D4A857, #f59e0b); }
    .stats-kpi--success::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stats-kpi--warning::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stats-kpi--info::before    { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stats-kpi-label {
        font-size: 12px; text-transform: uppercase; letter-spacing: .5px;
        color: #64748b; font-weight: 600; margin-bottom: 8px;
    }
    .stats-kpi-value {
        font-size: 32px; font-weight: 800; color: #1E3A5F; line-height: 1;
    }
    .stats-kpi-hint {
        font-size: 12px; color: #94a3b8; margin-top: 6px;
    }

    .stats-charts-row, .stats-tables-row {
        display: grid; grid-template-columns: 2fr 1fr;
        gap: 16px; margin-bottom: 20px;
    }
    .stats-tables-row { grid-template-columns: 1fr 1fr; }

    .stats-card {
        background: #fff; border-radius: 14px; padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .stats-card-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 16px;
    }
    .stats-card-header h3 {
        font-size: 15px; font-weight: 700; color: #1E3A5F; margin: 0;
    }
    .stats-card-badge {
        background: rgba(212,168,87,0.12); color: #B8923D;
        padding: 4px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 600;
    }

    .stats-table {
        width: 100%; border-collapse: collapse; font-size: 14px;
    }
    .stats-table th {
        text-align: left; padding: 10px 8px;
        font-size: 11px; text-transform: uppercase; letter-spacing: .5px;
        color: #64748b; font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    .stats-table td {
        padding: 12px 8px; border-bottom: 1px solid #f1f5f9;
    }
    .stats-table tr:last-child td { border-bottom: none; }
    .stats-num { text-align: right; }
    .stats-link { color: #1E3A5F; text-decoration: none; font-weight: 500; }
    .stats-link:hover { color: #D4A857; }
    .stats-danger  { color: #ef4444; }
    .stats-success { color: #10b981; }

    .stats-rank {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; border-radius: 50%;
        font-size: 12px; font-weight: 700;
    }
    .stats-rank--bad  { background: #fee2e2; color: #991b1b; }
    .stats-rank--good { background: #d1fae5; color: #065f46; }

    .stats-chip {
        background: #eef2ff; color: #4338ca;
        padding: 3px 10px; border-radius: 6px;
        font-size: 12px; font-weight: 600;
    }
    .stats-badge {
        padding: 4px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
    }
    .stats-badge--success { background: #d1fae5; color: #065f46; }
    .stats-badge--danger  { background: #fee2e2; color: #991b1b; }

    .stats-empty {
        padding: 32px; text-align: center; color: #94a3b8; font-size: 14px;
    }

    @media (max-width: 1024px) {
        .stats-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .stats-charts-row, .stats-tables-row { grid-template-columns: 1fr; }
    }
</style>

{{-- ═══ CHART.JS ═══ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const NAVY = '#1E3A5F', GOLD = '#D4A857';
    const GREEN = '#10b981', RED = '#ef4444', BLUE = '#3b82f6', ORANGE = '#f59e0b', PURPLE = '#8b5cf6';

    Chart.defaults.font.family = "'Inter', 'Poppins', system-ui, sans-serif";
    Chart.defaults.color = '#475569';

    // ─── Uptime evolution
    new Chart(document.getElementById('chartUptime'), {
        type: 'line',
        data: {
            labels: @json($uptimeEvolution->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
            datasets: [{
                label: 'Uptime (%)',
                data: @json($uptimeEvolution->pluck('uptime')),
                borderColor: GOLD,
                backgroundColor: 'rgba(212,168,87,0.15)',
                tension: 0.35,
                fill: true,
                pointBackgroundColor: NAVY,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: v => v + '%' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ─── Incidents par type (donut)
    new Chart(document.getElementById('chartIncidents'), {
        type: 'doughnut',
        data: {
            labels: @json($incidentsParType->pluck('type')->map(fn($t) => ucfirst($t))),
            datasets: [{
                data: @json($incidentsParType->pluck('count')),
                backgroundColor: [RED, ORANGE, BLUE, PURPLE, GOLD],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } } }
        }
    });

    // ─── Activité 7 jours
    new Chart(document.getElementById('chartActivite'), {
        type: 'bar',
        data: {
            labels: @json($activite7j->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
            datasets: [{
                label: 'Vérifications',
                data: @json($activite7j->pluck('count')),
                backgroundColor: 'rgba(30,58,95,0.85)',
                borderRadius: 8,
                hoverBackgroundColor: GOLD
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } } }
        }
    });

    // ─── Users par rôle (donut)
    new Chart(document.getElementById('chartUsers'), {
        type: 'doughnut',
        data: {
            labels: @json($usersParRole->pluck('role')->map(fn($r) => ucfirst($r))),
            datasets: [{
                data: @json($usersParRole->pluck('count')),
                backgroundColor: [NAVY, GOLD, GREEN, BLUE],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } } }
        }
    });
})();
</script>
@endsection