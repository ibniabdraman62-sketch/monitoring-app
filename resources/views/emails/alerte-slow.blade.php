<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Alerte — Lenteur</title></head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background:#F8FAFC; color:#0F172A;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; padding:30px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(15,23,42,0.08);">

    <tr><td style="background:#B45309; padding:24px 32px; color:#FFFFFF;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">MonitorPro — Notification de performance</div>
        <div style="font-size:22px; font-weight:700; padding-top:8px;">Lenteur détectée</div>
    </td></tr>

    <tr><td style="padding:32px;">
        <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">Bonjour,</p>

        <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">
            Le site <strong>{{ $site->client_name }}</strong> répond actuellement plus lentement que le seuil défini.
            Bien que le site reste accessible, ses performances peuvent dégrader l'expérience utilisateur.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FEF3C7; border-left:4px solid #B45309; padding:18px 20px; border-radius:6px; margin:20px 0;">
            <tr><td><table width="100%">
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px; width:40%;">Site concerné</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->client_name }}</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">URL</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->url }}</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Temps de réponse mesuré</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $responseTime }} ms</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Seuil défini</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->response_threshold_ms }} ms</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Détecté le</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $incident->started_at->format('d/m/Y à H:i:s') }}</td></tr>
            </table></td></tr>
        </table>

        <div style="margin:24px 0; padding:18px 20px; background:#F8FAFC; border-radius:6px;">
            <div style="font-size:13px; font-weight:700; color:#0F172A; margin-bottom:10px;">Causes possibles</div>
            <ul style="margin:0; padding-left:20px; font-size:13px; line-height:1.8; color:#334155;">
                <li>Charge serveur élevée ou ressources insuffisantes</li>
                <li>Requêtes SQL non optimisées</li>
                <li>Connexion réseau dégradée vers l'origine</li>
                <li>Ressources externes (CDN, API) bloquantes</li>
            </ul>
        </div>

        <p style="font-size:13px; line-height:1.7; margin:24px 0 0; color:#64748B;">
            Nous continuons à surveiller la situation. Si les performances reviennent à la normale, vous recevrez une notification de résolution.
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