@extends('layouts.monitoring')

@section('title', 'Ajouter un site')
@section('subtitle', 'Nouveau site à surveiller')

@section('content')

<div style="max-width:600px;">
    <div class="card">
        <div class="card-title">🌐 Informations du site</div>

        <form action="{{ route('sites.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Nom du client</label>
                <input type="text" name="client_name" value="{{ old('client_name') }}"
                       class="form-input" placeholder="Ex: Société ABC">
                @error('client_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">URL du site</label>
                <input type="url" name="url" value="{{ old('url') }}"
                       class="form-input" placeholder="https://example.com">
                @error('url')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Fréquence (minutes)</label>
                    <input type="number" name="frequency_min"
                           value="{{ old('frequency_min', 5) }}"
                           min="1" max="60" class="form-input">
                    @error('frequency_min')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Seuil réponse (ms)</label>
                    <input type="number" name="response_threshold_ms"
                           value="{{ old('response_threshold_ms', 2000) }}"
                           min="100" class="form-input">
                    @error('response_threshold_ms')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="ssl_check" checked
                           style="width:16px; height:16px; accent-color:#4F46E5;">
                    <span class="form-label" style="margin:0;">🔒 Vérifier le certificat SSL</span>
                </label>
            </div>
            <div style="display:flex; gap:10px; margin-top:8px;">
                <div class="form-group">
    <label class="form-label">
        <i class="fas fa-envelope" style="color:#1697C2;"></i>
        Emails de notification
    </label>
    <input type="text" name="notify_emails" class="form-input"
           placeholder="email1@exemple.com, email2@exemple.com"
           value="{{ old('notify_emails') }}">
    <div style="font-size:11px; color:#64748B; margin-top:4px;">
        Séparez plusieurs emails par des virgules
    </div>
    @error('notify_emails')
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i> Ajouter le site
                </button>
                <a href="{{ route('sites.index') }}" class="btn-primary btn-warning">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
<div class="form-group">
    <label class="form-label">Email du client (optionnel)</label>
    <input type="email" name="client_email" class="form-input"
           placeholder="client@exemple.com"
           value="{{ old('client_email', $site->client_email ?? '') }}">
</div>

<div class="form-group" style="display:flex; align-items:center; gap:12px;">
    <input type="checkbox" name="whois_check" id="whois_check" value="1"
           {{ old('whois_check', ($site->whois_check ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}
           style="width:18px; height:18px;">
    <label for="whois_check" class="form-label" style="margin:0;">
        Activer la vérification WHOIS (expiration domaine)
    </label>
</div>
@endsection