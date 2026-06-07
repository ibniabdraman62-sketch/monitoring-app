@extends('layouts.monitoring')
@section('title', 'Historique des incidents')
@section('subtitle', 'Tous les incidents enregistrés par le système')

@section('content')

{{-- ═══ NOUVELLE PALETTE KPI CARDS MODERNE ═══ --}}
<style>
.kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 20px !important;
    margin-bottom: 28px !important;
}

.kpi-card {
    position: relative;
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 16px !important;
    padding: 24px 24px 24px 28px !important;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
}

.kpi-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    border-radius: 16px 0 0 16px;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10) !important;
}

.kpi-card .kpi-label {
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #6B7280 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    margin-bottom: 12px !important;
}

.kpi-card .kpi-value {
    font-size: 38px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    letter-spacing: -1px !important;
}

.kpi-card .kpi-icon {
    position: absolute !important;
    right: 20px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 56px !important;
    opacity: 0.08 !important;
}

/* ─── Card BLUE (Total sites) ─── */
.kpi-card.blue::before { background: linear-gradient(180deg, #6366F1, #4F46E5); }
.kpi-card.blue .kpi-value { color: #4F46E5 !important; }
.kpi-card.blue .kpi-icon { color: #4F46E5 !important; }
.kpi-card.blue:hover { border-color: #C7D2FE !important; }

/* ─── Card GREEN (Sites actifs) ─── */
.kpi-card.green::before { background: linear-gradient(180deg, #34D399, #10B981); }
.kpi-card.green .kpi-value { color: #059669 !important; }
.kpi-card.green .kpi-icon { color: #10B981 !important; }
.kpi-card.green:hover { border-color: #A7F3D0 !important; }

/* ─── Card RED (Incidents actifs) ─── */
.kpi-card.red::before { background: linear-gradient(180deg, #F87171, #EF4444); }
.kpi-card.red .kpi-value { color: #DC2626 !important; }
.kpi-card.red .kpi-icon { color: #EF4444 !important; }
.kpi-card.red:hover { border-color: #FECACA !important; }

/* ─── Card GOLD (Disponibilité) ─── */
.kpi-card.gold::before { background: linear-gradient(180deg, #FBBF24, #F59E0B); }
.kpi-card.gold .kpi-value { color: #D97706 !important; }
.kpi-card.gold .kpi-icon { color: #F59E0B !important; }
.kpi-card.gold:hover { border-color: #FDE68A !important; }

/* ─── Responsive ─── */
@media (max-width: 1100px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 600px) {
    .kpi-grid { grid-template-columns: 1fr !important; }
}
</style>

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

    {{-- ═══ Pagination ═══ --}}
    @if($incidents->hasPages())
        <div style="padding:16px 20px; border-top:1px solid var(--border); background:var(--bg-soft);">
            {{ $incidents->links('vendor.pagination.monitorpro') }}
        </div>
    @endif
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