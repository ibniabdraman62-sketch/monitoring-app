@extends('layouts.monitoring')
@section('title', 'Gestion des Clients')
@section('subtitle', 'Créer et gérer les comptes clients')

@section('content')

@php
    $clients = \App\Models\User::where('role','client')
        ->withCount('sites')
        ->orderBy('name')
        ->get();
@endphp

{{-- ═══ Stats rapides ═══ --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-label">Total clients</div>
        <div class="kpi-value">{{ $clients->count() }}</div>
        <i class="fas fa-users kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Clients actifs</div>
        <div class="kpi-value">{{ $clients->where('is_active', true)->count() }}</div>
        <i class="fas fa-user-check kpi-icon"></i>
    </div>
    <div class="kpi-card red">
        <div class="kpi-label">Clients désactivés</div>
        <div class="kpi-value">{{ $clients->where('is_active', false)->count() }}</div>
        <i class="fas fa-user-slash kpi-icon"></i>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-label">Sites attribués</div>
        <div class="kpi-value">{{ $clients->sum('sites_count') }}</div>
        <i class="fas fa-globe kpi-icon"></i>
    </div>
</div>

{{-- ═══ Formulaire création client ═══ --}}
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-user-plus" style="color:var(--primary);"></i>
        Créer un nouveau client
    </div>
    <p class="text-sm text-muted" style="margin-bottom:16px;">
        Le client recevra automatiquement un email de bienvenue avec ses identifiants de connexion.
    </p>

    <form method="POST" action="{{ route('clients.store') }}"
          style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end;">
        @csrf
        <div class="form-group" style="margin:0;">
            <label class="form-label">Nom complet </label>
            <input type="text" name="name" class="form-input"
                   placeholder="Ex. Ahmed Bennani" value="{{ old('name') }}" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Email </label>
            <input type="email" name="email" class="form-input"
                   placeholder="ahmed@entreprise.com" value="{{ old('email') }}" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Mot de passe </label>
            <div style="display:flex; gap:6px;">
                <input type="text" name="password" id="newPwd" class="form-input" style="flex:1;"
                       placeholder="Min. 8 caractères" required>
                <button type="button" onclick="genPwd()" class="btn-secondary btn-sm"
                        title="Générer aléatoirement">
                    <i class="fas fa-dice"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn-primary" style="height:40px;">
            <i class="fas fa-paper-plane"></i> Créer & Envoyer
        </button>
    </form>
</div>

{{-- ═══ Liste des clients ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-users" style="color:var(--primary);"></i>
            Liste des clients
        </div>
        <span class="badge badge-info">{{ $clients->count() }} clients</span>
    </div>
    <div class="table-scroll" style="max-height:560px;">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th style="text-align:center;">Sites</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr>
                    <td style="font-weight:600; color:var(--text);">{{ $client->name }}</td>
                    <td class="text-sm">{{ $client->email }}</td>
                    <td style="text-align:center;">
                        <span class="badge {{ $client->sites_count > 0 ? 'badge-info' : 'badge-neutral' }}">
                            {{ $client->sites_count }} site{{ $client->sites_count > 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $client->is_active ? 'badge-success' : 'badge-danger' }} badge-dot">
                            {{ $client->is_active ? 'Actif' : 'Désactivé' }}
                        </span>
                    </td>
                    <td class="text-sm font-mono">{{ $client->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex; gap:6px; flex-wrap:wrap; justify-content:center;">

                            {{-- Modifier --}}
                            <button onclick="openEditModal({{ $client->id }}, '{{ addslashes($client->name) }}', '{{ $client->email }}')"
                                    class="btn-primary btn-warning btn-xs" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Réinitialiser mot de passe --}}
                            <button onclick="openResetPwdModal({{ $client->id }}, '{{ addslashes($client->name) }}', '{{ $client->email }}')"
                                    class="btn-primary btn-gold btn-xs" title="Réinitialiser mot de passe">
                                <i class="fas fa-key"></i>
                            </button>

                            {{-- Activer / Désactiver --}}
                            <form method="POST" action="{{ route('clients.toggle', $client) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="btn-primary {{ $client->is_active ? 'btn-danger' : 'btn-success' }} btn-xs"
                                    title="{{ $client->is_active ? 'Désactiver' : 'Activer' }}">
                                    <i class="fas fa-{{ $client->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>

                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" style="display:inline;"
                                  onsubmit="return confirm('Supprimer définitivement ce client ? Ses sites seront aussi affectés.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-primary btn-danger btn-xs" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucun client créé. Utilisez le formulaire ci-dessus pour ajouter votre premier client.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- MODAL — MODIFIER CLIENT                                 --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="editModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-edit" style="color:var(--primary); margin-right:6px;"></i>
                Modifier le client
            </div>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST" action="">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom complet  </label>
                    <input type="text" id="editName" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse email</label>
                    <input type="email" id="editEmail" name="email" class="form-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Sauvegarder
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- MODAL — RÉINITIALISER MOT DE PASSE                      --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="resetPwdModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-key" style="color:var(--gold); margin-right:6px;"></i>
                Réinitialiser le mot de passe
            </div>
            <button class="modal-close" onclick="closeResetPwdModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="resetPwdForm" method="POST" action="">
            @csrf @method('PATCH')
            <div class="modal-body">
                <p class="text-sm text-muted" style="margin-bottom:14px;">
                    Le client <strong id="resetClientName"></strong> recevra un email avec son nouveau mot de passe.
                </p>
                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe </label>
                    <div style="display:flex; gap:6px;">
                        <input type="text" id="resetPwd" name="password" class="form-input" style="flex:1;"
                               placeholder="Min. 8 caractères" required>
                        <button type="button" onclick="genResetPwd()" class="btn-secondary"
                                title="Générer aléatoirement">
                            <i class="fas fa-dice"></i>
                        </button>
                    </div>
                    <div class="form-help">
                        <i class="fas fa-info-circle"></i>
                        Un email avec ce mot de passe sera automatiquement envoyé au client.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeResetPwdModal()" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary btn-gold">
                    <i class="fas fa-paper-plane"></i> Réinitialiser & Envoyer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Générer mot de passe ───
function makePwd() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    const symbols = '@#$%&';
    let pwd = '';
    for (let i = 0; i < 10; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    pwd += symbols.charAt(Math.floor(Math.random() * symbols.length));
    pwd += Math.floor(Math.random() * 100);
    return pwd;
}
function genPwd() { document.getElementById('newPwd').value = makePwd(); }
function genResetPwd() { document.getElementById('resetPwd').value = makePwd(); }

// ─── Modal Modifier ───
function openEditModal(id, name, email) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editForm').action = '/clients/' + id;
    document.getElementById('editModal').classList.add('active');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// ─── Modal Reset Password ───
function openResetPwdModal(id, name, email) {
    document.getElementById('resetClientName').textContent = name + ' (' + email + ')';
    document.getElementById('resetPwd').value = makePwd();
    document.getElementById('resetPwdForm').action = '/clients/' + id + '/reset-password';
    document.getElementById('resetPwdModal').classList.add('active');
}
function closeResetPwdModal() {
    document.getElementById('resetPwdModal').classList.remove('active');
}
document.getElementById('resetPwdModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetPwdModal();
});

// Au chargement, générer un mot de passe par défaut
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('newPwd')) genPwd();
});
</script>

@endsection