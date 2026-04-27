@extends('layouts.monitoring')
@section('title', 'Rapports PDF')
@section('subtitle', 'Génération et historique des rapports')

@section('content')

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:24px;">

    <!-- Générer un rapport -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-file-pdf" style="color:#EF4444;"></i>
            Générer un rapport
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($sites as $site)
            <div style="display:flex; align-items:center; gap:10px; padding:12px;
                        background:#F8FAFF; border:1px solid #E0F2FE; border-radius:10px;">
                <div style="flex:1;">
                    <div style="font-weight:700; color:#0C3547; font-size:13px;">{{ $site->client_name }}</div>
                    <div style="font-size:11px; color:#64748B;">{{ $site->url }}</div>
                </div>
                <a href="{{ route('rapports.generate', $site) }}"
                   class="btn-primary" style="padding:7px 14px; font-size:11px; flex-shrink:0;">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Envoyer par email -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-envelope" style="color:#1697C2;"></i>
            Envoyer un rapport par email
        </div>
        <form method="POST" action="{{ route('rapports.send-email', $sites->first()) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Site</label>
                <select name="site_id" class="form-input" id="site-select"
                        onchange="updateSendAction(this)">
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" data-url="{{ route('rapports.send-email', $site) }}">
                        {{ $site->client_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Adresse email destinataire</label>
                <input type="email" name="email" class="form-input"
                       placeholder="client@exemple.com" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">
                <i class="fas fa-paper-plane"></i> Envoyer le rapport
            </button>
        </form>
    </div>
</div>

<!-- Historique des rapports générés -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            <i class="fas fa-history" style="color:#1697C2;"></i> Historique des rapports générés
        </div>
        <span class="badge badge-blue">{{ $rapports->count() }} rapports</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Période</th>
                <th>Uptime</th>
                <th>Incidents</th>
                <th>Tps réponse moy.</th>
                <th>Généré le</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rapports as $rapport)
        <tr>
            <td style="font-weight:700; color:#0C3547;">{{ $rapport->site->client_name ?? '—' }}</td>
            <td style="font-size:11px; color:#64748B;">
                {{ \Carbon\Carbon::parse($rapport->period_start)->format('d/m/Y') }}
                → {{ \Carbon\Carbon::parse($rapport->period_end)->format('d/m/Y') }}
            </td>
            <td>
                <span style="font-weight:700;
                    color:{{ $rapport->uptime_pct >= 99 ? '#059669' : ($rapport->uptime_pct >= 95 ? '#D97706' : '#DC2626') }}">
                    {{ $rapport->uptime_pct }}%
                </span>
            </td>
            <td style="text-align:center; font-weight:700;">{{ $rapport->incidents_count }}</td>
            <td style="font-weight:700;">{{ $rapport->avg_response_ms }}ms</td>
            <td style="font-size:11px; color:#64748B;">
                {{ $rapport->generated_at->format('d/m/Y H:i') }}
            </td>
            <td>
                <a href="{{ route('rapports.generate', $rapport->site) }}"
                   class="btn-primary" style="padding:5px 10px; font-size:11px;">
                    <i class="fas fa-redo"></i> Régénérer
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding:40px; color:#64748B;">
                Aucun rapport généré encore. Cliquez sur "Télécharger" ci-dessus.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

<script>
function updateSendAction(select) {
    const url = select.options[select.selectedIndex].dataset.url;
    select.closest('form').action = url;
}
</script>
@endsection