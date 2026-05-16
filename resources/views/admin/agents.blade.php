@extends('layouts.monitoring')
@section('title', 'Gestion des Agents')
@section('subtitle', 'Créer et gérer les comptes agents')

@section('content')

@php $agents = App\Models\User::where('role','agent')->get(); @endphp

{{-- Messages --}}
@if(session('success'))
    <div style="background:#ECFDF5; border:1px solid #6EE7B7; color:#065F46;
                padding:12px 16px; border-radius:10px; margin-bottom:20px;
                font-size:13px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#DC2626;
                padding:12px 16px; border-radius:10px; margin-bottom:20px;
                font-size:13px;">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
@endif

<!-- Formulaire création agent -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title">
        <i class="fas fa-user-plus" style="color:#1697C2;"></i> Ajouter un agent
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
        <button type="submit" class="btn-primary" style="height:42px;">
            <i class="fas fa-plus"></i> Créer
        </button>
    </form>
</div>

<!-- Liste agents -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            <i class="fas fa-users" style="color:#1697C2;"></i> Liste des agents
        </div>
        <span class="badge badge-blue">{{ $agents->count() }} agents</span>
    </div>
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
            <td style="font-weight:700; color:#0C3547;">{{ $agent->name }}</td>
            <td style="color:#64748B;">{{ $agent->email }}</td>
            <td>
                <span class="badge {{ $agent->is_active ? 'badge-green' : 'badge-red' }}">
                    {{ $agent->is_active ? '● Actif' : '● Inactif' }}
                </span>
            </td>
            <td style="font-size:11px; color:#64748B;">{{ $agent->created_at->format('d/m/Y') }}</td>
            <td style="text-align:center; vertical-align:middle;">

    {{-- Toggle Activer/Désactiver --}}
    <form method="POST" action="{{ route('agents.toggle', $agent) }}" style="display:inline;">
        @csrf @method('PATCH')
        <button type="submit"
                style="width:100px; padding:6px 0; font-size:11px; font-weight:700;
                       border:none; border-radius:8px; cursor:pointer; color:white;
                       text-align:center; line-height:1;
                       background:{{ $agent->is_active ? '#DC2626' : '#059669' }};">
            <i class="fas fa-{{ $agent->is_active ? 'ban' : 'check' }}"></i>
            {{ $agent->is_active ? 'Désactiver' : 'Activer' }}
        </button>
    </form>

    &nbsp;

    {{-- Modifier --}}
    <button onclick="openEditModal({{ $agent->id }}, '{{ addslashes($agent->name) }}', '{{ $agent->email }}')"
            style="width:100px; padding:6px 0; font-size:11px; font-weight:700;
                   border:none; border-radius:8px; cursor:pointer;
                   background:#F59E0B; color:#0C3547;
                   text-align:center; line-height:1;">
        <i class="fas fa-edit"></i> Modifier
    </button>

</td>
</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center; padding:30px; color:#64748B;">
                Aucun agent créé.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- ══════════════════════════════════════════════ -->
<!-- MODAL MODIFICATION AGENT                       -->
<!-- ══════════════════════════════════════════════ -->
<div id="editModal" style="display:none; position:fixed; inset:0; z-index:9999;
     background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:36px; width:440px;
                box-shadow:0 20px 60px rgba(0,0,0,0.3); position:relative;">

        <!-- Header modal -->
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div style="font-size:18px; font-weight:800; color:#0C3547;">
                <i class="fas fa-edit" style="color:#1697C2; margin-right:8px;"></i>
                Modifier l'agent
            </div>
            <button onclick="closeEditModal()"
                    style="background:none; border:none; font-size:20px;
                           color:#94A3B8; cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Formulaire modification -->
        <form id="editForm" method="POST" action="">
            @csrf @method('PATCH')

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user" style="color:#1697C2; margin-right:4px;"></i>
                    Nom complet
                </label>
                <input type="text" id="editName" name="name"
                       class="form-input" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope" style="color:#1697C2; margin-right:4px;"></i>
                    Adresse email
                </label>
                <input type="email" id="editEmail" name="email"
                       class="form-input" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock" style="color:#1697C2; margin-right:4px;"></i>
                    Nouveau mot de passe
                    <span style="color:#94A3B8; font-weight:400;">(laisser vide pour ne pas changer)</span>
                </label>
                <input type="password" name="password"
                       class="form-input" placeholder="Laisser vide = inchangé">
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn-primary" style="flex:1;">
                    <i class="fas fa-save"></i> Sauvegarder
                </button>
                <button type="button" onclick="closeEditModal()"
                        style="flex:1; padding:10px; border:2px solid #E5E7EB;
                               border-radius:10px; background:#fff; color:#64748B;
                               font-size:13px; font-weight:600; cursor:pointer;">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, email) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editForm').action = '/agents/' + id;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Fermer en cliquant en dehors du modal
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

@endsection