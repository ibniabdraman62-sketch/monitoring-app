<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Alerte — Site hors ligne</title>
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background:#F8FAFC; color:#0F172A;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; padding:30px 0;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(15,23,42,0.08);">

    {{-- ── HEADER ── --}}
    <tr>
        <td style="background:#B91C1C; padding:24px 32px; color:#FFFFFF;">
            <table width="100%">
                <tr>
                    <td style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">MonitorPro — Notification d'incident</td>
                </tr>
                <tr>
                    <td style="font-size:22px; font-weight:700; padding-top:8px;">Site web hors ligne</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- ── CONTENT ── --}}
    <tr>
        <td style="padding:32px;">

            <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">
                Bonjour,
            </p>

            <p style="font-size:14.5px; line-height:1.7; margin:0 0 18px;">
                Notre système de surveillance a détecté que le site
                <strong>{{ $site->client_name }}</strong> ({{ $site->url }})
                est actuellement <strong style="color:#B91C1C;">inaccessible</strong>.
            </p>

            {{-- ── Detail box ── --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#FEE2E2; border-left:4px solid #B91C1C; padding:18px 20px; border-radius:6px; margin:20px 0;">
                <tr><td>
                    <table width="100%">
                        <tr>
                            <td style="padding:6px 0; color:#7F1D1D; font-size:12px; width:40%;">Site concerné</td>
                            <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $site->client_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0; color:#7F1D1D; font-size:12px;">URL</td>
                            <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px; word-break:break-all;">{{ $site->url }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0; color:#7F1D1D; font-size:12px;">Date détection</td>
                            <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $incident->started_at->format('d/m/Y à H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0; color:#7F1D1D; font-size:12px;">Code HTTP retourné</td>
                            <td style="padding:6px 0; color:#0F172A; font-weight:600; font-size:13px;">{{ $httpCode ?? 'Aucune réponse' }}</td>
                        </tr>
                    </table>
                </td></tr>
            </table>

            {{-- ── Impact ── --}}
            <div style="margin:24px 0;">
                <div style="font-size:13px; font-weight:700; color:#0F172A; margin-bottom:8px;">Impact potentiel</div>
                <p style="font-size:13.5px; line-height:1.7; margin:0; color:#475569;">
                    Les visiteurs ne peuvent actuellement pas accéder au site. Cette indisponibilité peut affecter
                    l'expérience utilisateur, le référencement naturel et la confiance des clients.
                </p>
            </div>

            {{-- ── Recommended actions ── --}}
            <div style="margin:24px 0; padding:18px 20px; background:#F8FAFC; border-radius:6px;">
                <div style="font-size:13px; font-weight:700; color:#0F172A; margin-bottom:10px;">Actions recommandées</div>
                <ol style="margin:0; padding-left:20px; font-size:13px; line-height:1.8; color:#334155;">
                    <li>Vérifier la disponibilité du serveur d'hébergement</li>
                    <li>Contrôler la configuration DNS du domaine</li>
                    <li>Examiner les journaux d'erreur du serveur</li>
                    <li>Contacter l'équipe technique en cas de doute</li>
                </ol>
            </div>

            <p style="font-size:13px; line-height:1.7; margin:24px 0 0; color:#64748B;">
                Vous recevrez automatiquement une nouvelle notification dès que le site sera de nouveau accessible.
            </p>

        </td>
    </tr>

    {{-- ── FOOTER ── --}}
    <tr>
        <td style="padding:18px 32px; background:#F8FAFC; border-top:1px solid #E2E8F0;">
            <table width="100%">
                <tr>
                    <td style="font-size:11px; color:#94A3B8; line-height:1.6;">
                        <strong>MonitorPro</strong> — Système de monitoring intelligent<br>
                        Soft Seven Art — Casablanca, Maroc<br>
                        Cet email est généré automatiquement, merci de ne pas y répondre.
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>