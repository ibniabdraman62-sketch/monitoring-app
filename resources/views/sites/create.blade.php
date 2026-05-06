@extends('layouts.monitoring')
@section('title', 'Ajouter un site')
@section('subtitle', 'Nouveau site à surveiller')

@section('content')

<div style="max-width:800px; margin:0 auto;">

    <div class="card">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:28px;
                    padding-bottom:20px; border-bottom:2px solid #E0F2FE;">
            <div style="width:44px; height:44px; background:linear-gradient(135deg,#1697C2,#53EAFD);
                        border-radius:12px; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-plus" style="color:#fff; font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:18px; font-weight:800; color:#0C3547;">Ajouter un nouveau site</div>
                <div style="font-size:12px; color:#64748B; margin-top:2px;">
                    Configurez la surveillance d'un nouveau site web
                </div>
            </div>
        </div>

        <form action="{{ route('sites.store') }}" method="POST">
            @csrf

            {{-- Ligne 1 : Nom client + URL --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-user" style="color:#1697C2; margin-right:6px;"></i>
                        Nom du client
                    </label>
                    <input type="text" name="client_name"
                           value="{{ old('client_name') }}"
                           class="form-input"
                           placeholder="Ex: Société ABC" required>
                    @error('client_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">
                        <i class="fas fa-globe" style="color:#1697C2; margin-right:6px;"></i>
                        URL du site
                    </label>
                    <input type="url" name="url"
                           value="{{ old('url') }}"
                           class="form-input"
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
                    <input type="number" name="frequency_min"
                           value="{{ old('frequency_min', 5) }}"
                           min="1" max="60"
                           class="form-input" placeholder="5">
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
                    <input type="number" name="response_threshold_ms"
                           value="{{ old('response_threshold_ms', 2000) }}"
                           min="100" max="30000"
                           class="form-input" placeholder="2000">
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
                    <input type="email" name="client_email"
                           value="{{ old('client_email') }}"
                           class="form-input"
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
                    <input type="text" name="notify_emails"
                           value="{{ old('notify_emails') }}"
                           class="form-input"
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
                           {{ old('ssl_check', true) ? 'checked' : '' }}
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
                           {{ old('whois_check', true) ? 'checked' : '' }}
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
                    <i class="fas fa-plus"></i> Ajouter le site
                </button>
            </div>

        </form>
    </div>

</div>

@endsection