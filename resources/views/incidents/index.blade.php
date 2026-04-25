@extends('layouts.monitoring')
@section('title', 'Incidents')
@section('subtitle', 'Historique et gestion des incidents')

@section('content')

<!-- Stats incidents -->
@php
    $allIncidents = App\Models\Incident::whereHas('site', fn($q) => $q->where('user_id', auth()->id()))->get();
    $actifs = $allIncidents->whereNull('resolved_at')->count();
    $resolus = $allIncidents->whereNotNull('resolved_at')->count();
    $offline = $allIncidents->where('type','offline')->count();
    $slow = $allIncidents->where('type','slow')->count();
@endphp

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="card" style="text-align:center; border-top:3px solid #EF4444;">
        <div style="font-size:32px; font-weight:900; color:#DC2626;">{{ $actifs }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">INCIDENTS ACTIFS</div>
    </div>
    <div class="card" style="text-align:center; border-top:3px solid #10B981;">
        <div style="font-size:32px; font-weight:900; color:#059669;">{{ $resolus }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">RÉSOLUS</div>
    </div>
    <div class="card" style="text-align:center; border-top:3px solid #1697C2;">
        <div style="font-size:32px; font-weight:900; color:#1697C2;">{{ $offline }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">PANNES</div>
    </div>
    <div class="card" style="text-align:center; border-top:3px solid #D97706;">
        <div style="font-size:32px; font-weight:900; color:#D97706;">{{ $slow }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">LENTEURS</div>
    </div>
</div>

<!-- Timeline incidents -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            ⚠️ Historique des incidents
        </div>
        <span class="badge badge-red" style="font-size:12px;">
            {{ $incidents->total() }} au total
        </span>
    </div>

    @forelse($incidents as $incident)
    <div style="padding:16px 24px; border-bottom:1px solid #F0F9FF;
                display:grid; grid-template-columns:auto 1fr auto; gap:16px; align-items:center;">

        <!-- Icone type -->
        <div style="width:44px; height:44px; border-radius:50%; display:flex;
                    align-items:center; justify-content:center; font-size:18px;
                    background:{{ $incident->type == 'offline' ? '#FEE2E2' : ($incident->type == 'slow' ? '#FEF3C7' : '#EDE9FE') }}">
            {{ $incident->type == 'offline' ? '🔴' : ($incident->type == 'slow' ? '🟡' : '🔵') }}
        </div>

        <!-- Infos -->
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                <span style="font-size:14px; font-weight:700; color:#0C3547;">
                    {{ $incident->site->client_name }}
                </span>
                <span class="badge {{ $incident->type == 'offline' ? 'badge-red' : ($incident->type == 'slow' ? 'badge-yellow' : 'badge-blue') }}">
                    {{ strtoupper($incident->type) }}
                </span>
                @if($incident->resolved_at)
                    <span class="badge badge-green">✅ RÉSOLU</span>
                @else
                    <span class="badge badge-red" style="animation: pulse-anim 1.5s infinite;">
                        🔴 EN COURS
                    </span>
                @endif
            </div>
            <div style="font-size:12px; color:#64748B;">
                <i class="fas fa-globe" style="color:#1697C2;"></i>
                {{ $incident->site->url }}
            </div>
            <div style="font-size:12px; color:#94A3B8; margin-top:4px;">
                <i class="fas fa-clock"></i>
                Début : {{ $incident->started_at->format('d/m/Y à H:i:s') }}
                @if($incident->resolved_at)
                    &nbsp;→&nbsp;
                    <i class="fas fa-check-circle" style="color:#10B981;"></i>
                    Résolu : {{ $incident->resolved_at->format('d/m/Y à H:i:s') }}
                @endif
            </div>
        </div>

        <!-- Durée -->
        <div style="text-align:right; min-width:100px;">
            @if($incident->duration_min)
                <div style="font-size:20px; font-weight:900;
                            color:{{ $incident->duration_min > 60 ? '#DC2626' : '#D97706' }}">
                    {{ $incident->duration_min > 60
                        ? floor($incident->duration_min/60).'h '.($incident->duration_min%60).'min'
                        : $incident->duration_min.' min' }}
                </div>
                <div style="font-size:10px; color:#64748B; font-weight:700;">DURÉE</div>
            @elseif(!$incident->resolved_at)
                <div style="font-size:14px; font-weight:800; color:#DC2626;">
                    {{ $incident->started_at->diffForHumans() }}
                </div>
                <div style="font-size:10px; color:#DC2626; font-weight:700;">EN COURS</div>
            @endif
        </div>

    </div>
    @empty
    <div style="padding:60px; text-align:center;">
        <div style="font-size:48px; margin-bottom:12px;">✅</div>
        <div style="font-size:18px; font-weight:700; color:#059669;">
            Aucun incident détecté !
        </div>
        <div style="font-size:13px; color:#64748B; margin-top:6px;">
            Tous vos sites fonctionnent parfaitement.
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($incidents->hasPages())
<div style="margin-top:16px; display:flex; justify-content:center;">
    {{ $incidents->links() }}
</div>
@endif

@endsection