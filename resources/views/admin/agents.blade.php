@extends('layouts.monitoring')
@section('title', 'Gestion des Agents')
@section('subtitle', 'Créer et gérer les comptes agents')

@section('content')

@php $agents = App\Models\User::where('role','agent')->get(); @endphp

<!-- Formulaire création agent -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title"><i class="fas fa-user-plus" style="color:#1697C2;"></i> Ajouter un agent</div>
    <form method="POST" action="{{ route('agents.store') }}" style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end;">
        @csrf
        <div class="form-group" style="margin:0;">
            <label class="form-label">Nom complet</label>
            <input type="text" name="name" class="form-input" placeholder="Nom de l'agent" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" placeholder="agent@softseven.ma" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-input" placeholder="Min. 8 caractères" required>
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
            <tr><th>Nom</th><th>Email</th><th>Statut</th><th>Créé le</th><th>Actions</th></tr>
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
            <td>
                <form method="POST" action="{{ route('agents.toggle', $agent) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-primary {{ $agent->is_active ? 'btn-danger' : 'btn-success' }}" style="padding:5px 12px; font-size:11px;">
                        <i class="fas fa-{{ $agent->is_active ? 'ban' : 'check' }}"></i>
                        {{ $agent->is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center; padding:30px; color:#64748B;">Aucun agent créé.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection