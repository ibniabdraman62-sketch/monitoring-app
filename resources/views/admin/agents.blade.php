@extends('layouts.monitoring')
@section('title', 'Gestion des Agents')
@section('subtitle', 'Créer et gérer les comptes agents')

@section('content')

@php $agents = App\Models\User::where('role','agent')->get(); @endphp

{{-- ═══ Formulaire création ═══ --}}
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-user-plus" style="color:var(--primary);"></i>
        Ajouter un agent
    </div>
    <form method="POST" action="{{ route('agents.store') }}"
          style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end;">
        @csrf
        <div class="form-group" style="margin:0;">
            <label class="form-label">Nom complet</label>
            <input type="text" name="name" class="form-input"
                   placeholder="Nom de l'agent" value="{{ old('name') }}" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input"
                   placeholder="agent@softseven.ma" value="{{ old('email') }}" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-input"
                   placeholder="Min. 8 caractères" required>
        </div>
        <button type="submit" class="btn-primary" style="height:40px;">
            <i class="fas fa-plus"></i> Créer
        </button>
    </form>
</div>

{{-- ═══ Liste agents ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-users" style="color:var(--primary);"></i>
            Liste des agents
        </div>
        <span class="badge badge-info">{{ $agents->count() }} agents</span>
    </div>
    <div class="table-scroll" style="max-height:520px;">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($agents as $agent)
                <tr>
                    <td style="font-weight:600; color:var(--text);">{{ $agent->name }}</td>
                    <td class="text-sm">{{ $agent->email }}</td>
                    <td>
                        <span class="badge {{ $agent->is_active ? 'badge-success' : 'badge-danger' }} badge-dot">
                            {{ $agent->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="text-sm font-mono">{{ $agent->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex; gap:8px;">
                            <form method="POST" action="{{ route('agents.toggle', $agent) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn-primary btn-xs {{ $agent->is_active ? 'btn-danger' : 'btn-success' }}"
                                        style="width:100px;">
                                    <i class="fas fa-{{ $agent->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $agent->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            <button onclick="openEditModal({{ $agent->id }}, '{{ addslashes($agent->name) }}', '{{ $agent->email }}')"
                                    class="btn-primary btn-warning btn-xs" style="width:100px;">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucun agent créé pour l'instant
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL MODIFICATION ═══ --}}
<div id="editModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-edit" style="color:var(--primary); margin-right:6px;"></i>
                Modifier l'agent
            </div>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST" action="">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" id="editName" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse email</label>
                    <input type="email" id="editEmail" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Nouveau mot de passe
                        <span class="text-muted" style="font-weight:400; font-size:11px;">(laisser vide pour conserver)</span>
                    </label>
                    <input type="password" name="password" class="form-input"
                           placeholder="Laisser vide = inchangé">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Sauvegarder</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, email) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editForm').action = '/agents/' + id;
    document.getElementById('editModal').classList.add('active');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

@endsection