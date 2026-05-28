@extends('layouts.monitoring')
@section('title', $site->client_name)
@section('subtitle', $site->url)

@section('content')

{{-- ═══ Back + actions ═══ --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <a href="{{ route('sites.index') }}" class="btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
    <div style="display:flex; gap:8px;">
        <form method="POST" action="{{ route('sites.check-now', $site) }}">
            @csrf
            <button class="btn-primary btn-sm">
                <i class="fas fa-sync"></i> Vérifier maintenant
            </button>
        </form>
        <a href="{{ route('sites.edit', $site) }}" class="btn-primary btn-warning btn-sm">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <form method="POST" action="{{ route('sites.toggle', $site) }}">
            @csrf @method('PATCH')
            <button class="btn-primary {{ $site->is_active ? 'btn-warning' : 'btn-success' }} btn-sm">
                <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }}"></i>
                {{ $site->is_active ? 'Désactiver' : 'Activer' }}
            </button>
        </form>
        <form method="POST" action="{{ route('sites.destroy', $site) }}"
              onsubmit="return confirm('Confirmer la suppression définitive ?');">
            @csrf @method('DELETE')
            <button class="btn-primary btn-danger btn-sm">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </form>
    </div>
</div>

@php
    $lastVerif = $site->verifications()->orderByDesc('checked_at')->first();
    $isUp      = $lastVerif && $lastVerif->is_up;
    $uptime    = $site->verifications()->where('is_up', true)->count();
    $total     = $site->verifications()->count();
    $uptimePct = $total ? round(($uptime/$total)*100, 1) : 100;
    $avgRT     = round($site->verifications()->avg('response_time_ms') ?? 0);
@endphp

