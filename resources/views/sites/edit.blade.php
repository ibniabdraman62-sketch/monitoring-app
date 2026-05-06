@extends('layouts.monitoring')
@section('title', 'Modifier le site')
@section('subtitle', $site->client_name)

@section('content')

<div style="max-width:800px; margin:0 auto;">

    <div class="card">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:28px;
                    padding-bottom:20px; border-bottom:2px solid #E0F2FE;">
            <div style="width:44px; height:44px; background:linear-gradient(135deg,#1697C2,#53EAFD);
                        border-radius:12px; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-edit" style="color:#fff; font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:18px; font-weight:800; color:#0C3547;">Modifier les informations</div>
                <div style="font-size:12px; color:#64748B; margin-top:2px;">
                    Modifiez la configuration de surveillance du site <strong>{{ $site->client_name }}</strong>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('sites.update', $site) }}">
            @csrf @method('PUT')

            {{-- Ligne 1 : Nom client + URL --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-user" style="color:#1697C2; margin-right:6px;"></i>
                        Nom du client
                    </label>
                    <input type="text" name="client_name" class="form-input"
                           value="{{ old('client_name', $site->client_name) }}"
                           placeholder="Nom du client" required>
                    @error('client_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-globe" style="color:#1697C2; margin-right:6px;"></i>
                        URL du site
                    </label>
                    <input type="url" name="url" class="form-input"
                           value="{{ old('url', $site->url) }}"
                           placeholder="https://exemple.com" required>
                    @error('url')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Ligne 2 : Fréquence + Seuil --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-clock" style="color:#1697C2; margin-right:6px;"></i>
                        Fréquence de vérification (minutes)
                    </label>
                    <input type="number" name="frequency_min" class="form-input"
                           value="{{ old('frequency_min', $site->frequency_min) }}"
                           min="1" max="60" placeholder="5">
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        Recommandé : 5 minutes
                    </div>
                    @error('frequency_min')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-tachometer-alt" style="color:#1697C2; margin-right:6px;"></i>
                        Seuil d'alerte temps de réponse (ms)
                    </label>
                    <input type="number" name="response_threshold_ms" class="form-input"
                           value="{{ old('response_threshold_ms', $site->response_threshold_ms) }}"
                           min="100" max="30000" placeholder="2000">
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        Alerte si temps > cette valeur. Recommandé : 2000ms
                    </div>
                    @error('response_threshold_ms')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Ligne 3 : Email client + Emails notification --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-user-tie" style="color:#1697C2; margin-right:6px;"></i>
                        Email du client (optionnel)
                    </label>
                    <input type="email" name="client_email" class="form-input"
                           value="{{ old('client_email', $site->client_email ?? '') }}"
                           placeholder="client@exemple.com">
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        Email du responsable client
                    </div>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-envelope" style="color:#1697C2; margin-right:6px;"></i>
                        Emails de notification des alertes
                    </label>
                    <input type="text" name="notify_emails" class="form-input"
                           value="{{ old('notify_emails', $site->notify_emails) }}"
                           placeholder="email1@exemple.com, email2@exemple.com">
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        Séparez plusieurs emails par des virgules
                    </div>
                    @error('notify_emails')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Ligne 4 : Options SSL + WHOIS --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:28px;">
                <div style="background:#F0F9FF; border:1px solid #BAE6FD;
                            border-radius:12px; padding:18px 20px;
                            display:flex; align-items:center; gap:14px;">
                    <input type="checkbox" name="ssl_check" id="ssl_check" value="1"
                           {{ old('ssl_check', $site->ssl_check) ? 'checked' : '' }}
                           style="width:20px; height:20px; accent-color:#1697C2; cursor:pointer; flex-shrink:0;">
                    <label for="ssl_check" style="cursor:pointer;">
                        <div style="font-size:13px; font-weight:700; color:#0C3547; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-lock" style="color:#10B981;"></i>
                            Vérifier le certificat SSL
                        </div>
                        <div style="font-size:11px; color:#64748B; margin-top:3px;">
                            Surveille la validité et l'expiration du HTTPS
                        </div>
                    </label>
                </div>

                <div style="background:#F0F9FF; border:1px solid #BAE6FD;
                            border-radius:12px; padding:18px 20px;
                            display:flex; align-items:center; gap:14px;">
                    <input type="checkbox" name="whois_check" id="whois_check" value="1"
                           {{ old('whois_check', $site->whois_check ?? true) ? 'checked' : '' }}
                           style="width:20px; height:20px; accent-color:#1697C2; cursor:pointer; flex-shrink:0;">
                    <label for="whois_check" style="cursor:pointer;">
                        <div style="font-size:13px; font-weight:700; color:#0C3547; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-globe" style="color:#7C3AED;"></i>
                            Vérification WHOIS domaine
                        </div>
                        <div style="font-size:11px; color:#64748B; margin-top:3px;">
                            Surveille l'expiration du nom de domaine
                        </div>
                    </label>
                </div>
            </div>

            {{-- Boutons --}}
            <div style="display:flex; gap:12px; justify-content:flex-end;
                        padding-top:20px; border-top:1px solid #E0F2FE;">
                <a href="{{ route('sites.index') }}"
                   class="btn-primary btn-warning"
                   style="text-decoration:none;">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>

        </form>
    </div>

    {{-- Infos WHOIS actuelles --}}
    @if($site->domain_registrar || $site->domain_expires_at)
    <div class="card" style="margin-top:20px;">
        <div class="card-title">
            <i class="fas fa-globe" style="color:#7C3AED;"></i>
            Informations WHOIS actuelles
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
            <div style="text-align:center; padding:14px; background:#F5F3FF; border-radius:10px;">
                <div style="font-size:14px; font-weight:800; color:#0C3547;">
                    {{ $site->domain_registrar ?? '—' }}
                </div>
                <div style="font-size:11px; color:#64748B; margin-top:4px; font-weight:600;">REGISTRAR</div>
            </div>
            <div style="text-align:center; padding:14px; background:#F5F3FF; border-radius:10px;">
                @php
                    $daysLeft = $site->domain_expires_at
                        ? now()->diffInDays(\Carbon\Carbon::parse($site->domain_expires_at), false)
                        : null;
                @endphp
                <div style="font-size:14px; font-weight:800;
                    color:{{ $daysLeft !== null ? ($daysLeft <= 30 ? '#DC2626' : '#059669') : '#94A3B8' }}">
                    {{ $site->domain_expires_at
                        ? \Carbon\Carbon::parse($site->domain_expires_at)->format('d/m/Y')
                        : '—' }}
                </div>
                <div style="font-size:11px; color:#64748B; margin-top:4px; font-weight:600;">EXPIRATION</div>
            </div>
            <div style="text-align:center; padding:14px; background:#F5F3FF; border-radius:10px;">
                @if($daysLeft !== null)
                    <span class="badge {{ $daysLeft <= 7 ? 'badge-red' : ($daysLeft <= 30 ? 'badge-yellow' : 'badge-green') }}"
                          style="font-size:12px; padding:4px 12px;">
                        {{ $daysLeft > 0 ? $daysLeft.'j restants' : 'EXPIRÉ' }}
                    </span>
                @else
                    <span style="color:#94A3B8; font-size:14px;">—</span>
                @endif
                <div style="font-size:11px; color:#64748B; margin-top:8px; font-weight:600;">STATUT</div>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection



