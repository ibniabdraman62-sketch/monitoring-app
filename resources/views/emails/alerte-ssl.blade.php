<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Alerte SSL — MonitorPro</title></head>
<body style="margin:0;padding:0;background:#FBF8F0;font-family:Arial,sans-serif;color:#3D2F1F;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#FBF8F0;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0"
       style="background:#FFFFFF;border:1px solid #E8DFC9;border-radius:12px;overflow:hidden;
              box-shadow:0 4px 12px rgba(61,47,31,0.08);">

    {{-- HEADER --}}
    <tr>
        <td style="background:linear-gradient(135deg,#C48A4A 0%,#D4A060 100%);
                   padding:28px 40px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#FFFFFF;margin-bottom:4px;">
                MonitorPro — Alerte Certificat SSL
            </div>
            <div style="font-size:13px;color:rgba(255,255,255,0.85);">
                Action requise · {{ now()->format('d/m/Y à H:i') }}
            </div>
        </td>
    </tr>

    {{-- BODY --}}
    <tr>
        <td style="padding:32px 40px;">
            <p style="font-size:15px;color:#3D2F1F;margin:0 0 14px;">Bonjour,</p>

            <p style="font-size:14px;line-height:1.7;color:#5C4B36;margin:0 0 20px;">
                Le certificat SSL du site <strong>{{ $site->client_name }}</strong>
                arrive à expiration. Une action rapide est nécessaire pour éviter
                toute interruption de service ou alerte de sécurité pour les visiteurs.
            </p>

            {{-- Info box --}}
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#F5E9D6;border:1px solid #E8D0A0;
                          border-radius:10px;margin:0 0 20px;">
                <tr><td style="padding:20px 24px;">
                    <div style="font-size:11px;color:#8B6530;text-transform:uppercase;
                                letter-spacing:1px;font-weight:700;margin-bottom:12px;">
                        Détails du certificat SSL
                    </div>
                    <table width="100%">
                        <tr>
                            <td style="padding:5px 0;color:#8B7855;font-size:13px;width:50%;">Site surveillé</td>
                            <td style="padding:5px 0;font-weight:700;font-size:13px;color:#3D2F1F;">{{ $site->client_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0;color:#8B7855;font-size:13px;">URL</td>
                            <td style="padding:5px 0;font-size:13px;">
                                <a href="{{ $site->url }}" style="color:#5B95C4;text-decoration:none;">{{ $site->url }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0;color:#8B7855;font-size:13px;">Jours restants</td>
                            <td style="padding:5px 0;font-weight:700;font-size:13px;
                                       color:{{ isset($daysRemaining) && $daysRemaining <= 7 ? '#B66258' : '#C48A4A' }};">
                                {{ $daysRemaining ?? 'N/D' }} jours
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0;color:#8B7855;font-size:13px;">Date d'expiration</td>
                            <td style="padding:5px 0;font-weight:700;font-size:13px;color:#B66258;">
                                {{ isset($expiresAt) ? \Carbon\Carbon::parse($expiresAt)->format('d/m/Y') : 'N/D' }}
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>

            <p style="font-size:13px;line-height:1.6;color:#5C4B36;margin:0 0 16px;">
                Veuillez renouveler le certificat SSL auprès de votre registrar ou
                de votre hébergeur dès que possible pour maintenir la sécurité
                et la confiance des utilisateurs de ce site.
            </p>

            <p style="font-size:13px;color:#3D2F1F;margin:18px 0 0;">
                Cordialement,<br>
                <strong>L'équipe Soft Seven Art</strong><br>
                <span style="font-size:12px;color:#8B7855;">Plateforme MonitorPro — Surveillance intelligente</span>
            </p>
        </td>
    </tr>

    {{-- FOOTER --}}
    <tr>
        <td style="background:#FBF8F0;padding:16px 40px;border-top:1px solid #E8DFC9;text-align:center;">
            <p style="font-size:11px;color:#8B7855;margin:0;">
                MonitorPro © {{ date('Y') }} — Soft Seven Art · Casablanca, Maroc
            </p>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>