{{-- ═══ KPIs ═══ --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-label">Statut actuel</div>
        <div class="kpi-value" style="font-size:22px;">{{ $isUp ? 'En ligne' : 'Hors ligne' }}</div>
        <i class="fas fa-signal kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Disponibilité</div>
        <div class="kpi-value">{{ $uptimePct }}<span style="font-size:18px;">%</span></div>
        <i class="fas fa-arrow-up kpi-icon"></i>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-label">Temps moyen</div>
        <div class="kpi-value">{{ $avgRT }}<span style="font-size:18px;"> ms</span></div>
        <i class="fas fa-bolt kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Incidents</div>
        <div class="kpi-value">{{ $site->incidents()->count() }}</div>
        <i class="fas fa-exclamation-circle kpi-icon"></i>
    </div>
</div>

{{-- ═══ Config + SSL/Domaine ═══ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">

    <div class="card">
        <div class="card-title"><i class="fas fa-cog" style="color:var(--primary);"></i> Configuration</div>
        <table style="width:100%; font-size:13px;">
            <tr style="border-bottom:1px solid var(--border-light);">
                <td style="padding:8px 0; color:var(--text-muted);">Client</td>
                <td style="padding:8px 0; font-weight:600;">{{ $site->client_name }}</td>
            </tr>
            <tr style="border-bottom:1px solid var(--border-light);">
                <td style="padding:8px 0; color:var(--text-muted);">URL</td>
                <td style="padding:8px 0; font-weight:600; word-break:break-all;">{{ $site->url }}</td>
            </tr>
            <tr style="border-bottom:1px solid var(--border-light);">
                <td style="padding:8px 0; color:var(--text-muted);">Fréquence</td>
                <td style="padding:8px 0; font-weight:600;">Toutes les {{ $site->frequency_min }} min</td>
            </tr>
            <tr style="border-bottom:1px solid var(--border-light);">
                <td style="padding:8px 0; color:var(--text-muted);">Seuil de lenteur</td>
                <td style="padding:8px 0; font-weight:600;">{{ $site->response_threshold_ms }} ms</td>
            </tr>
            <tr style="border-bottom:1px solid var(--border-light);">
                <td style="padding:8px 0; color:var(--text-muted);">Vérif. SSL</td>
                <td style="padding:8px 0;">
                    @if($site->ssl_check)<span class="badge badge-success">Activée</span>
                    @else<span class="badge badge-neutral">Désactivée</span>@endif
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:var(--text-muted);">Vérif. WHOIS</td>
                <td style="padding:8px 0;">
                    @if($site->whois_check)<span class="badge badge-success">Activée</span>
                    @else<span class="badge badge-neutral">Désactivée</span>@endif
                </td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="card-title"><i class="fas fa-shield-alt" style="color:var(--primary);"></i> SSL et domaine</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div style="background:var(--bg-soft); padding:14px; border-radius:8px;">
                <div class="text-xs text-muted" style="margin-bottom:5px;">Certificat SSL</div>
                @if($lastVerif && $lastVerif->ssl_days_remaining)
                    <div style="font-size:22px; font-weight:700;
                                color:{{ $lastVerif->ssl_days_remaining > 30 ? 'var(--success)' : ($lastVerif->ssl_days_remaining > 7 ? 'var(--warning)' : 'var(--danger)') }};">
                        {{ $lastVerif->ssl_days_remaining }} j
                    </div>
                    <div class="text-xs text-muted">
                        Expire le {{ \Carbon\Carbon::parse($lastVerif->ssl_expires_at)->format('d/m/Y') }}
                    </div>
                @else
                    <div class="text-muted">N/D</div>
                @endif
            </div>
            <div style="background:var(--bg-soft); padding:14px; border-radius:8px;">
                <div class="text-xs text-muted" style="margin-bottom:5px;">Domaine</div>
                @php
                    $dd = $site->domain_expires_at
                        ? \Carbon\Carbon::parse($site->domain_expires_at)->diffInDays(now(), false) * -1
                        : null;
                @endphp
                @if($dd !== null)
                    <div style="font-size:22px; font-weight:700;
                                color:{{ $dd > 60 ? 'var(--success)' : ($dd > 30 ? 'var(--warning)' : 'var(--danger)') }};">
                        {{ $dd }} j
                    </div>
                    <div class="text-xs text-muted">
                        Expire le {{ \Carbon\Carbon::parse($site->domain_expires_at)->format('d/m/Y') }}
                    </div>
                @else
                    <div class="text-muted">N/D</div>
                @endif
            </div>
        </div>
        @if($site->domain_registrar)
            <div style="margin-top:12px; padding:12px; background:var(--bg-soft); border-radius:8px;">
                <div class="text-xs text-muted">Registrar</div>
                <div style="font-weight:600;">{{ $site->domain_registrar }}</div>
            </div>
        @endif
    </div>
</div>

{{-- ═══ Historique vérifications ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            Historique des vérifications
        </div>
        <span class="badge badge-info">{{ $site->verifications()->count() }} entrées</span>
    </div>
    <div class="table-scroll" style="max-height:520px;">
        <table>
            <thead>
                <tr>
                    <th>Date / heure</th>
                    <th>Statut</th>
                    <th>Code HTTP</th>
                    <th>Temps de réponse</th>
                    <th>SSL valide</th>
                    <th>Jours SSL</th>
                </tr>
            </thead>
            <tbody>
            @forelse($site->verifications()->orderByDesc('checked_at')->limit(100)->get() as $v)
                <tr>
                    <td class="text-sm font-mono">{{ $v->checked_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        @if($v->is_up)<span class="badge badge-success">OK</span>
                        @else<span class="badge badge-danger">Échec</span>@endif
                    </td>
                    <td class="font-mono">{{ $v->http_code ?? '—' }}</td>
                    <td class="font-mono">{{ $v->response_time_ms }} ms</td>
                    <td>
                        @if($v->ssl_valid)<span class="badge badge-success">Oui</span>
                        @elseif($v->ssl_valid === false)<span class="badge badge-danger">Non</span>
                        @else<span class="text-muted">—</span>@endif
                    </td>
                    <td class="font-mono">{{ $v->ssl_days_remaining ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucune vérification enregistrée
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection