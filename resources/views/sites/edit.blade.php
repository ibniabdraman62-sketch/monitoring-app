@extends('layouts.monitoring')
@section('title', 'Modifier le site')
@section('subtitle', $site->client_name)

@section('content')

<a href="{{ route('sites.show', $site) }}" class="btn-secondary btn-sm mb-24">
    <i class="fas fa-arrow-left"></i> Retour aux détails
</a>

{{-- ═══ FORMULAIRE CENTRÉ ═══ --}}
<div style="max-width:720px; margin:0 auto;">
    <div class="card">
        <div class="card-title">
            <i class="fas fa-edit" style="color:var(--primary);"></i>
            Modifier la configuration
        </div>
        <p class="text-sm text-muted" style="margin-bottom:20px;">Mettre à jour les paramètres de surveillance.</p>

        <form method="POST" action="{{ route('sites.update', $site) }}">
            @csrf @method('PATCH')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Nom du client *</label>
                    <input type="text" name="client_name" class="form-input"
                        value="{{ old('client_name', $site->client_name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email du client</label>
                    <input type="email" name="client_email" class="form-input"
                        value="{{ old('client_email', $site->client_email) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">URL du site *</label>
                <input type="url" name="url" class="form-input"
                    value="{{ old('url', $site->url) }}" required>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Fréquence de vérification *</label>
                    <select name="frequency_min" class="form-select" required>
                        @foreach([5,10,15,30] as $f)
                            <option value="{{ $f }}" {{ (int)old('frequency_min', $site->frequency_min)===$f ? 'selected' : '' }}>{{ $f }} minutes</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Seuil de lenteur (ms) *</label>
                    <input type="number" name="response_threshold_ms" class="form-input"
                        value="{{ old('response_threshold_ms', $site->response_threshold_ms) }}"
                        min="500" max="30000" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Emails de notification</label>
                <input type="text" name="notify_emails" class="form-input"
                    value="{{ old('notify_emails', $site->notify_emails) }}"
                    placeholder="email1@exemple.com, email2@exemple.com">
            </div>

            <div style="background:var(--bg-soft); padding:18px; border-radius:var(--radius); margin-bottom:20px;">
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:14px;">
                    Options de surveillance
                </div>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:12px;">
                    <input type="checkbox" name="ssl_check" value="1" {{ old('ssl_check', $site->ssl_check) ? 'checked' : '' }}>
                    <span style="font-size:13px;">Surveillance du certificat SSL</span>
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="whois_check" value="1" {{ old('whois_check', $site->whois_check) ? 'checked' : '' }}>
                    <span style="font-size:13px;">Surveillance WHOIS du domaine</span>
                </label>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('sites.show', $site) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection