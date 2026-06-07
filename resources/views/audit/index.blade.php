@extends('layouts.monitoring')
@section('title', 'Historique d\'audit')
@section('subtitle', 'Traçabilité complète des actions du système')

@section('content')

{{-- ═══ KPIs STATISTIQUES ═══ --}}
<div class="kpi-grid mb-24">
    <div class="kpi-card blue">
        <div class="kpi-label">Total actions</div>
        <div class="kpi-value">{{ number_format($stats['total']) }}</div>
        <i class="fas fa-clock-rotate-left kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Aujourd'hui</div>
        <div class="kpi-value">{{ $stats['today'] }}</div>
        <i class="fas fa-calendar-day kpi-icon"></i>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-label">Cette semaine</div>
        <div class="kpi-value">{{ $stats['this_week'] }}</div>
        <i class="fas fa-chart-line kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Échecs</div>
        <div class="kpi-value">{{ $stats['failures'] }}</div>
        <i class="fas fa-triangle-exclamation kpi-icon"></i>
    </div>
</div>

{{-- ═══ FILTRES ═══ --}}
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-filter" style="color:var(--primary);"></i>
        Filtres de recherche
    </div>

    <form method="GET" action="{{ route('audit.index') }}">
        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px;">
            {{-- Recherche --}}
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Recherche libre</label>
                <input type="text" name="search" class="form-input"
                       value="{{ request('search') }}"
                       placeholder="Description, utilisateur, IP, site...">
            </div>

            {{-- Utilisateur --}}
            <div class="form-group">
                <label class="form-label">Utilisateur</label>
                <select name="user_id" class="form-select">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Catégorie --}}
            <div class="form-group">
                <label class="form-label">Catégorie</label>
                <select name="category" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Action --}}
            <div class="form-group">
                <label class="form-label">Action</label>
                <select name="action" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($actions as $key => $label)
                        <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Statut --}}
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Succès</option>
                    <option value="failure" {{ request('status') === 'failure' ? 'selected' : '' }}>Échec</option>
                </select>
            </div>

            {{-- Date début --}}
            <div class="form-group">
                <label class="form-label">Du</label>
                <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
            </div>

            {{-- Date fin --}}
            <div class="form-group">
                <label class="form-label">Au</label>
                <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
            </div>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
            <a href="{{ route('audit.index') }}" class="btn-secondary">
                <i class="fas fa-rotate-left"></i> Réinitialiser
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-magnifying-glass"></i> Filtrer
            </button>
        </div>
    </form>
</div>

{{-- ═══ TABLEAU DES LOGS ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            Journal d'audit ({{ $logs->total() }} entrées)
        </div>
    </div>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th style="width:160px;">Date / Heure</th>
                    <th style="width:180px;">Utilisateur</th>
                    <th style="width:110px;">Catégorie</th>
                    <th>Description</th>
                    <th style="width:130px;">IP</th>
                    <th style="width:80px; text-align:center;">Statut</th>
                    <th style="width:80px; text-align:center;">Détails</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-xs font-mono" style="color:var(--text);">
                        {{ $log->created_at->timezone('Africa/Casablanca')->format('d/m/Y') }}<br>
                        <span class="text-muted">{{ $log->created_at->timezone('Africa/Casablanca')->format('H:i:s') }}</span>
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text); font-size:13px;">
                            {{ $log->user_name ?? '[Anonyme]' }}
                        </div>
                        @if($log->user_role)
                            <div class="text-xs text-muted">
                                @switch($log->user_role)
                                    @case('super_admin') Super Admin @break
                                    @case('agent') Agent @break
                                    @case('client') Client @break
                                    @default {{ $log->user_role }}
                                @endswitch
                            </div>
                        @endif
                    </td>
                    <td>
                        @php
                            $catColors = [
                                'auth'    => '#F59E0B',
                                'site'    => '#4F46E5',
                                'user'    => '#A855F7',
                                'report'  => '#10B981',
                                'profile' => '#6B7280',
                                'system'  => '#1F2937',
                            ];
                            $catIcons = [
                                'auth'    => 'fa-lock',
                                'site'    => 'fa-globe',
                                'user'    => 'fa-user',
                                'report'  => 'fa-file-pdf',
                                'profile' => 'fa-user-pen',
                                'system'  => 'fa-gear',
                            ];
                            $color = $catColors[$log->category] ?? '#6B7280';
                            $icon  = $catIcons[$log->category] ?? 'fa-circle-info';
                        @endphp
                        <span style="display:inline-flex; align-items:center; gap:6px;
                                     padding:4px 10px; border-radius:14px;
                                     background:{{ $color }}20;
                                     color:{{ $color }}; font-size:11px; font-weight:700;">
                            <i class="fas {{ $icon }}"></i>
                            {{ $categories[$log->category] ?? $log->category }}
                        </span>
                    </td>
                    <td style="font-size:13px; color:var(--text);">
                        {{ $log->description }}
                        @if($log->model_name)
                            <div class="text-xs text-muted" style="margin-top:2px;">
                                <i class="fas fa-link" style="font-size:9px;"></i>
                                {{ class_basename($log->model_type ?? '') }} #{{ $log->model_id }}
                            </div>
                        @endif
                    </td>
                    <td class="font-mono text-xs text-muted">{{ $log->ip_address }}</td>
                    <td style="text-align:center;">
                        @if($log->status === 'success')
                            <span class="badge badge-success" title="Succès">
                                <i class="fas fa-check"></i>
                            </span>
                        @else
                            <span class="badge badge-danger" title="Échec">
                                <i class="fas fa-xmark"></i>
                            </span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <button onclick="showAuditDetail({{ $log->id }})"
                                class="btn-primary btn-xs" title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size:32px; opacity:0.3;"></i>
                        <div style="margin-top:10px;">Aucune entrée d'audit ne correspond aux filtres.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="padding:16px 20px; border-top:1px solid var(--border); background:var(--bg-soft);">
            {{ $logs->links('vendor.pagination.monitorpro') }}
        </div>
    @endif
</div>

{{-- ═══ MODAL DÉTAILS ═══ --}}
<div id="auditModal" class="modal-overlay">
    <div class="modal" style="max-width:720px;">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-circle-info" style="color:var(--primary); margin-right:6px;"></i>
                Détails de l'entrée d'audit
            </div>
            <button class="modal-close" onclick="closeAuditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body" id="auditModalBody">
            <div style="text-align:center; padding:40px;">
                <i class="fas fa-spinner fa-spin" style="font-size:24px; color:var(--primary);"></i>
                <div style="margin-top:10px; color:var(--text-muted);">Chargement...</div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" onclick="closeAuditModal()" class="btn-secondary">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
async function showAuditDetail(id) {
    const modal = document.getElementById('auditModal');
    const body  = document.getElementById('auditModalBody');

    modal.classList.add('active');
    body.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px; color:var(--primary);"></i><div style="margin-top:10px; color:var(--text-muted);">Chargement...</div></div>';

    try {
        const res = await fetch(`/audit/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        let html = `
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px;">
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px;">UTILISATEUR</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-top:4px;">${data.user_name}</div>
                    <div class="text-xs text-muted">${data.user_role || ''}</div>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px;">DATE / HEURE</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-top:4px; font-family:monospace;">${data.created_at}</div>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px;">ACTION</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-top:4px;">${data.action_label}</div>
                    <div class="text-xs text-muted font-mono">${data.action}</div>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px;">CATÉGORIE</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-top:4px;">${data.category_label}</div>
                </div>
            </div>

            <div style="background:var(--bg-soft); padding:14px; border-radius:8px; margin-bottom:14px;">
                <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">DESCRIPTION</div>
                <div style="font-size:13px; color:var(--text);">${data.description}</div>
            </div>
        `;

        if (data.model_type) {
            html += `
                <div style="background:#EEF2FF; border-left:3px solid #4F46E5; padding:12px; border-radius:6px; margin-bottom:14px;">
                    <div style="font-size:11px; font-weight:700; color:#4F46E5; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">ÉLÉMENT CONCERNÉ</div>
                    <div style="font-size:13px; color:var(--text);">
                        <strong>${data.model_type}</strong> #${data.model_id}
                        ${data.model_name ? ` — ${data.model_name}` : ''}
                    </div>
                </div>
            `;
        }

        if (data.old_values || data.new_values) {
            html += '<div style="margin-bottom:14px;">';
            html += '<div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">CHANGEMENTS DÉTECTÉS</div>';

            if (data.old_values) {
                html += `
                    <div style="margin-bottom:8px;">
                        <div style="font-size:12px; font-weight:600; color:#DC2626; margin-bottom:4px;">
                            <i class="fas fa-minus-circle"></i> Anciennes valeurs
                        </div>
                        <pre style="background:#FEF2F2; border-left:3px solid #DC2626; padding:10px; border-radius:6px; font-size:11px; color:var(--text); overflow-x:auto; margin:0;">${JSON.stringify(data.old_values, null, 2)}</pre>
                    </div>
                `;
            }

            if (data.new_values) {
                html += `
                    <div>
                        <div style="font-size:12px; font-weight:600; color:#059669; margin-bottom:4px;">
                            <i class="fas fa-plus-circle"></i> Nouvelles valeurs
                        </div>
                        <pre style="background:#F0FDF4; border-left:3px solid #059669; padding:10px; border-radius:6px; font-size:11px; color:var(--text); overflow-x:auto; margin:0;">${JSON.stringify(data.new_values, null, 2)}</pre>
                    </div>
                `;
            }

            html += '</div>';
        }

        html += `
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; padding-top:14px; border-top:1px solid var(--border);">
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">ADRESSE IP</div>
                    <div class="font-mono text-xs" style="margin-top:4px;">${data.ip_address || '—'}</div>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">STATUT</div>
                    <div style="margin-top:4px;">
                        ${data.status === 'success'
                            ? '<span class="badge badge-success"><i class="fas fa-check"></i> Succès</span>'
                            : '<span class="badge badge-danger"><i class="fas fa-xmark"></i> Échec</span>'}
                    </div>
                </div>
            </div>

            <div style="margin-top:10px;">
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">USER AGENT</div>
                <div class="text-xs text-muted" style="margin-top:4px; word-break:break-all;">${data.user_agent || '—'}</div>
            </div>
        `;

        body.innerHTML = html;
    } catch (e) {
        body.innerHTML = '<div style="text-align:center; padding:40px; color:var(--danger);"><i class="fas fa-triangle-exclamation"></i> Erreur de chargement.</div>';
    }
}

function closeAuditModal() {
    document.getElementById('auditModal').classList.remove('active');
}

document.getElementById('auditModal').addEventListener('click', function(e) {
    if (e.target === this) closeAuditModal();
});
</script>

@endsection