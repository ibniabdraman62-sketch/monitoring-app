@extends('layouts.monitoring')
@section('title', 'Historique des incidents')
@section('subtitle', 'Tous les incidents enregistrés par le système')

@section('content')

@php
    $totalIncidents = ($incidents ?? collect())->count();
    $activeCount    = ($incidents ?? collect())->where('is_resolved', false)->count();
    $resolvedCount  = ($incidents ?? collect())->where('is_resolved', true)->count();
    $avgDuration    = round(($incidents ?? collect())->where('is_resolved', true)->avg('duration_min') ?? 0);
@endphp

{{-- ═══ KPIs ═══ --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-label">Total incidents</div>
        <div class="kpi-value">{{ $totalIncidents }}</div>
        <i class="fas fa-list kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Incidents actifs</div>
        <div class="kpi-value">{{ $activeCount }}</div>
        <i class="fas fa-exclamation-triangle kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Résolus</div>
        <div class="kpi-value">{{ $resolvedCount }}</div>
        <i class="fas fa-check-circle kpi-icon"></i>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-label">Durée moyenne</div>
        <div class="kpi-value">{{ $avgDuration }}<span style="font-size:18px;"> min</span></div>
        <i class="fas fa-clock kpi-icon"></i>
    </div>
</div>

{{-- ═══ FILTRES AUTO-SUBMIT ═══ --}}
<div class="card mb-24" style="padding:14px 20px;">
    <form method="GET" id="incidentFilterForm" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
        <div class="form-group" style="margin:0; flex:1; min-width:160px;">
            <label class="form-label">Type</label>
            <select name="type" class="form-select auto-filter">
                <option value="">Tous</option>
                <option value="offline" {{ request('type')==='offline' ? 'selected':'' }}>Hors ligne</option>
                <option value="slow"    {{ request('type')==='slow'    ? 'selected':'' }}>Lenteur</option>
            </select>
        </div>
        <div class="form-group" style="margin:0; flex:1; min-width:160px;">
            <label class="form-label">Statut</label>
            <select name="status" class="form-select auto-filter">
                <option value="">Tous</option>
                <option value="active"   {{ request('status')==='active'   ? 'selected':'' }}>Actifs</option>
                <option value="resolved" {{ request('status')==='resolved' ? 'selected':'' }}>Résolus</option>
            </select>
        </div>
        <div class="form-group" style="margin:0; flex:1; min-width:200px;">
            <label class="form-label">Site</label>
            <select name="site_id" class="form-select auto-filter">
                <option value="">Tous les sites</option>
                @foreach($sites ?? \App\Models\Site::all() as $s)
                    <option value="{{ $s->id }}" {{ (int)request('site_id')===$s->id ? 'selected':'' }}>{{ $s->client_name }}</option>
                @endforeach
            </select>
        </div>
        @if(request('type') || request('status') || request('site_id'))
            <a href="{{ route('incidents.index') }}" class="btn-secondary btn-sm">
                <i class="fas fa-undo"></i> Réinitialiser
            </a>
        @endif
    </form>
</div>

{{-- ═══ Incidents table ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-exclamation-circle" style="color:var(--primary);"></i>
            Liste détaillée des incidents
        </div>
        <span class="badge badge-info">{{ $totalIncidents }} incidents</span>
    </div>
    <div class="table-scroll" style="max-height:600px;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Site</th>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Résolution</th>
                    <th>Durée</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @forelse($incidents ?? [] as $inc)
                <tr>
                    <td class="font-mono text-sm text-muted">#{{ $inc->id }}</td>
                    <td style="font-weight:600; color:var(--text);">{{ $inc->site->client_name ?? '—' }}</td>
                    <td>
                        @if($inc->type === 'offline')
                            <span class="badge badge-danger">Hors ligne</span>
                        @elseif($inc->type === 'slow')
                            <span class="badge badge-warning">Lenteur</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($inc->type) }}</span>
                        @endif
                    </td>
                    <td class="text-sm font-mono">{{ $inc->started_at ? $inc->started_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') : '—' }}</td>
                    <td class="text-sm font-mono">
                        @if($inc->resolved_at)
                            {{ $inc->resolved_at ? $inc->resolved_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') : '—' }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="font-mono text-sm">
                        @if($inc->duration_min)
                            {{ $inc->duration_min }} min
                        @else
                            <span class="text-muted">En cours</span>
                        @endif
                    </td>
                    <td>
                        @if($inc->resolved_at !== null)
    <span class="badge badge-success badge-dot">Résolu</span>
@else
    <span class="badge badge-danger badge-dot">Actif</span>
@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucun incident enregistré
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ Script auto-submit (FIX) ═══ --}}
<script>
    document.querySelectorAll('#incidentFilterForm .auto-filter').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('incidentFilterForm').submit();
        });
    });
</script>

@endsection