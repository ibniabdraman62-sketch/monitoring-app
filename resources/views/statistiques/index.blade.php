@extends('layouts.monitoring')
@section('title', 'Statistiques globales')
@section('subtitle', 'Vue exécutive consolidée sur 30 jours')

@section('content')

{{-- ═══ ALERTE SITES DOWN ═══ --}}
@if($sitesDown->isNotEmpty())
<div class="alert alert-error mb-24">
    <i class="fas fa-triangle-exclamation"></i>
    <div style="flex:1;">
        <div style="font-weight:700; margin-bottom:6px;">
            {{ $sitesDown->count() }} site(s) actuellement indisponible(s)
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:6px;">
            @foreach($sitesDown as $s)
                <a href="{{ route('sites.show', $s) }}"
                   class="badge badge-danger" style="text-decoration:none;">
                    <i class="fas fa-globe" style="font-size:9px;"></i>
                    {{ $s->client_name ?? $s->name ?? $s->url }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ═══ KPIs ROW 1 (gradient cards) ═══ --}}
<div class="kpi-grid mb-24">
    <div class="kpi-card blue">
        <div class="kpi-label">Sites monitorés</div>
        <div class="kpi-value">{{ $kpis['sites_total'] }}</div>
        <i class="fas fa-globe kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Uptime moyen (30j)</div>
        <div class="kpi-value">{{ $kpis['uptime_moyen'] }}%</div>
        <i class="fas fa-heart-pulse kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Incidents ce mois</div>
        <div class="kpi-value">{{ $kpis['incidents_mois'] }}</div>
        <i class="fas fa-triangle-exclamation kpi-icon"></i>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-label">Alertes envoyées</div>
        <div class="kpi-value">{{ $kpis['alertes_mois'] }}</div>
        <i class="fas fa-bell kpi-icon"></i>
    </div>
</div>

{{-- ═══ KPIs ROW 2 (cards neutres) ═══ --}}
<div class="kpi-grid mb-24" style="grid-template-columns:repeat(4, 1fr);">
    <div class="card" style="padding:18px 20px;">
        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.6px; margin-bottom:6px;">
            Clients
        </div>
        <div style="font-size:28px; font-weight:700; color:var(--text); line-height:1;">
            {{ $kpis['clients'] }}
        </div>
        <div class="text-xs text-muted" style="margin-top:4px;">comptes actifs</div>
    </div>
    <div class="card" style="padding:18px 20px;">
        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.6px; margin-bottom:6px;">
            Agents
        </div>
        <div style="font-size:28px; font-weight:700; color:var(--text); line-height:1;">
            {{ $kpis['agents'] }}
        </div>
        <div class="text-xs text-muted" style="margin-top:4px;">opérateurs</div>
    </div>
    <div class="card" style="padding:18px 20px;">
        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.6px; margin-bottom:6px;">
            Rapports générés
        </div>
        <div style="font-size:28px; font-weight:700; color:var(--text); line-height:1;">
            {{ $kpis['rapports_mois'] }}
        </div>
        <div class="text-xs text-muted" style="margin-top:4px;">ce mois</div>
    </div>
    <div class="card" style="padding:18px 20px;">
        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.6px; margin-bottom:6px;">
            Vérifications totales
        </div>
        <div style="font-size:28px; font-weight:700; color:var(--text); line-height:1;">
            {{ number_format($kpis['verifs_total'], 0, ',', ' ') }}
        </div>
        <div class="text-xs text-muted" style="margin-top:4px;">all-time</div>
    </div>
</div>

