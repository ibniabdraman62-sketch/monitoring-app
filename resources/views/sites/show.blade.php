@extends('layouts.monitoring')

@section('title', $site->client_name)
@section('subtitle', $site->url)

@section('content')

<!-- Infos + Actions -->
<div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:24px;">

    <div class="card">
        <div class="card-title">📋 Informations du site</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <div style="font-size:11px; color:#6B7280; margin-bottom:4px;">URL</div>
                <a href="{{ $site->url }}" target="_blank" style="color:#818CF8;">{{ $site->url }}</a>
            </div>
            <div>
                <div style="font-size:11px; color:#6B7280; margin-bottom:4px;">Statut</div>
                @if($site->is_active)
                    <span class="badge badge-green">● Actif</span>
                @else
                    <span class="badge badge-red">● Inactif</span>
                @endif
            </div>
            <div>
                <div style="font-size:11px; color:#6B7280; margin-bottom:4px;">Fréquence</div>
                <span style="color:#fff; font-weight:600;">{{ $site->frequency_min }} min</span>
            </div>
            <div>
                <div style="font-size:11px; color:#6B7280; margin-bottom:4px;">Seuil réponse</div>
                <span style="color:#fff; font-weight:600;">{{ $site->response_threshold_ms }} ms</span>
            </div>
            <div>
                <div style="font-size:11px; color:#6B7280; margin-bottom:4px;">SSL</div>
                <span style="color:#10B981;">🔒 {{ $site->ssl_check ? 'Activé' : 'Désactivé' }}</span>
            </div>
            <div>
                <div style="font-size:11px; color:#6B7280; margin-bottom:4px;">Ajouté le</div>
                <span style="color:#9CA3AF; font-size:12px;">{{ $site->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <div class="card" style="display:flex; flex-direction:column; gap:10px;">
        <div class="card-title">⚡ Actions rapides</div>
        <button onclick="checkNow({{ $site->id }})" class="btn-primary btn-success" style="width:100%; justify-content:center;">
            <i class="fas fa-sync"></i> Vérifier maintenant
        </button>
        <a href="{{ route('sites.edit', $site) }}" class="btn-primary btn-warning" style="width:100%; justify-content:center;">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <a href="{{ route('rapports.generate', $site) }}" class="btn-primary" style="width:100%; justify-content:center;">
            <i class="fas fa-file-pdf"></i> Générer rapport PDF
        </a>
        <form action="{{ route('sites.toggle', $site) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="btn-primary {{ $site->is_active ? 'btn-danger' : 'btn-success' }}" style="width:100%; justify-content:center;">
                <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }}"></i>
                {{ $site->is_active ? 'Désactiver' : 'Activer' }}
            </button>
        </form>
    </div>
</div>

<!-- Graphique -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title">📈 Temps de réponse — 24 dernières heures</div>
    <canvas id="responseChart" height="80"></canvas>
</div>
<!-- Graphique disponibilité 30 jours -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title">📊 Disponibilité par jour — 30 derniers jours</div>
    <canvas id="uptimeChart" height="80"></canvas>
</div>
<!-- Dernières vérifications -->
<div class="table-wrapper" style="margin-bottom:24px;">
    <div class="table-header">
        <div style="font-size:15px; font-weight:600; color:#fff;">🔍 Dernières vérifications</div>
        <span class="badge badge-blue">{{ $verifications->count() }} entrées</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date / Heure</th>
                <th>HTTP</th>
                <th>Temps</th>
                <th>SSL</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($verifications as $v)
            <tr>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $v->checked_at->format('d/m/Y H:i:s') }}
                </td>
                <td>
                    <span class="badge {{ $v->http_code == 200 ? 'badge-green' : 'badge-red' }}">
                        {{ $v->http_code }}
                    </span>
                </td>
                <td>
                    <span style="color:{{ $v->response_time_ms > 2000 ? '#EF4444' : '#10B981' }}; font-weight:600;">
                        {{ $v->response_time_ms }} ms
                    </span>
                </td>
                <td>
                    @if($v->ssl_valid)
                        <span class="ssl-ok">🔒 Valide</span>
                    @else
                        <span class="ssl-danger">🔓 Invalide</span>
                    @endif
                </td>
                <td>
                    @if($v->is_up)
                        <span class="badge badge-green">EN LIGNE</span>
                    @else
                        <span class="badge badge-red">HORS LIGNE</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:40px; color:#6B7280;">
                    Aucune vérification encore effectuée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Incidents -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:600; color:#fff;">⚠️ Incidents récents</div>
        <span class="badge badge-red">{{ $incidents->count() }} incidents</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Début</th>
                <th>Résolu</th>
                <th>Durée</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incidents as $incident)
            <tr>
                <td>
                    <span class="badge {{ $incident->type == 'offline' ? 'badge-red' : 'badge-yellow' }}">
                        {{ strtoupper($incident->type) }}
                    </span>
                </td>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $incident->started_at->format('d/m/Y H:i') }}
                </td>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $incident->resolved_at ? $incident->resolved_at->format('d/m/Y H:i') : '—' }}
                </td>
                <td style="color:#F59E0B; font-weight:600;">
                    {{ $incident->duration_min ? $incident->duration_min . ' min' : 'En cours' }}
                </td>
                <td>
                    @if($incident->resolved_at)
                        <span class="badge badge-green">✅ Résolu</span>
                    @else
                        <span class="badge badge-red">🔴 Actif</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:40px; color:#10B981;">
                    ✅ Aucun incident détecté !
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const verifications = @json($verifications->map(fn($v) => [
    'x' => $v->checked_at->format('H:i:s'),
    'y' => $v->response_time_ms
])->values());

new Chart(document.getElementById('responseChart'), {
    type: 'line',
    data: {
        datasets: [{
            label: '{{ $site->client_name }}',
            data: verifications,
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79,70,229,0.1)',
            tension: 0.4, fill: true, pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        parsing: { xAxisKey: 'x', yAxisKey: 'y' },
        plugins: { legend: { labels: { color: '#9CA3AF' } } },
        scales: {
            x: { ticks: { color: '#6B7280' }, grid: { color: '#1E2235' } },
            y: { ticks: { color: '#6B7280' }, grid: { color: '#1E2235' }, beginAtZero: true }
        }
    }
});

function checkNow(siteId) {
    const btn = event.target;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';
    btn.disabled = true;
    fetch(`/sites/${siteId}/check-now`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(`✅ Vérification effectuée !\nStatut : ${data.is_up ? '🟢 EN LIGNE' : '🔴 HORS LIGNE'}\nTemps : ${data.response_time} ms\nHTTP : ${data.http_code}`);
        location.reload();
    });
}

// Graphique disponibilité 30 jours
@php
    $uptimeData = [];
    for ($i = 29; $i >= 0; $i--) {
        $day = now()->subDays($i);
        $total = $site->verifications()->whereDate('checked_at', $day)->count();
        $up = $site->verifications()->whereDate('checked_at', $day)->where('is_up', true)->count();
        $uptimeData[] = [
            'x' => $day->format('d/m'),
            'y' => $total > 0 ? round($up/$total*100, 1) : null,
        ];
    }
@endphp
const uptimeData = @json(array_filter($uptimeData, fn($d) => $d['y'] !== null));

new Chart(document.getElementById('uptimeChart'), {
    type: 'bar',
    data: {
        datasets: [{
            label: 'Disponibilité (%)',
            data: uptimeData,
            backgroundColor: uptimeData.map(d => d.y >= 99 ? '#10B981' : d.y >= 95 ? '#D97706' : '#EF4444'),
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        parsing: { xAxisKey: 'x', yAxisKey: 'y' },
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#64748B' }, grid: { display: false } },
            y: { min: 0, max: 100, ticks: { color: '#64748B', callback: v => v+'%' }, grid: { color: '#F1F5F9' } }
        }
    }
});
</script>
<!-- Bloc WHOIS Domaine -->
<div class="card" style="margin-top:16px;">
    <div class="card-title">
        <i class="fas fa-globe" style="color:#1697C2;"></i>
        Informations WHOIS — Domaine
    </div>
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px;">
        <div style="text-align:center; padding:16px; background:#F0F9FF; border-radius:10px;">
            <div style="font-size:16px; font-weight:800; color:#0C3547;">
                {{ $site->domain_registrar ?? '—' }}
            </div>
            <div style="font-size:11px; color:#64748B; margin-top:4px; font-weight:600;">REGISTRAR</div>
        </div>
        <div style="text-align:center; padding:16px; background:#F0F9FF; border-radius:10px;">
            @php
                $domainDaysLeft = $site->domain_expires_at
                    ? now()->diffInDays(\Carbon\Carbon::parse($site->domain_expires_at), false)
                    : null;
            @endphp
            <div style="font-size:16px; font-weight:800;
                color:{{ $domainDaysLeft !== null ? ($domainDaysLeft <= 7 ? '#DC2626' : ($domainDaysLeft <= 30 ? '#D97706' : '#059669')) : '#94A3B8' }}">
                {{ $site->domain_expires_at
                    ? \Carbon\Carbon::parse($site->domain_expires_at)->format('d/m/Y')
                    : '—' }}
            </div>
            <div style="font-size:11px; color:#64748B; margin-top:4px; font-weight:600;">EXPIRATION DOMAINE</div>
        </div>
        <div style="text-align:center; padding:16px; background:#F0F9FF; border-radius:10px;">
            <div style="font-size:16px; font-weight:800; color:#0C3547;">
                {{ $site->domain_created_at
                    ? \Carbon\Carbon::parse($site->domain_created_at)->format('d/m/Y')
                    : '—' }}
            </div>
            <div style="font-size:11px; color:#64748B; margin-top:4px; font-weight:600;">DATE CRÉATION</div>
        </div>
        <div style="text-align:center; padding:16px; background:#F0F9FF; border-radius:10px;">
            @if($domainDaysLeft !== null)
                <span class="badge {{ $domainDaysLeft <= 7 ? 'badge-red' : ($domainDaysLeft <= 30 ? 'badge-yellow' : 'badge-green') }}"
                      style="font-size:13px; padding:6px 14px;">
                    {{ $domainDaysLeft > 0 ? $domainDaysLeft.'j restants' : 'EXPIRÉ' }}
                </span>
            @else
                <span style="color:#94A3B8; font-size:16px; font-weight:800;">—</span>
            @endif
            <div style="font-size:11px; color:#64748B; margin-top:8px; font-weight:600;">STATUT</div>
        </div>
    </div>

    <div style="margin-top:16px; padding:12px 16px; background:#FEF3C7;
                border-radius:8px; border-left:4px solid #D97706;
                display:flex; align-items:center; gap:8px;">
        <i class="fas fa-info-circle" style="color:#D97706;"></i>
        <span style="font-size:12px; color:#92400E; font-weight:600;">
            Les données WHOIS sont mises à jour automatiquement chaque semaine via le Cron Job
            <code>monitor:check-whois</code>.
            @if($site->whois_checked_at)
                Dernière vérification : {{ \Carbon\Carbon::parse($site->whois_checked_at)->format('d/m/Y à H:i') }}
            @else
                Aucune vérification WHOIS effectuée encore.
            @endif
        </span>
    </div>
</div>
@endsection