@extends('layouts.monitoring')
@section('title', 'Historique des alertes')
@section('subtitle', 'Toutes les alertes envoyées par le système')

@section('content')

{{-- ═══ FILTRES AVEC AUTO-SUBMIT ═══ --}}
<div class="card mb-24" style="padding:14px 20px;">
    <form method="GET" id="filterForm" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
        <div class="form-group" style="margin:0; flex:1; min-width:200px;">
            <label class="form-label">Type d'alerte</label>
            <select name="type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Tous types</option>
                <option value="down"     {{ request('type')==='down'     ? 'selected':'' }}>Site hors ligne</option>
                <option value="slow"     {{ request('type')==='slow'     ? 'selected':'' }}>Lenteur détectée</option>
                <option value="resolved" {{ request('type')==='resolved' ? 'selected':'' }}>Résolution</option>
                <option value="ssl"      {{ request('type')==='ssl'      ? 'selected':'' }}>Certificat SSL</option>
                <option value="domain"   {{ request('type')==='domain'   ? 'selected':'' }}>Domaine</option>
            </select>
        </div>
        <div class="form-group" style="margin:0; flex:1; min-width:200px;">
            <label class="form-label">Site</label>
            <select name="site_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Tous les sites</option>
                @foreach($sites ?? \App\Models\Site::all() as $s)
                    <option value="{{ $s->id }}" {{ (int)request('site_id')===$s->id ? 'selected':'' }}>{{ $s->client_name }}</option>
                @endforeach
            </select>
        </div>
        @if(request('type') || request('site_id'))
            <a href="{{ route('alertes.index') }}" class="btn-secondary btn-sm">
                <i class="fas fa-undo"></i> Réinitialiser
            </a>
        @endif
    </form>
</div>

{{-- ═══ Alerts table ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-bell" style="color:var(--primary);"></i>
            Liste des alertes envoyées
        </div>
        <span class="badge badge-info">{{ ($alertes ?? collect())->count() }} alertes</span>
    </div>

    <div class="table-scroll" style="max-height:640px;">
        <table>
            <thead>
                <tr>
                    <th>Date / heure</th>
                    <th>Site</th>
                    <th>Type</th>
                    <th>Destinataire</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @forelse($alertes ?? [] as $a)
                <tr>
                    <td class="text-sm font-mono">{{ $a->sent_at ? $a->sent_at->timezone('Africa/Casablanca')->format('d/m/Y H:i:s') : '—' }}</td>
                    <td style="font-weight:600; color:var(--text);">{{ $a->incident->site->client_name ?? '—' }}</td>
                    <td>
                        @if($a->type === 'down')
                            <span class="badge badge-danger">Site hors ligne</span>
                        @elseif($a->type === 'slow')
                            <span class="badge badge-warning">Lenteur</span>
                        @elseif($a->type === 'resolved')
                            <span class="badge badge-success">Résolution</span>
                        @elseif($a->type === 'ssl')
                            <span class="badge badge-warning">Certificat SSL</span>
                        @elseif($a->type === 'domain')
                            <span class="badge badge-warning">Domaine</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($a->type) }}</span>
                        @endif
                    </td>
                    <td class="text-sm">{{ $a->email_to ?? $a->sent_to ?? '—' }}</td>
                    <td>
                        @if($a->is_resolved_alert ?? false)
                            <span class="badge badge-success">Résolu</span>
                        @else
                            <span class="badge badge-neutral">Notifié</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucune alerte enregistrée
                </td></tr>
            @endforelse
            </tbody>
        </table>
        @if($alertes->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border); background:var(--bg-soft);">
        {{ $alertes->links('vendor.pagination.monitorpro') }}
    </div>
@endif
    </div>
</div>

@endsection