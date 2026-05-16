@extends('layouts.monitoring')

@section('title', 'Mon Profil')
@section('subtitle', 'Gérer vos informations personnelles')

@section('content')

<div style="max-width:680px; margin:0 auto;">

    <!-- Carte Avatar -->
    <div class="card" style="margin-bottom:20px; text-align:center; padding:32px;">
        <div style="width:80px; height:80px; background:linear-gradient(135deg,#1697C2,#53EAFD);
                    border-radius:50%; display:flex; align-items:center; justify-content:center;
                    font-size:32px; font-weight:900; color:#0C3547; margin:0 auto 16px;">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <div style="font-size:22px; font-weight:800; color:#0C3547;">{{ auth()->user()->name }}</div>
        <div style="font-size:14px; color:#64748B; margin-top:4px;">{{ auth()->user()->email }}</div>
        <div style="margin-top:12px;">
            <span class="badge badge-blue">
                <i class="fas fa-shield-alt"></i>
                {{ auth()->user()->role === 'super_admin' ? 'Super Administrateur' : 'Agent' }}
            </span>
        </div>
    </div>

    <!-- Informations de profil -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-title">
            <i class="fas fa-user" style="color:#1697C2;"></i> Informations de profil
        </div>
        <p style="font-size:13px; color:#64748B; margin-bottom:20px;">
            Mettez à jour votre nom et votre adresse e-mail.
        </p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('patch')

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user" style="color:#1697C2; margin-right:5px;"></i> Nom complet
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $user->name) }}"
                       class="form-input" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope" style="color:#1697C2; margin-right:5px;"></i> Adresse e-mail
                </label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="form-input" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Sauvegarder
                </button>
                @if(session('status') === 'profile-updated')
                    <span style="color:#059669; font-size:13px; font-weight:600;">
                        <i class="fas fa-check"></i> Sauvegardé !
                    </span>
                @endif
            </div>
        </form>
    </div>

    <!-- Mot de passe -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-lock" style="color:#1697C2;"></i> Mettre à jour le mot de passe
        </div>
        <p style="font-size:13px; color:#64748B; margin-bottom:20px;">
            Utilisez un mot de passe long et aléatoire pour sécuriser votre compte.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf @method('put')

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-key" style="color:#1697C2; margin-right:5px;"></i> Mot de passe actuel
                </label>
                <input type="password" name="current_password"
                       class="form-input" autocomplete="current-password">
                @error('current_password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock" style="color:#1697C2; margin-right:5px;"></i> Nouveau mot de passe
                </label>
                <input type="password" name="password"
                       class="form-input" autocomplete="new-password">
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock" style="color:#1697C2; margin-right:5px;"></i> Confirmer le mot de passe
                </label>
                <input type="password" name="password_confirmation"
                       class="form-input" autocomplete="new-password">
                @error('password_confirmation')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-shield-alt"></i> Mettre à jour
                </button>
                @if(session('status') === 'password-updated')
                    <span style="color:#059669; font-size:13px; font-weight:600;">
                        <i class="fas fa-check"></i> Mis à jour !
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- Zone dangereuse SUPPRIMÉE sur demande de M. MOUMKINE --}}
    {{-- L'administrateur ne peut pas supprimer son propre compte --}}

</div>

@endsection