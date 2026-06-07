@extends('layouts.monitoring')
@section('title', 'Tableau de bord')
@section('subtitle', 'Vue d\'ensemble de la supervision en temps réel')

@section('content')

{{-- ═══ NOUVELLE PALETTE KPI CARDS MODERNE ═══ --}}
<style>
.kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 20px !important;
    margin-bottom: 28px !important;
}

.kpi-card {
    position: relative;
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 16px !important;
    padding: 24px 24px 24px 28px !important;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
}

.kpi-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    border-radius: 16px 0 0 16px;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10) !important;
}

.kpi-card .kpi-label {
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #6B7280 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    margin-bottom: 12px !important;
}

.kpi-card .kpi-value {
    font-size: 38px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    letter-spacing: -1px !important;
}

.kpi-card .kpi-icon {
    position: absolute !important;
    right: 20px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 56px !important;
    opacity: 0.08 !important;
}

/* ─── Card BLUE (Total sites) ─── */
.kpi-card.blue::before { background: linear-gradient(180deg, #6366F1, #4F46E5); }
.kpi-card.blue .kpi-value { color: #4F46E5 !important; }
.kpi-card.blue .kpi-icon { color: #4F46E5 !important; }
.kpi-card.blue:hover { border-color: #C7D2FE !important; }

/* ─── Card GREEN (Sites actifs) ─── */
.kpi-card.green::before { background: linear-gradient(180deg, #34D399, #10B981); }
.kpi-card.green .kpi-value { color: #059669 !important; }
.kpi-card.green .kpi-icon { color: #10B981 !important; }
.kpi-card.green:hover { border-color: #A7F3D0 !important; }

/* ─── Card RED (Incidents actifs) ─── */
.kpi-card.red::before { background: linear-gradient(180deg, #F87171, #EF4444); }
.kpi-card.red .kpi-value { color: #DC2626 !important; }
.kpi-card.red .kpi-icon { color: #EF4444 !important; }
.kpi-card.red:hover { border-color: #FECACA !important; }

/* ─── Card GOLD (Disponibilité) ─── */
.kpi-card.gold::before { background: linear-gradient(180deg, #FBBF24, #F59E0B); }
.kpi-card.gold .kpi-value { color: #D97706 !important; }
.kpi-card.gold .kpi-icon { color: #F59E0B !important; }
.kpi-card.gold:hover { border-color: #FDE68A !important; }

/* ─── Responsive ─── */
@media (max-width: 1100px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 600px) {
    .kpi-grid { grid-template-columns: 1fr !important; }
}

/* ═══ UPTIME RING MODERNE ═══ */
.uptime-card-modern {
    background: linear-gradient(135deg, #FFFFFF 0%, #F9FAFB 100%) !important;
    border: 1px solid #E5E7EB !important;
    padding: 28px 20px !important;
}

.uptime-particles {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 1;
}

.uptime-particles span {
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(99, 102, 241, 0.15);
    border-radius: 50%;
    animation: floatParticle 6s infinite ease-in-out;
}

.uptime-particles span:nth-child(1) { top: 15%; left: 10%; animation-delay: 0s; }
.uptime-particles span:nth-child(2) { top: 75%; left: 20%; animation-delay: 1.5s; }
.uptime-particles span:nth-child(3) { top: 30%; right: 15%; animation-delay: 3s; }
.uptime-particles span:nth-child(4) { bottom: 20%; right: 10%; animation-delay: 4.5s; }

@keyframes floatParticle {
    0%, 100% { transform: translate(0, 0); opacity: 0.3; }
    50%      { transform: translate(8px, -12px); opacity: 0.8; }
}

.uptime-ring-container {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 14px 0;
    z-index: 2;
}

.uptime-ring-svg {
    transform: scale(0.95);
    transition: transform 0.4s ease;
}

.uptime-ring-svg:hover {
    transform: scale(1);
}

.uptime-ring-progress {
    transition: stroke-dashoffset 2s cubic-bezier(0.4, 0, 0.2, 1);
    animation: drawRing 2s ease-out;
}

@keyframes drawRing {
    from { stroke-dashoffset: 502; }
}

.pulse-glow {
    animation: drawRing 2s ease-out, pulseGlow 2.5s infinite ease-in-out 2s;
}

@keyframes pulseGlow {
    0%, 100% { filter: url(#glow) drop-shadow(0 0 4px #34D399); }
    50%      { filter: url(#glow) drop-shadow(0 0 12px #10B981); }
}

.uptime-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 2;
}

.uptime-percent {
    font-size: 42px;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}

.uptime-label {
    font-size: 11px;
    font-weight: 700;
    color: #6B7280;
    letter-spacing: 2px;
    margin-top: 6px;
}

.uptime-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 24px;
    font-size: 12.5px;
    font-weight: 700;
    margin-top: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease;
    z-index: 2;
}

.uptime-status-badge:hover {
    transform: scale(1.05);
}

.uptime-status-badge.excellent {
    background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
    color: #065F46;
    border: 1px solid #6EE7B7;
}

.uptime-status-badge.acceptable {
    background: linear-gradient(135deg, #FEF3C7, #FDE68A);
    color: #92400E;
    border: 1px solid #FCD34D;
}

.uptime-status-badge.critique {
    background: linear-gradient(135deg, #FEE2E2, #FECACA);
    color: #991B1B;
    border: 1px solid #FCA5A5;
}

</style>



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

    <div class="card uptime-card-modern" style="display:flex; flex-direction:column; align-items:center; justify-content:center; position:relative; overflow:hidden;">
    {{-- Background particles --}}
    <div class="uptime-particles">
        <span></span><span></span><span></span><span></span>
    </div>

    <div class="card-title" style="text-align:center; position:relative; z-index:2;">
        <i class="fas fa-chart-pie" style="color:var(--primary);"></i>
        Disponibilité 24h
    </div>

    <div class="uptime-ring-container">
        {{-- SVG Anneau animé --}}
        <svg width="200" height="200" viewBox="0 0 200 200" class="uptime-ring-svg">
            <defs>
                <linearGradient id="gradientGreen" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#34D399"/>
                    <stop offset="100%" stop-color="#059669"/>
                </linearGradient>
                <linearGradient id="gradientOrange" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FBBF24"/>
                    <stop offset="100%" stop-color="#D97706"/>
                </linearGradient>
                <linearGradient id="gradientRed" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#F87171"/>
                    <stop offset="100%" stop-color="#DC2626"/>
                </linearGradient>
                <filter id="glow">
                    <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                    <feMerge>
                        <feMergeNode in="coloredBlur"/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>

            {{-- Anneau de fond --}}
            <circle cx="100" cy="100" r="80" fill="none" stroke="#F1F5F9" stroke-width="14"/>

            {{-- Anneau animé --}}
            @php
                $stroke = $uptimeMoyen >= 99 ? 'url(#gradientGreen)' : ($uptimeMoyen >= 95 ? 'url(#gradientOrange)' : 'url(#gradientRed)');
                $circumference = 2 * 3.14159 * 80;
                $offset = $circumference - ($circumference * $uptimeMoyen / 100);
            @endphp
            <circle cx="100" cy="100" r="80" fill="none"
                    stroke="{{ $stroke }}"
                    stroke-width="14"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $offset }}"
                    transform="rotate(-90 100 100)"
                    filter="url(#glow)"
                    class="uptime-ring-progress {{ $uptimeMoyen >= 99 ? 'pulse-glow' : '' }}"/>
        </svg>

        {{-- Centre — Valeur + label --}}
        <div class="uptime-center">
            <div class="uptime-percent"
                 data-target="{{ $uptimeMoyen }}"
                 style="color:{{ $uptimeMoyen >= 99 ? '#059669' : ($uptimeMoyen >= 95 ? '#D97706' : '#DC2626') }};">
                0<span style="font-size:18px;">%</span>
            </div>
            <div class="uptime-label">UPTIME</div>
        </div>
    </div>

    {{-- Badge de statut --}}
    <div class="uptime-status-badge {{ $uptimeMoyen >= 99 ? 'excellent' : ($uptimeMoyen >= 95 ? 'acceptable' : 'critique') }}">
        @if($uptimeMoyen >= 99)
            <i class="fas fa-circle-check"></i> Excellent
        @elseif($uptimeMoyen >= 95)
            <i class="fas fa-circle-exclamation"></i> Acceptable
        @else
            <i class="fas fa-triangle-exclamation"></i> Critique
        @endif
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
                <td class="text-sm font-mono">{{ $inc->started_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
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

// ─── Animation du compteur uptime ───
(function animateUptime() {
    const el = document.querySelector('.uptime-percent');
    if (!el) return;
    const target = parseFloat(el.dataset.target);
    let current = 0;
    const duration = 1800; // 1.8s
    const startTime = performance.now();

    function update(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Easing : easeOutCubic
        const eased = 1 - Math.pow(1 - progress, 3);
        current = (target * eased).toFixed(1);
        el.innerHTML = current + '<span style="font-size:18px;">%</span>';

        if (progress < 1) requestAnimationFrame(update);
        else el.innerHTML = target + '<span style="font-size:18px;">%</span>';
    }
    requestAnimationFrame(update);
})();


function setChartType(type) {
    createChart(type);
    document.getElementById('btn-line').className = type === 'line' ? 'btn-primary btn-xs' : 'btn-secondary btn-xs';
    document.getElementById('btn-bar').className  = type === 'bar'  ? 'btn-primary btn-xs' : 'btn-secondary btn-xs';
}

createChart('line');

// ─── Donut uptime ──────────────────────────────
// new Chart(document.getElementById('uptimeDonut'), {
//     type: 'doughnut',
//     data: {
//         datasets: [{
//             data: [{{ $uptimeMoyen }}, {{ 100 - $uptimeMoyen }}],
//             backgroundColor: [
//                 '{{ $uptimeMoyen >= 99 ? "#4A8C5A" : ($uptimeMoyen >= 95 ? "#C48A4A" : "#B66258") }}',
//                 '#F0E8D4'
//             ],
//             borderWidth: 0,
//         }]
//     },
//     options: { cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } }, responsive: true, maintainAspectRatio: false }
// });

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
const timer = setInterval(() => {
    countdown--;
    if (el) el.textContent = countdown;
    if (countdown <= 0) {
        clearInterval(timer);
        location.reload();
    }
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