@extends('layouts.monitoring')

@section('title', 'Modifier le site')
@section('subtitle', $site->client_name)

@section('content')

<div style="max-width:600px;">
    <div class="card">
        <div class="card-title">✏️ Modifier les informations</div>

        <form action="{{ route('sites.update', $site) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Nom du client</label>
                <input type="text" name="client_name"
                       value="{{ old('client_name', $site->client_name) }}"
                       class="form-input">
                @error('client_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">URL du site</label>
                <input type="url" name="url"
                       value="{{ old('url', $site->url) }}"
                       class="form-input">
                @error('url')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Fréquence (minutes)</label>
                    <input type="number" name="frequency_min"
                           value="{{ old('frequency_min', $site->frequency_min) }}"
                           min="1" max="60" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Seuil réponse (ms)</label>
                    <input type="number" name="response_threshold_ms"
                           value="{{ old('response_threshold_ms', $site->response_threshold_ms) }}"
                           min="100" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="ssl_check"
                           {{ $site->ssl_check ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#4F46E5;">
                    <span class="form-label" style="margin:0;">🔒 Vérifier le certificat SSL</span>
                </label>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn-primary btn-success">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="{{ route('sites.index') }}" class="btn-primary btn-warning">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection