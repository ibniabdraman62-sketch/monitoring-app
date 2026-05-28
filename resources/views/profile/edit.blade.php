@extends('layouts.monitoring')
@section('title', 'Mon Profil')
@section('subtitle', 'Gérer vos informations personnelles')

@section('content')

<div style="max-width:680px; margin:0 auto;">

    {{-- ═══ Avatar Card ═══ --}}
    <div class="card mb-16" style="text-align:center; padding:28px;">
        <div style="width:80px; height:80px;
                    background:linear-gradient(135deg, var(--primary), var(--primary-dark));
                    color:#FFFFFF; border-radius:50%; display:flex; align-items:center;
                    justify-content:center; font-size:30px; font-weight:700; margin:0 auto 14px;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div style="font-size:20px; font-weight:700; color:var(--text);">{{ auth()->user()->name }}</div>
        <div class="text-sm text-muted" style="margin-top:3px;">{{ auth()->user()->email }}</div>
        <div style="margin-top:12px;">
            <span class="badge badge-info">
                <i class="fas fa-shield-alt"></i>
                {{ auth()->user()->isSuperAdmin() ? 'Super Administrateur' : (auth()->user()->role === 'agent' ? 'Agent' : 'Client') }}
            </span>
        </div>
    </div>

    {{-- ═══ Informations profil ═══ --}}
    <div class="card mb-16">
        <div class="card-title">
            <i class="fas fa-user" style="color:var(--primary);"></i>
            Informations de profil
        </div>
        <p class="text-sm text-muted" style="margin-bottom:16px;">Mettre à jour votre nom et adresse email.</p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PATCH')

            <div class="form-group">
                <label class="form-label">Nom complet</label>
                <input type="text" name="name" class="form-input"
                    value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" name="email" class="form-input"
                    value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                @if(session('status') === 'profile-updated')
                    <span class="badge badge-success">
                        <i class="fas fa-check"></i> Mis à jour
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- ═══ Sécurité ═══ --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-lock" style="color:var(--primary);"></i>
            Sécurité du compte
        </div>
        <p class="text-sm text-muted" style="margin-bottom:16px;">Mettre à jour votre mot de passe.</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Mot de passe actuel</label>
                <input type="password" name="current_password" class="form-input" autocomplete="current-password">
                @error('current_password', 'updatePassword') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" class="form-input" autocomplete="new-password">
                @error('password', 'updatePassword') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-shield-alt"></i> Mettre à jour le mot de passe
                </button>
                @if(session('status') === 'password-updated')
                    <span class="badge badge-success">
                        <i class="fas fa-check"></i> Mis à jour
                    </span>
                @endif
            </div>
        </form>
    </div>

</div>

@endsection