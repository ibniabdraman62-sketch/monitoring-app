<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Site rétabli</title></head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background:#F8FAFC; color:#0F172A;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; padding:30px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(15,23,42,0.08);">

    <tr><td style="background:#15803D; padding:24px 32px; color:#FFFFFF;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">MonitorPro — Notification de résolution</div>
        <div style="font-size:22px; font-weight:700; padding-top:8px;">Service rétabli</div>
    </td></tr>

    <tr><td style="padding:32px;">
        <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">Bonjour,</p>

        <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">
            Bonne nouvelle. Le site <strong>{{ $site->client_name }}</strong> est de nouveau
            <strong style="color:#15803D;">accessible</strong> et fonctionne normalement.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#DCFCE7; border-left:4px solid #15803D; padding:18px 20px; border-radius:6px; margin:20px 0;">
            <tr><td><table width="100%">
                <tr><td style="padding:6px 0; color:#14532D; font-size:12px; width:40%;">Site rétabli</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->client_name }}</td></tr>
                <tr><td style="padding:6px 0; color:#14532D; font-size:12px;">URL</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->url }}</td></tr>
                <tr><td style="padding:6px 0; color:#14532D; font-size:12px;">Début de l'incident</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $incident->started_at->format('d/m/Y à H:i') }}</td></tr>
                <tr><td style="padding:6px 0; color:#14532D; font-size:12px;">Résolution</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ \Carbon\Carbon::parse($incident->resolved_at)->format('d/m/Y à H:i') }}</td></tr>
                <tr><td style="padding:6px 0; color:#14532D; font-size:12px;">Durée totale</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $incident->duration_min }} minutes</td></tr>
            </table></td></tr>
        </table>

        <p style="font-size:14px; line-height:1.7; margin:24px 0;">
            Nous continuons à surveiller le site pour garantir sa stabilité.
            Une analyse post-incident peut être consultée dans le tableau de bord MonitorPro.
        </p>

        <p style="font-size:13px; line-height:1.7; margin:24px 0 0; color:#64748B;">
            Nous vous remercions pour votre confiance.
        </p>
    </td></tr>

    <tr><td style="padding:18px 32px; background:#F8FAFC; border-top:1px solid #E2E8F0;">
        <div style="font-size:11px; color:#94A3B8; line-height:1.6;">
            <strong>MonitorPro</strong> — Système de monitoring intelligent<br>
            Soft Seven Art — Casablanca, Maroc<br>
            Cet email est généré automatiquement, merci de ne pas y répondre.
        </div>
    </td></tr>
</table>

</td></tr></table>
</body></html>