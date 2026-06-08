@extends('layouts.monitoring')
@section('title', 'Sites surveillés')
@section('subtitle', 'Liste complète des sites monitorés')

@section('content')


{{-- ═══ Header actions ═══ --}}
<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
    <form method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-light); font-size:13px;"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Rechercher un site, une URL…"
                class="form-input"
                style="padding-left:36px; width:280px;">
        </div>
        <select name="statut" class="form-select" style="width:160px;">
            <option value="">Tous statuts</option>
            <option value="online"   {{ request('statut')==='online'   ? 'selected' : '' }}>En ligne</option>
            <option value="offline"  {{ request('statut')==='offline'  ? 'selected' : '' }}>Hors ligne</option>
            <option value="slow"     {{ request('statut')==='slow'     ? 'selected' : '' }}>Lent</option>
            <option value="ssl"      {{ request('statut')==='ssl'      ? 'selected' : '' }}>SSL expire bientôt</option>
            <option value="inactive" {{ request('statut')==='inactive' ? 'selected' : '' }}>Inactif</option>
        </select>
        <button type="submit" class="btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filtrer
        </button>
        <x-export-button :route="route('export.sites')" label="Exporter Sites" />
        @if(request('search') || request('statut'))
            <a href="{{ route('sites.index') }}" class="btn-secondary btn-sm">Réinitialiser</a>
        @endif
    </form>

    @if(auth()->user()->role !== 'client')
    <a href="{{ route('sites.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Ajouter un site
    </a>
@endif
</div>
{{-- ═══ Table des sites ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-globe" style="color:var(--primary);"></i>
            Liste des sites
        </div>
        <span class="badge badge-info">{{ $sites->count() }} sites</span>
    </div>

    <div class="table-scroll" style="max-height:640px;">
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">État</th>
                    <th>Client</th>
                    <th>URL</th>
                    <th>Fréquence</th>
                    <th>SSL</th>
                    <th>Domaine</th>
                    <th>Statut</th>
                    <th style="text-align:center; min-width:260px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sites as $site)
                @php
                    $lastVerif = $site->verifications()->latest('checked_at')->first();
                    $isUp      = $lastVerif && $lastVerif->is_up;
                    $isSlow    = $isUp && $lastVerif->response_time_ms > $site->response_threshold_ms;
                    $sslDays   = $lastVerif->ssl_days_remaining ?? null;
                    $domDays   = $site->domain_expires_at
                        ? \Carbon\Carbon::parse($site->domain_expires_at)->diffInDays(now(), false) * -1
                        : null;
                @endphp
                <tr>
                    <td>
                        @if(!$lastVerif)
                            <span class="status-dot unknown"></span>
                        @elseif(!$isUp)
                            <span class="status-dot offline"></span>
                        @else
                            <span class="status-dot online"></span>
                        @endif
                    </td>

                    <td style="font-weight:600; color:var(--text);">{{ $site->client_name }}</td>

                    <td>
                        <a href="{{ $site->url }}" target="_blank"
                            style="color:var(--primary); text-decoration:none; font-size:12.5px;
                                   display:inline-flex; align-items:center; gap:5px;">
                            <i class="fas fa-external-link-alt" style="font-size:9px;"></i>
                            {{ Str::limit($site->url, 30) }}
                        </a>
                    </td>

                    <td class="text-sm">Toutes les {{ $site->frequency_min }} min</td>

                    <td>
                        @if($sslDays !== null)
                            <span class="badge {{ $sslDays > 30 ? 'badge-success' : ($sslDays > 7 ? 'badge-warning' : 'badge-danger') }}">
                                {{ $sslDays }} jours
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if($domDays !== null)
                            <span class="badge {{ $domDays > 60 ? 'badge-success' : ($domDays > 30 ? 'badge-warning' : 'badge-danger') }}">
                                {{ $domDays }} jours
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if(!$site->is_active)
                            <span class="badge badge-neutral">Désactivé</span>
                        @elseif(!$lastVerif)
                            <span class="badge badge-neutral">En attente</span>
                        @elseif(!$isUp)
                            <span class="badge badge-danger badge-dot">Hors ligne</span>
                        @elseif($isSlow)
                            <span class="badge badge-warning badge-dot">Lent</span>
                        @else
                            <span class="badge badge-success badge-dot">En ligne</span>
                        @endif
                    </td>

                    {{-- ═══ ACTIONS COMPLÈTES ═══ --}}
                    <td>
    <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
        {{-- Détails : visible pour tous --}}
        <a href="{{ route('sites.show', $site) }}" class="btn-primary btn-xs" title="Détails">
            <i class="fas fa-eye"></i>
        </a>

        {{-- Modifier/Vérifier/Désactiver/Supprimer : sauf client --}}
        @if(auth()->user()->role !== 'client')
            <a href="{{ route('sites.edit', $site) }}" class="btn-primary btn-warning btn-xs" title="Modifier">
                <i class="fas fa-edit"></i>
            </a>
            <form method="POST" action="{{ route('sites.check-now', $site) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-primary btn-success btn-xs" title="Vérifier">
                    <i class="fas fa-sync"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('sites.toggle', $site) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit"
                    class="btn-primary {{ $site->is_active ? 'btn-warning' : 'btn-success' }} btn-xs"
                    title="{{ $site->is_active ? 'Désactiver' : 'Activer' }}">
                    <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }}"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('sites.destroy', $site) }}" style="display:inline;"
                  onsubmit="return confirm('Confirmer la suppression définitive ?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn-primary btn-danger btn-xs" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endif
    </div>
</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; padding:50px; color:var(--text-muted);">
                    <i class="fas fa-globe" style="font-size:34px; color:var(--text-light); margin-bottom:10px; display:block;"></i>
                    Aucun site configuré.
                    <a href="{{ route('sites.create') }}" style="color:var(--primary); font-weight:600;">Ajouter un site</a>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection