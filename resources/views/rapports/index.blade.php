@extends('layouts.monitoring')

@section('title', 'Rapports PDF')
@section('subtitle', 'Génération et téléchargement des rapports')

@section('content')

<!-- Générer -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title">📄 Générer un nouveau rapport (7 derniers jours)</div>
    <div style="display:flex; flex-wrap:wrap; gap:10px;">
        @foreach(auth()->user()->sites as $site)
        <a href="{{ route('rapports.generate', $site) }}" class="btn-primary">
            <i class="fas fa-file-pdf"></i> {{ $site->client_name }}
        </a>
        @endforeach
    </div>
</div>

<!-- Liste -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:600; color:#fff;">📋 Rapports générés</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Période</th>
                <th>Uptime</th>
                <th>Généré le</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rapports as $rapport)
            <tr>
                <td style="font-weight:700; color:#0C3547;">{{ $rapport->site->client_name }}</td>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $rapport->period_start->format('d/m/Y') }} →
                    {{ $rapport->period_end->format('d/m/Y') }}
                </td>
                <td>
                    <span class="badge {{ $rapport->uptime_pct >= 99 ? 'badge-green' : ($rapport->uptime_pct >= 95 ? 'badge-yellow' : 'badge-red') }}">
                        {{ $rapport->uptime_pct }}%
                    </span>
                </td>
                <td style="color:#9CA3AF; font-size:12px;">
                    {{ $rapport->generated_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    <a href="{{ route('rapports.download', $rapport) }}" class="btn-primary" style="padding:5px 12px; font-size:11px;">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:40px; color:#6B7280;">
                    Aucun rapport généré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection