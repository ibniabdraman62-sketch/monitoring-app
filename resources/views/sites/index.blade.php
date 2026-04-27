@extends('layouts.monitoring')
@section('title', 'Sites Surveillés')
@section('subtitle', 'Monitoring en temps réel')

@section('content')
<!-- Barre de recherche -->
<div class="card" style="margin-bottom:16px; padding:16px 24px;">
    <form method="GET" action="{{ route('sites.index') }}"
          style="display:flex; gap:12px; align-items:center;">
        <div style="flex:1; position:relative;">
            <i class="fas fa-search" style="position:absolute; left:12px; top:50%;
               transform:translateY(-50%); color:#94A3B8;"></i>
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Rechercher par nom client ou URL..."
                   class="form-input" style="padding-left:36px;">
        </div>
        <button type="submit" class="btn-primary">
            <i class="fas fa-search"></i> Rechercher
        </button>
        @if(request('search'))
        <a href="{{ route('sites.index') }}" class="btn-primary btn-danger">
            <i class="fas fa-times"></i> Effacer
        </a>
        @endif
    </form>
</div>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <span class="badge badge-green" style="font-size:12px; padding:6px 14px;">
            ● {{ $sites->where('is_active',true)->count() }} actifs
        </span>
        <span class="badge badge-red" style="font-size:12px; padding:6px 14px; margin-left:8px;">
            ● {{ $sites->where('is_active',false)->count() }} inactifs
        </span>
    </div>
    <a href="{{ route('sites.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Ajouter un site
    </a>
</div>

@forelse($sites as $site)
@php
    $lastVerif = $site->verifications()->latest('checked_at')->first();
    $uptime = 0;
    $total = $site->verifications()->where('created_at','>=',now()->subDay())->count();
    $up = $site->verifications()->where('created_at','>=',now()->subDay())->where('is_up',true)->count();
    $uptime = $total > 0 ? round($up/$total*100,1) : 100;
    $history = $site->verifications()->latest('checked_at')->take(30)->get()->reverse();
    $sslDays = null;
    if($lastVerif && $lastVerif->ssl_expires_at) {
        $sslDays = now()->diffInDays($lastVerif->ssl_expires_at, false);
    }
@endphp

<div class="card" style="margin-bottom:16px; padding:0; overflow:hidden;">
    <div style="display:grid; grid-template-columns:auto 1fr auto; gap:0; align-items:stretch;">

        <!-- Bande statut gauche -->
        <div style="width:6px; background:{{ $lastVerif && $lastVerif->is_up ? '#10B981' : ($lastVerif ? '#EF4444' : '#94A3B8') }};"></div>

        <!-- Contenu principal -->
        <div style="padding:20px 24px;">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px;">

                <!-- Statut animé -->
                <div style="position:relative; width:14px; height:14px;">
                    <span class="status-dot {{ $lastVerif && $lastVerif->is_up ? 'online' : ($lastVerif ? 'offline' : 'unknown') }}"
                          style="width:14px; height:14px; position:absolute;"></span>
                </div>

                <!-- Nom client -->
                <div>
                    <div style="font-size:16px; font-weight:700; color:#0C3547;">
                        {{ $site->client_name }}
                    </div>
                    <a href="{{ $site->url }}" target="_blank"
                       style="font-size:12px; color:#1697C2; text-decoration:none;">
                        {{ $site->url }}
                    </a>
                </div>

                <!-- Badges infos -->
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-left:auto;">
                    @if($lastVerif && $lastVerif->is_up)
                        <span class="badge badge-green">EN LIGNE</span>
                    @elseif($lastVerif)
                        <span class="badge badge-red">HORS LIGNE</span>
                    @else
                        <span class="badge badge-gray">EN ATTENTE</span>
                    @endif

                    @if($lastVerif)
                        <span class="badge {{ $lastVerif->response_time_ms > 2000 ? 'badge-red' : ($lastVerif->response_time_ms > 1000 ? 'badge-yellow' : 'badge-green') }}">
                            <i class="fas fa-clock"></i>
                            {{ $lastVerif->response_time_ms }} ms
                        </span>
                        <span class="badge badge-blue">
                            HTTP {{ $lastVerif->http_code }}
                        </span>
                    @endif

                    @if($sslDays !== null)
                        <span class="badge {{ $sslDays < 7 ? 'badge-red' : ($sslDays < 30 ? 'badge-yellow' : 'badge-green') }}">
                            🔒 SSL {{ $sslDays > 0 ? $sslDays.'j' : 'EXPIRÉ' }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Barre historique uptime 30 dernières vérifs -->
            <div style="margin-bottom:12px;">
                <div style="font-size:11px; color:#64748B; margin-bottom:4px; font-weight:600;">
                    HISTORIQUE (30 dernières vérifications)
                </div>
                <div style="display:flex; gap:2px; align-items:flex-end; height:24px;">
                    @foreach($history as $v)
                        <div style="flex:1; height:{{ $v->is_up ? '100%' : '40%' }};
                                    background:{{ $v->is_up ? '#10B981' : '#EF4444' }};
                                    border-radius:2px; min-width:4px;"
                             title="{{ $v->checked_at->format('d/m H:i') }} — {{ $v->is_up ? 'EN LIGNE' : 'HORS LIGNE' }} — {{ $v->response_time_ms }}ms">
                        </div>
                    @endforeach
                    @if($history->count() == 0)
                        <div style="color:#94A3B8; font-size:11px;">Aucune donnée encore</div>
                    @endif
                </div>
            </div>

            <!-- Stats row -->
            <div style="display:flex; gap:24px; align-items:center;">
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
                    <button type="submit" class="btn-primary {{ $site->is_active ? 'btn-danger' : 'btn-success' }}"
                            style="width:100%; justify-content:center; padding:7px 8px; font-size:11px;">
                        <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }}"></i>
                    </button>
                </form>
                <form action="{{ route('sites.destroy', $site) }}" method="POST"
                      onsubmit="return confirm('Supprimer {{ $site->client_name }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-primary btn-danger"
                            style="justify-content:center; padding:7px 10px; font-size:11px;">
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
    .then(data => {
        btn.innerHTML = '<i class="fas fa-check"></i> Fait !';
        btn.style.background = 'linear-gradient(135deg, #059669, #047857)';
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; location.reload(); }, 1500);
    })
    .catch(() => { btn.innerHTML = orig; btn.disabled = false; });
}
</script>

@endsection