{{-- ═══ CHARTS ROW 1 ═══ --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:24px;">
    <div class="card">
        <div class="card-title">
            <i class="fas fa-chart-line" style="color:var(--primary);"></i>
            Évolution du taux de disponibilité
            <span class="badge badge-info" style="margin-left:auto;">30 jours</span>
        </div>
        <div style="position:relative; height:280px;">
            <canvas id="chartUptime"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-title">
            <i class="fas fa-triangle-exclamation" style="color:var(--danger);"></i>
            Incidents par type
            <span class="badge badge-info" style="margin-left:auto;">30j</span>
        </div>
        <div style="position:relative; height:280px;">
            <canvas id="chartIncidents"></canvas>
        </div>
    </div>
</div>

{{-- ═══ CHARTS ROW 2 ═══ --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:24px;">
    <div class="card">
        <div class="card-title">
            <i class="fas fa-bolt" style="color:var(--gold);"></i>
            Activité des vérifications
            <span class="badge badge-info" style="margin-left:auto;">7 jours</span>
        </div>
        <div style="position:relative; height:280px;">
            <canvas id="chartActivite"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-title">
            <i class="fas fa-users" style="color:var(--primary);"></i>
            Utilisateurs par rôle
        </div>
        <div style="position:relative; height:280px;">
            <canvas id="chartUsers"></canvas>
        </div>
    </div>
</div>

{{-- ═══ TABLES TOP SITES (2 colonnes) ═══ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">

    {{-- Top instables --}}
    <div class="table-wrapper">
        <div class="table-header">
            <div class="card-title" style="margin:0;">
                <i class="fas fa-circle-exclamation" style="color:var(--danger);"></i>
                Sites les plus instables
            </div>
            <span class="badge badge-info">30 jours</span>
        </div>
        <div class="table-scroll" style="max-height:320px;">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Site</th>
                        <th style="text-align:right;">Incidents</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topInstables as $i => $s)
                        <tr>
                            <td>
                                <span class="badge badge-danger">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <a href="{{ route('sites.show', $s) }}"
                                   style="color:var(--text); font-weight:600; text-decoration:none;">
                                    {{ $s->client_name ?? $s->name ?? $s->url }}
                                </a>
                            </td>
                            <td style="text-align:right;">
                                <span class="badge badge-danger">{{ $s->incidents_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding:30px; color:var(--text-muted);">
                                <i class="fas fa-circle-check" style="font-size:24px; color:var(--success); display:block; margin-bottom:8px;"></i>
                                Aucun incident enregistré
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top fiables --}}
    <div class="table-wrapper">
        <div class="table-header">
            <div class="card-title" style="margin:0;">
                <i class="fas fa-circle-check" style="color:var(--success);"></i>
                Sites les plus fiables
            </div>
            <span class="badge badge-info">30 jours</span>
        </div>
        <div class="table-scroll" style="max-height:320px;">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Site</th>
                        <th style="text-align:right;">Uptime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topFiables as $i => $s)
                        <tr>
                            <td>
                                <span class="badge badge-success">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <a href="{{ route('sites.show', $s) }}"
                                   style="color:var(--text); font-weight:600; text-decoration:none;">
                                    {{ $s->client_name ?? $s->name ?? $s->url }}
                                </a>
                            </td>
                            <td style="text-align:right;">
                                <span class="badge badge-success">{{ $s->uptime_pct }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding:30px; color:var(--text-muted);">
                                Aucune donnée disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ═══ DERNIERS INCIDENTS ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-clock-rotate-left" style="color:var(--primary);"></i>
            Derniers incidents enregistrés
        </div>
        <span class="badge badge-info">5 plus récents</span>
    </div>
    <div class="table-scroll" style="max-height:380px;">
        <table>
            <thead>
                <tr>
                    <th>Site</th>
                    <th style="width:120px;">Type</th>
                    <th style="width:150px;">Début</th>
                    <th style="width:120px; text-align:center;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($derniersIncidents as $inc)
                    <tr>
                        <td>
                            @if($inc->site)
                                <a href="{{ route('sites.show', $inc->site) }}"
                                   style="color:var(--text); font-weight:600; text-decoration:none;
                                          display:inline-flex; align-items:center; gap:6px;">
                                    <i class="fas fa-globe" style="color:var(--primary); font-size:11px;"></i>
                                    {{ $inc->site->client_name ?? $inc->site->name ?? $inc->site->url }}
                                </a>
                            @else
                                <span class="text-muted">Site supprimé</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-neutral">
                                {{ ucfirst($inc->type ?? 'incident') }}
                            </span>
                        </td>
                        <td class="text-xs font-mono text-muted">
                            {{ optional($inc->started_at)->format('d/m/Y H:i') }}
                        </td>
                        <td style="text-align:center;">
                            @if(($inc->resolved_at ?? null) || ($inc->ended_at ?? null))
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Résolu
                                </span>
                            @else
                                <span class="badge badge-danger badge-dot">En cours</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);">
                            <i class="fas fa-circle-check" style="font-size:30px; color:var(--success); display:block; margin-bottom:10px;"></i>
                            Aucun incident à signaler ✨
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    // Palette synchronisée avec le layout
    const PRIMARY      = '#5B95C4';
    const PRIMARY_DARK = '#4078A9';
    const GOLD         = '#C9A876';
    const SUCCESS      = '#4A8C5A';
    const DANGER       = '#B66258';
    const WARNING      = '#C48A4A';
    const NEUTRAL      = '#8B7855';
    const TEXT         = '#3D2F1F';

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#5C4B36';

    // ─── Uptime evolution (ligne)
    new Chart(document.getElementById('chartUptime'), {
        type: 'line',
        data: {
            labels: @json($uptimeEvolution->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
            datasets: [{
                label: 'Uptime',
                data: @json($uptimeEvolution->pluck('uptime')),
                borderColor: GOLD,
                backgroundColor: 'rgba(201,168,118,0.15)',
                tension: 0.35,
                fill: true,
                pointBackgroundColor: PRIMARY_DARK,
                pointRadius: 3.5,
                pointHoverRadius: 5,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: TEXT,
                    titleColor: '#FFF',
                    bodyColor: '#FFF',
                    padding: 10,
                    cornerRadius: 6,
                    callbacks: { label: ctx => ' ' + ctx.parsed.y + '%' }
                }
            },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#F0E8D4' } },
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
                backgroundColor: [DANGER, WARNING, PRIMARY, GOLD, NEUTRAL],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 }, boxWidth: 10 } }
            }
        }
    });

    // ─── Activité 7 jours (bar)
    new Chart(document.getElementById('chartActivite'), {
        type: 'bar',
        data: {
            labels: @json($activite7j->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
            datasets: [{
                label: 'Vérifications',
                data: @json($activite7j->pluck('count')),
                backgroundColor: PRIMARY,
                hoverBackgroundColor: GOLD,
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#F0E8D4' }, ticks: { precision: 0 } }
            }
        }
    });

    // ─── Users par rôle (donut)
    new Chart(document.getElementById('chartUsers'), {
        type: 'doughnut',
        data: {
            labels: @json($usersParRole->pluck('role')->map(fn($r) => ucfirst($r))),
            datasets: [{
                data: @json($usersParRole->pluck('count')),
                backgroundColor: [PRIMARY_DARK, GOLD, SUCCESS, DANGER, NEUTRAL],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 }, boxWidth: 10 } }
            }
        }
    });
})();
</script>
@endsection