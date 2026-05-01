@extends('layouts.monitoring')
@section('title', 'Sites Surveillés')
@section('subtitle', 'Monitoring en temps réel')

@section('content')

<!-- Barre de recherche + filtres -->
<div class="card" style="margin-bottom:16px; padding:16px 24px;">
    <form method="GET" action="{{ route('sites.index') }}"
          style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px; position:relative;">
            <i class="fas fa-search" style="position:absolute; left:12px; top:50%;
               transform:translateY(-50%); color:#94A3B8;"></i>
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Rechercher par nom client ou URL..."
                   class="form-input" style="padding-left:36px;">
        </div>

        <!-- Filtre statut -->
        <select name="statut" class="form-input" style="width:auto; min-width:160px;"
                onchange="this.form.submit()">
            <option value="">— Tous les statuts —</option>
            <option value="online"   {{ request('statut')=='online'   ? 'selected' : '' }}>✅ En ligne</option>
            <option value="offline"  {{ request('statut')=='offline'  ? 'selected' : '' }}>🔴 Hors ligne</option>
            <option value="slow"     {{ request('statut')=='slow'     ? 'selected' : '' }}>🟡 Lent</option>
            <option value="ssl"      {{ request('statut')=='ssl'      ? 'selected' : '' }}>🔒 Alerte SSL</option>
            <option value="inactive" {{ request('statut')=='inactive' ? 'selected' : '' }}>⏸ Inactifs</option>
        </select>

        <button type="submit" class="btn-primary">
            <i class="fas fa-search"></i> Rechercher
        </button>
        @if(request('search') || request('statut'))
        <a href="{{ route('sites.index') }}" class="btn-primary btn-danger">
            <i class="fas fa-times"></i> Effacer
        </a>
        @endif
    </form>
</div>

<!-- Compteurs -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <span class="badge badge-blue" style="font-size:12px; padding:6px 14px;">
            📡 {{ $sites->count() }} site(s)
        </span>
        <a href="{{ route('sites.index', ['statut'=>'online']) }}"
           class="badge badge-green" style="font-size:12px; padding:6px 14px; text-decoration:none; cursor:pointer;">
            ● {{ $sites->filter(fn($s) => $s->verifications()->latest('checked_at')->first()?->is_up)->count() }} actifs
        </a>
        <a href="{{ route('sites.index', ['statut'=>'offline']) }}"
           class="badge badge-red" style="font-size:12px; padding:6px 14px; text-decoration:none; cursor:pointer;">
            ● {{ $sites->filter(fn($s) => !($s->verifications()->latest('checked_at')->first()?->is_up ?? true))->count() }} hors ligne
        </a>
        <a href="{{ route('sites.index', ['statut'=>'inactive']) }}"
           class="badge" style="font-size:12px; padding:6px 14px; text-decoration:none; cursor:pointer; background:#E2E8F0; color:#64748B;">
            ⏸ {{ $sites->where('is_active', false)->count() }} inactifs
        </a>
    </div>
    <a href="{{ route('sites.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Ajouter un site
    </a>
</div>

@forelse($sites as $site)
@php
    $lastVerif  = $site->verifications()->latest('checked_at')->first();
    $total      = $site->verifications()->where('checked_at', '>=', now()->subDay())->count();
    $up         = $site->verifications()->where('checked_at', '>=', now()->subDay())->where('is_up', true)->count();
    $uptime     = $total > 0 ? round($up / $total * 100, 1) : 100;
    $history    = $site->verifications()->latest('checked_at')->take(30)->get()->reverse();

    // SSL
    $sslDays = null;
    if ($lastVerif && $lastVerif->ssl_expires_at) {
        $sslDays = now()->diffInDays($lastVerif->ssl_expires_at, false);
    }

    // Domaine WHOIS
    $domainDays = null;
    if ($site->domain_expires_at) {
        $domainDays = now()->diffInDays(\Carbon\Carbon::parse($site->domain_expires_at), false);
    }

    // Statut vitesse
    $isSlow = $lastVerif && $lastVerif->is_up &&
              $lastVerif->response_time_ms > $site->response_threshold_ms;

    // Couleur bande gauche
    if (!$site->is_active) {
        $bandColor = '#94A3B8';
    } elseif ($lastVerif && !$lastVerif->is_up) {
        $bandColor = '#EF4444';
    } elseif ($isSlow) {
        $bandColor = '#F59E0B';
    } else {
        $bandColor = '#10B981';
    }
@endphp

<div class="card" style="margin-bottom:16px; padding:0; overflow:hidden;
     {{ !$site->is_active ? 'opacity:0.65;' : '' }}">
    <div style="display:grid; grid-template-columns:6px 1fr auto; align-items:stretch;">

        <!-- Bande statut gauche -->
        <div style="background:{{ $bandColor }};"></div>

        <!-- Contenu principal -->
        <div style="padding:20px 24px;">

            <!-- Ligne 1 : nom + badges -->
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px; flex-wrap:wrap;">

                <div style="position:relative; width:14px; height:14px; flex-shrink:0;">
                    <span class="status-dot {{ $lastVerif && $lastVerif->is_up ? 'online' : ($lastVerif ? 'offline' : 'unknown') }}"
                          style="width:14px; height:14px; position:absolute;"></span>
                </div>

                <div style="flex:1; min-width:150px;">
                    <div style="font-size:16px; font-weight:700; color:#0C3547;">
                        {{ $site->client_name }}
                        @if(!$site->is_active)
                            <span style="font-size:11px; color:#94A3B8; font-weight:500;">(inactif)</span>
                        @endif
                    </div>
                    <a href="{{ $site->url }}" target="_blank"
                       style="font-size:12px; color:#1697C2; text-decoration:none;">
                        {{ $site->url }}
                    </a>
                </div>

                <!-- Badges statut -->
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-left:auto;">

                    {{-- Statut principal --}}
                    @if(!$lastVerif)
                        <span class="badge badge-gray">EN ATTENTE</span>
                    @elseif(!$lastVerif->is_up)
                        <span class="badge badge-red">🔴 HORS LIGNE</span>
                    @elseif($isSlow)
                        <span class="badge badge-yellow">🟡 LENT</span>
                    @else
                        <span class="badge badge-green">✅ EN LIGNE</span>
                    @endif

                    {{-- Temps de réponse --}}
                    @if($lastVerif)
                        <span class="badge {{ $lastVerif->response_time_ms > $site->response_threshold_ms ? 'badge-yellow' : ($lastVerif->response_time_ms > 1000 ? 'badge-yellow' : 'badge-green') }}">
                            <i class="fas fa-clock"></i> {{ $lastVerif->response_time_ms }} ms
                        </span>
                        <span class="badge badge-blue">HTTP {{ $lastVerif->http_code }}</span>
                    @endif

                    {{-- SSL --}}
                    @if($sslDays !== null)
                        <span class="badge {{ $sslDays <= 0 ? 'badge-red' : ($sslDays < 7 ? 'badge-red' : ($sslDays < 30 ? 'badge-yellow' : 'badge-green')) }}">
                            🔒 SSL {{ $sslDays <= 0 ? 'EXPIRÉ' : $sslDays.'j' }}
                        </span>
                    @endif

                    {{-- Domaine WHOIS --}}
                    @if($domainDays !== null)
                        <span class="badge {{ $domainDays <= 0 ? 'badge-red' : ($domainDays <= 7 ? 'badge-red' : ($domainDays <= 30 ? 'badge-yellow' : 'badge-green')) }}">
                            🌐 {{ $domainDays <= 0 ? 'EXPIRÉ' : $domainDays.'j' }}
                        </span>
                    @endif

                </div>
            </div>

            <!-- Barre historique -->
            <div style="margin-bottom:12px;">
                <div style="font-size:11px; color:#64748B; margin-bottom:4px; font-weight:600;">
                    HISTORIQUE (30 dernières vérifications)
                </div>
                <div style="display:flex; gap:2px; align-items:flex-end; height:24px;">
                    @forelse($history as $v)
                        <div style="flex:1; height:{{ $v->is_up ? '100%' : '40%' }};
                                    background:{{ !$v->is_up ? '#EF4444' : ($v->response_time_ms > $site->response_threshold_ms ? '#F59E0B' : '#10B981') }};
                                    border-radius:2px; min-width:4px;"
                             title="{{ $v->checked_at->format('d/m H:i') }} — {{ $v->is_up ? 'EN LIGNE' : 'HORS LIGNE' }} — {{ $v->response_time_ms }}ms">
                        </div>
                    @empty
                        <div style="color:#94A3B8; font-size:11px;">Aucune donnée encore</div>
                    @endforelse
                </div>
            </div>

            <!-- Stats row -->
            <div style="display:flex; gap:24px; align-items:center; flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-size:18px; font-weight:800;
                                color:{{ $uptime >= 99 ? '#10B981' : ($uptime >= 95 ? '#D97706' : '#EF4444') }}">
                        {{ $uptime }}%
                    </div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">UPTIME 24H</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:18px; font-weight:800; color:#1697C2;">
                        {{ $site->frequency_min }}min
                    </div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">FRÉQUENCE</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:18px; font-weight:800; color:#0C3547;">
                        {{ $site->incidents()->whereNull('resolved_at')->count() }}
                    </div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">INCIDENTS ACTIFS</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:14px; font-weight:600; color:#64748B;">
                        {{ $lastVerif ? $lastVerif->checked_at->diffForHumans() : 'Jamais' }}
                    </div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">DERNIÈRE VÉRIF</div>
                </div>
                @if($domainDays !== null)
                <div style="text-align:center;">
                    <div style="font-size:14px; font-weight:700;
                        color:{{ $domainDays <= 0 ? '#DC2626' : ($domainDays <= 7 ? '#DC2626' : ($domainDays <= 30 ? '#D97706' : '#059669')) }}">
                        {{ $domainDays <= 0 ? 'EXPIRÉ' : $domainDays.'j' }}
                    </div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">DOMAINE</div>
                </div>
                @endif
                @if($site->response_threshold_ms)
                <div style="text-align:center;">
                    <div style="font-size:14px; font-weight:700; color:#64748B;">
                        {{ $site->response_threshold_ms }}ms
                    </div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">SEUIL ALERTE</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions droite -->
        <div style="padding:20px; display:flex; flex-direction:column; gap:8px;
                    border-left:1px solid #E0F2FE; background:#F8FAFF; min-width:160px; justify-content:center;">
            <a href="{{ route('sites.show', $site) }}" class="btn-primary"
               style="justify-content:center; padding:8px 14px; font-size:12px;">
                <i class="fas fa-eye"></i> Détail
            </a>
            <button onclick="checkNow({{ $site->id }}, this)" class="btn-primary btn-success"
                    style="justify-content:center; padding:8px 14px; font-size:12px;">
                <i class="fas fa-sync"></i> Vérifier
            </button>
            <a href="{{ route('sites.edit', $site) }}" class="btn-primary btn-warning"
               style="justify-content:center; padding:8px 14px; font-size:12px;">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <div style="display:flex; gap:6px;">
                <form action="{{ route('sites.toggle', $site) }}" method="POST" style="flex:1;">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="btn-primary {{ $site->is_active ? 'btn-danger' : 'btn-success' }}"
                            style="width:100%; justify-content:center; padding:7px 8px; font-size:11px;"
                            title="{{ $site->is_active ? 'Mettre en pause' : 'Réactiver' }}">
                        <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }}"></i>
                    </button>
                </form>
                <form action="{{ route('sites.destroy', $site) }}" method="POST"
                      onsubmit="return confirm('Supprimer {{ addslashes($site->client_name) }} et tout son historique ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-primary btn-danger"
                            style="justify-content:center; padding:7px 10px; font-size:11px;"
                            title="Supprimer définitivement">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@empty
<div class="card" style="text-align:center; padding:60px;">
    <div style="font-size:48px; margin-bottom:16px;">📡</div>
    <div style="font-size:18px; font-weight:700; color:#0C3547; margin-bottom:8px;">
        Aucun site surveillé
    </div>
    <div style="font-size:14px; color:#64748B; margin-bottom:20px;">
        Ajoutez votre premier site pour commencer le monitoring
    </div>
    <a href="{{ route('sites.create') }}" class="btn-primary" style="display:inline-flex;">
        <i class="fas fa-plus"></i> Ajouter un site
    </a>
</div>
@endforelse

<script>
function checkNow(siteId, btn) {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    btn.disabled = true;
    fetch(`/sites/${siteId}/check-now`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> Fait !';
        btn.style.background = 'linear-gradient(135deg, #059669, #047857)';
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; location.reload(); }, 1500);
    })
    .catch(() => { btn.innerHTML = orig; btn.disabled = false; });
}
</script>

@endsection