@extends('layouts.monitoring')

@section('title', 'Statistiques globales')
@section('subtitle', 'Vue exécutive consolidée sur 30 jours')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     STYLES SPÉCIFIQUES À LA PAGE (scoped via .stats-page)
═══════════════════════════════════════════════════════════════ --}}
<style>
.stats-page {
    --section-gap: 24px;
    --card-radius: 14px;
    --card-shadow: 0 1px 2px rgba(60,45,25,.04), 0 4px 12px rgba(60,45,25,.05);
    --card-shadow-hover: 0 4px 8px rgba(60,45,25,.06), 0 12px 28px rgba(60,45,25,.10);
    --card-border: 1px solid #EDE3CF;
    display: flex;
    flex-direction: column;
    gap: var(--section-gap);
}

/* ── Animations d'entrée ── */
@keyframes statsFadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes statsPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(182,98,88,.55); }
    50%      { box-shadow: 0 0 0 10px rgba(182,98,88,0); }
}
.stats-page > * {
    animation: statsFadeUp .55s cubic-bezier(.22,.61,.36,1) both;
}
.stats-page > *:nth-child(1){animation-delay:.00s}
.stats-page > *:nth-child(2){animation-delay:.05s}
.stats-page > *:nth-child(3){animation-delay:.10s}
.stats-page > *:nth-child(4){animation-delay:.15s}
.stats-page > *:nth-child(5){animation-delay:.20s}
.stats-page > *:nth-child(6){animation-delay:.25s}
.stats-page > *:nth-child(7){animation-delay:.30s}

/* ── Bandeau d'alerte sites down ── */
.alert-banner {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 18px 22px;
    background: linear-gradient(135deg, #FCEEEC 0%, #F9E4E0 100%);
    border: 1px solid #E8C5BF;
    border-left: 4px solid #B66258;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
}
.alert-banner__dot {
    width: 12px; height: 12px;
    background: #B66258;
    border-radius: 50%;
    margin-top: 6px;
    flex-shrink: 0;
    animation: statsPulse 2s infinite;
}
.alert-banner__body { flex: 1; min-width: 0; }
.alert-banner__title {
    font-weight: 700;
    color: #7A332C;
    font-size: 14px;
    margin-bottom: 10px;
    letter-spacing: .2px;
}
.alert-banner__sites {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.alert-banner__sites .badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    font-size: 12px;
    transition: transform .15s ease;
}
.alert-banner__sites .badge:hover { transform: translateY(-1px); }

/* ── Grilles KPI ── */
.kpi-grid-primary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}
.kpi-grid-secondary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
}

/* ── Cartes KPI primaires (executive) ── */
.kpi-card {
    position: relative;
    padding: 20px 22px 22px;
    background: #FFFFFF;
    border: var(--card-border);
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    transition: transform .25s ease, box-shadow .25s ease;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
}
.kpi-card::after {
    content: '';
    position: absolute;
    left: 0; right: 0; bottom: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--kpi-accent, #5B95C4), transparent);
    opacity: .9;
}
.kpi-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.kpi-card__label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #8B7855;
}
.kpi-card__icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--kpi-accent, #5B95C4) 12%, transparent);
    color: var(--kpi-accent, #5B95C4);
    font-size: 15px;
}
.kpi-card__value {
    font-size: 32px;
    font-weight: 700;
    color: #3D2F1F;
    line-height: 1.1;
    letter-spacing: -.5px;
    font-feature-settings: "tnum";
}
.kpi-card__hint {
    margin-top: 6px;
    font-size: 12px;
    color: #8B7855;
}

/* ── Cartes KPI secondaires (compactes) ── */
.kpi-mini {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    background: #FFFFFF;
    border: var(--card-border);
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    transition: transform .2s ease, box-shadow .2s ease;
}
.kpi-mini:hover {
    transform: translateY(-1px);
    box-shadow: var(--card-shadow-hover);
}
.kpi-mini__icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    background: #F7F1E3;
    color: #4078A9;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.kpi-mini__label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #8B7855;
    margin-bottom: 2px;
}
.kpi-mini__value {
    font-size: 22px;
    font-weight: 700;
    color: #3D2F1F;
    line-height: 1.1;
    font-feature-settings: "tnum";
}
.kpi-mini__hint {
    font-size: 11px;
    color: #A89880;
    margin-top: 2px;
}

/* ── Sections graphiques / tables ── */
.panel-row {
    display: grid;
    gap: 18px;
}
.panel-row.cols-2 { grid-template-columns: 2fr 1fr; }
.panel-row.cols-2-eq { grid-template-columns: 1fr 1fr; }
@media (max-width: 1100px) {
    .panel-row.cols-2,
    .panel-row.cols-2-eq { grid-template-columns: 1fr; }
}

.panel {
    background: #FFFFFF;
    border: var(--card-border);
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    transition: box-shadow .25s ease;
}
.panel:hover { box-shadow: var(--card-shadow-hover); }
.panel__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #F0E8D4;
    background: linear-gradient(180deg, #FBF7EC 0%, #FFFFFF 100%);
}
.panel__title {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-size: 13.5px;
    font-weight: 700;
    color: #3D2F1F;
    letter-spacing: .2px;
}
.panel__title i { color: #4078A9; font-size: 13px; }
.panel__badge {
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: #8B7855;
    padding: 4px 10px;
    background: #F7F1E3;
    border-radius: 999px;
}
.panel__body { padding: 18px 20px; }
.panel__body--chart { height: 300px; position: relative; }
.panel__body--table { padding: 0; }

/* ── Tableaux raffinés ── */
.stats-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.stats-table thead th {
    text-align: left;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #8B7855;
    padding: 12px 20px;
    background: #FBF7EC;
    border-bottom: 1px solid #F0E8D4;
}
.stats-table tbody td {
    padding: 13px 20px;
    border-bottom: 1px solid #F5EDD9;
    color: #3D2F1F;
    vertical-align: middle;
}
.stats-table tbody tr { transition: background .15s ease; }
.stats-table tbody tr:hover { background: #FBF7EC; }
.stats-table tbody tr:last-child td { border-bottom: none; }
.stats-table .rank-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px; height: 26px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    font-feature-settings: "tnum";
}
.stats-table .empty-state {
    text-align: center;
    padding: 36px 20px;
    color: #A89880;
}
.stats-table .empty-state i {
    font-size: 26px;
    display: block;
    margin-bottom: 10px;
}
</style>

<div class="stats-page">

{{-- ═══ ALERTE SITES DOWN ═══ --}}
@if($sitesDown->isNotEmpty())
    <div class="alert-banner" role="alert">
        <div class="alert-banner__dot"></div>
        <div class="alert-banner__body">
            <div class="alert-banner__title">
                <i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>
                {{ $sitesDown->count() }} site(s) actuellement indisponible(s)
            </div>
            <div class="alert-banner__sites">
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

{{-- ═══ KPIs ROW 1 (cartes executives) ═══ --}}
<div class="kpi-grid-primary">

    <div class="kpi-card" style="--kpi-accent:#4078A9;">
        <div class="kpi-card__head">
            <span class="kpi-card__label">Sites monitorés</span>
            <span class="kpi-card__icon"><i class="fas fa-globe"></i></span>
        </div>
        <div class="kpi-card__value">{{ $kpis['sites_total'] }}</div>
        <div class="kpi-card__hint">infrastructure surveillée</div>
    </div>

    <div class="kpi-card" style="--kpi-accent:#4A8C5A;">
        <div class="kpi-card__head">
            <span class="kpi-card__label">Uptime moyen (30j)</span>
            <span class="kpi-card__icon"><i class="fas fa-heart-pulse"></i></span>
        </div>
        <div class="kpi-card__value">{{ $kpis['uptime_moyen'] }}%</div>
        <div class="kpi-card__hint">disponibilité consolidée</div>
    </div>

    <div class="kpi-card" style="--kpi-accent:#B66258;">
        <div class="kpi-card__head">
            <span class="kpi-card__label">Incidents ce mois</span>
            <span class="kpi-card__icon"><i class="fas fa-circle-exclamation"></i></span>
        </div>
        <div class="kpi-card__value">{{ $kpis['incidents_mois'] }}</div>
        <div class="kpi-card__hint">détections sur 30 jours</div>
    </div>

    <div class="kpi-card" style="--kpi-accent:#C9A876;">
        <div class="kpi-card__head">
            <span class="kpi-card__label">Alertes envoyées</span>
            <span class="kpi-card__icon"><i class="fas fa-bell"></i></span>
        </div>
        <div class="kpi-card__value">{{ $kpis['alertes_mois'] }}</div>
        <div class="kpi-card__hint">notifications déclenchées</div>
    </div>

</div>

{{-- ═══ KPIs ROW 2 (cartes compactes) ═══ --}}
<div class="kpi-grid-secondary">

    <div class="kpi-mini">
        <div class="kpi-mini__icon"><i class="fas fa-users"></i></div>
        <div>
            <div class="kpi-mini__label">Clients</div>
            <div class="kpi-mini__value">{{ $kpis['clients'] }}</div>
            <div class="kpi-mini__hint">comptes actifs</div>
        </div>
    </div>

    <div class="kpi-mini">
        <div class="kpi-mini__icon"><i class="fas fa-user-shield"></i></div>
        <div>
            <div class="kpi-mini__label">Agents</div>
            <div class="kpi-mini__value">{{ $kpis['agents'] }}</div>
            <div class="kpi-mini__hint">opérateurs</div>
        </div>
    </div>

    <div class="kpi-mini">
        <div class="kpi-mini__icon"><i class="fas fa-file-lines"></i></div>
        <div>
            <div class="kpi-mini__label">Rapports générés</div>
            <div class="kpi-mini__value">{{ $kpis['rapports_mois'] }}</div>
            <div class="kpi-mini__hint">ce mois</div>
        </div>
    </div>

    <div class="kpi-mini">
        <div class="kpi-mini__icon"><i class="fas fa-wave-square"></i></div>
        <div>
            <div class="kpi-mini__label">Vérifications totales</div>
            <div class="kpi-mini__value">{{ number_format($kpis['verifs_total'], 0, ',', ' ') }}</div>
            <div class="kpi-mini__hint">all-time</div>
        </div>
    </div>

</div>

{{-- ═══ CHARTS ROW 1 ═══ --}}
<div class="panel-row cols-2">

    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">
                <i class="fas fa-chart-line"></i>
                Évolution du taux de disponibilité
            </div>
            <span class="panel__badge">30 jours</span>
        </div>
        <div class="panel__body panel__body--chart">
            <canvas id="chartUptime"></canvas>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">
                <i class="fas fa-chart-pie"></i>
                Incidents par type
            </div>
            <span class="panel__badge">30j</span>
        </div>
        <div class="panel__body panel__body--chart">
            <canvas id="chartIncidents"></canvas>
        </div>
    </div>

</div>

{{-- ═══ CHARTS ROW 2 ═══ --}}
<div class="panel-row cols-2-eq">

    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">
                <i class="fas fa-chart-column"></i>
                Activité des vérifications
            </div>
            <span class="panel__badge">7 jours</span>
        </div>
        <div class="panel__body panel__body--chart">
            <canvas id="chartActivite"></canvas>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">
                <i class="fas fa-users-gear"></i>
                Utilisateurs par rôle
            </div>
            <span class="panel__badge">répartition</span>
        </div>
        <div class="panel__body panel__body--chart">
            <canvas id="chartUsers"></canvas>
        </div>
    </div>

</div>

{{-- ═══ TABLES TOP SITES ═══ --}}
<div class="panel-row cols-2-eq">

    {{-- Top instables --}}
    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">
                <i class="fas fa-triangle-exclamation" style="color:#B66258;"></i>
                Sites les plus instables
            </div>
            <span class="panel__badge">30 jours</span>
        </div>
        <div class="panel__body panel__body--table">
            <table class="stats-table">
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
                                <span class="rank-pill" style="background:#FCEEEC; color:#B66258;">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <a href="{{ route('sites.show', $s) }}"
                                   style="color:#3D2F1F; font-weight:600; text-decoration:none;">
                                    {{ $s->client_name ?? $s->name ?? $s->url }}
                                </a>
                            </td>
                            <td style="text-align:right;">
                                <span class="badge badge-danger">{{ $s->incidents_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fas fa-circle-check" style="color:#4A8C5A;"></i>
                                Aucun incident enregistré
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top fiables --}}
    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">
                <i class="fas fa-shield-halved" style="color:#4A8C5A;"></i>
                Sites les plus fiables
            </div>
            <span class="panel__badge">30 jours</span>
        </div>
        <div class="panel__body panel__body--table">
            <table class="stats-table">
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
                                <span class="rank-pill" style="background:#E8F1EA; color:#4A8C5A;">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <a href="{{ route('sites.show', $s) }}"
                                   style="color:#3D2F1F; font-weight:600; text-decoration:none;">
                                    {{ $s->client_name ?? $s->name ?? $s->url }}
                                </a>
                            </td>
                            <td style="text-align:right;">
                                <span class="badge badge-success">{{ $s->uptime_pct }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fas fa-circle-info"></i>
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
<div class="panel">
    <div class="panel__head">
        <div class="panel__title">
            <i class="fas fa-clock-rotate-left"></i>
            Derniers incidents enregistrés
        </div>
        <span class="panel__badge">5 plus récents</span>
    </div>
    <div class="panel__body panel__body--table">
        <table class="stats-table">
            <thead>
                <tr>
                    <th>Site</th>
                    <th>Type</th>
                    <th>Début</th>
                    <th style="text-align:center;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($derniersIncidents as $inc)
                    <tr>
                        <td>
                            @if($inc->site)
                                <a href="{{ route('sites.show', $inc->site) }}"
                                   style="color:#3D2F1F; font-weight:600; text-decoration:none;
                                          display:inline-flex; align-items:center; gap:8px;">
                                    <i class="fas fa-globe" style="color:#4078A9; font-size:11px;"></i>
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
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-circle-check" style="color:#4A8C5A;"></i>
                            Aucun incident à signaler ✨
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div> {{-- /.stats-page --}}

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
    Chart.defaults.font.size = 11.5;
    Chart.defaults.color = '#5C4B36';

    const tooltipStyle = {
        backgroundColor: TEXT,
        titleColor: '#FFF',
        bodyColor: '#FFF',
        padding: 12,
        cornerRadius: 8,
        displayColors: false,
        titleFont: { weight: '600', size: 12 },
        bodyFont: { size: 12 }
    };

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
                pointBorderColor: '#FFF',
                pointBorderWidth: 1.5,
                pointRadius: 3.5,
                pointHoverRadius: 6,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { ...tooltipStyle, callbacks: { label: ctx => ' ' + ctx.parsed.y + '%' } }
            },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#F0E8D4', drawBorder: false } },
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
                borderWidth: 2,
                borderColor: '#FFFFFF',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 }, boxWidth: 10, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: tooltipStyle
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
                borderRadius: 8,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipStyle },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#F0E8D4', drawBorder: false }, ticks: { precision: 0 } }
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
                borderWidth: 2,
                borderColor: '#FFFFFF',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 }, boxWidth: 10, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: tooltipStyle
            }
        }
    });
})();
</script>
@endsection
