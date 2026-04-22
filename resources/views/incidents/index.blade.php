@extends('layouts.monitoring')

@section('title', 'Incidents')
@section('subtitle', 'Historique des incidents détectés')

@section('content')

<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:600; color:#fff;">
            ⚠️ Historique des incidents
        </div>
        <span class="badge badge-red">{{ $incidents->total() }} incidents</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Site</th>
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
                <td style="font-weight:600; color:#fff;">
                    {{ $incident->site->client_name }}
                    <div style="font-size:11px; color:#6B7280;">{{ $incident->site->url }}</div>
                </td>
                <td>
                    @if($incident->type == 'offline')
                        <span class="badge badge-red">🔴 HORS LIGNE</span>
                    @elseif($incident->type == 'slow')
                        <span class="badge badge-yellow">🟡 LENT</span>
                    @else
                        <span class="badge badge-blue">🔵 SSL</span>
                    @endif
                </td>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $incident->started_at->format('d/m/Y H:i:s') }}
                </td>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $incident->resolved_at ? $incident->resolved_at->format('d/m/Y H:i:s') : '—' }}
                </td>
                <td>
                    @if($incident->duration_min)
                        <span style="color:#F59E0B; font-weight:600;">
                            {{ $incident->duration_min }} min
                        </span>
                    @else
                        <span style="color:#EF4444; font-weight:600;">En cours</span>
                    @endif
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
                <td colspan="6" style="text-align:center; padding:40px; color:#6B7280;">
                    ✅ Aucun incident détecté — tout fonctionne parfaitement !
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($incidents->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #2D3148; display:flex; justify-content:center; gap:8px;">
        {{ $incidents->links() }}
    </div>
    @endif
</div>

@endsection