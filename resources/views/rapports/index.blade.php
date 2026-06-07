@extends('layouts.monitoring')
@section('title', 'Rapports PDF')
@section('subtitle', 'Génération et historique des rapports de disponibilité')

@section('content')

{{-- ═══ Génération de rapport ═══ --}}
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-file-pdf" style="color:var(--primary);"></i>
        Générer un rapport
    </div>
    <p class="text-sm text-muted" style="margin-bottom:16px;">
        Sélectionnez un site pour générer un rapport PDF instantané.
    </p>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:14px;">
        @foreach($sites ?? [] as $site)
        <div style="border:1px solid var(--border); border-radius:10px; padding:16px; background:var(--bg-soft);">
            <div style="font-weight:700; color:var(--text); margin-bottom:4px;">{{ $site->client_name }}</div>
            <div class="text-xs text-muted truncate" style="margin-bottom:12px;">{{ $site->url }}</div>
            <a href="{{ route('rapports.generate', $site) }}" class="btn-primary btn-sm" style="width:100%;">
                <i class="fas fa-download"></i> Générer le rapport
            </a>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══ Envoi par email ═══ --}}
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-paper-plane" style="color:var(--primary);"></i>
        Envoyer un rapport par email
    </div>
    <p class="text-sm text-muted" style="margin-bottom:16px;">
        Choisissez le site et l'adresse email destinataire.
    </p>

    <form id="sendEmailForm" onsubmit="this.action='/rapports/'+this.site_id.value+'/send-email'"
          method="POST"
          style="display:grid; grid-template-columns:1fr 1fr auto; gap:12px; align-items:end;">
        @csrf
        <div class="form-group" style="margin:0;">
            <label class="form-label">Site</label>
            <select name="site_id" class="form-select" required>
                @foreach($sites ?? [] as $site)
                    <option value="{{ $site->id }}">{{ $site->client_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Adresse email destinataire</label>
            <input type="email" name="email" class="form-input"
                placeholder="destinataire@exemple.com" required>
        </div>
        <button type="submit" class="btn-primary btn-gold">
            <i class="fas fa-paper-plane"></i> Envoyer
        </button>
    </form>
</div>

{{-- ═══ Historique ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-history" style="color:var(--primary);"></i>
            Historique des rapports générés
        </div>
        <span class="badge badge-info">{{ ($rapports ?? collect())->count() }} rapports</span>
    </div>
    <div class="table-scroll" style="max-height:520px;">
        <table>
            <thead>
                <tr>
                    <th>Date de génération</th>
                    <th>Site</th>
                    <th>Période</th>
                    <th>Disponibilité</th>
                    <th>Incidents</th>
                    <th>Temps moyen</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rapports ?? [] as $r)
                <tr>
                    <td class="text-sm font-mono">{{ $r->generated_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                    <td style="font-weight:600;">{{ $r->site->client_name ?? '—' }}</td>
                    <td class="text-sm">
                        {{ $r->period_start ? \Carbon\Carbon::parse($r->period_start)->timezone('Africa/Casablanca')->format('d/m/Y') : '—' }}
                        →
                        {{ $r->period_end ? \Carbon\Carbon::parse($r->period_end)->timezone('Africa/Casablanca')->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        @php $pct = $r->uptime_pct ?? 0; @endphp
                        <span class="badge {{ $pct > 99 ? 'badge-success' : ($pct > 95 ? 'badge-warning' : 'badge-danger') }}">
                            {{ number_format($pct, 2) }}%
                        </span>
                    </td>
                    <td class="font-mono text-sm">{{ $r->incidents_count ?? 0 }}</td>
                    <td class="font-mono text-sm">{{ $r->avg_response_ms ?? 0 }} ms</td>
                    <td>
                        <a href="{{ route('rapports.download', $r) }}" class="btn-secondary btn-xs">
    <i class="fas fa-download"></i> Rétélécharger
</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucun rapport généré
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection