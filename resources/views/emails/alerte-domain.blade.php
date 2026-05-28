<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Alerte domaine</title></head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background:#F8FAFC; color:#0F172A;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; padding:30px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(15,23,42,0.08);">

    <tr><td style="background:#B45309; padding:24px 32px; color:#FFFFFF;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">MonitorPro — Alerte de domaine</div>
        <div style="font-size:22px; font-weight:700; padding-top:8px;">Nom de domaine bientôt expiré</div>
    </td></tr>

    <tr><td style="padding:32px;">
        <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">Bonjour,</p>

        <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">
            Le nom de domaine associé au site <strong>{{ $site->client_name }}</strong>
            expirera dans <strong style="color:#B45309;">{{ $daysRemaining }} jours</strong>.
            Le renouvellement doit être effectué rapidement pour éviter la perte du domaine.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FEF3C7; border-left:4px solid #B45309; padding:18px 20px; border-radius:6px; margin:20px 0;">
            <tr><td><table width="100%">
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px; width:40%;">Site concerné</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->client_name }}</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Domaine</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ parse_url($site->url, PHP_URL_HOST) }}</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Jours avant expiration</td>
                    <td style="padding:6px 0; color:#B45309; font-weight:700; font-size:15px;">{{ $daysRemaining }} jours</td></tr>
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Date d'expiration</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $expiresAt }}</td></tr>
                @if($site->domain_registrar)
                <tr><td style="padding:6px 0; color:#92400E; font-size:12px;">Registrar</td>
                    <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->domain_registrar }}</td></tr>
                @endif
            </table></td></tr>
        </table>

        <div style="margin:24px 0;">
            <div style="font-size:13px; font-weight:700; color:#0F172A; margin-bottom:8px;">Impact d'une expiration</div>
            <p style="font-size:13.5px; line-height:1.7; margin:0; color:#475569;">
                Un domaine expiré devient inaccessible. Les emails associés cessent de fonctionner.
                Après une période de grâce, le domaine peut être récupéré par un tiers,
                entraînant une perte définitive de l'actif numérique.
            </p>
        </div>

        <div style="margin:24px 0; padding:18px 20px; background:#F8FAFC; border-radius:6px;">
            <div style="font-size:13px; font-weight:700; color:#0F172A; margin-bottom:10px;">Actions recommandées</div>
            <ol style="margin:0; padding-left:20px; font-size:13px; line-height:1.8; color:#334155;">
                <li>Se connecter à l'espace client du registrar</li>
                <li>Procéder au renouvellement du domaine sans tarder</li>
                <li>Activer le renouvellement automatique si possible</li>
                <li>Vérifier que les coordonnées de contact sont à jour</li>
            </ol>
        </div>
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