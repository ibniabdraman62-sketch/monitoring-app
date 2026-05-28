<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Rapport hebdomadaire</title></head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background:#F8FAFC; color:#0F172A;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; padding:30px 0;">
<tr><td align="center">

<table width="640" cellpadding="0" cellspacing="0" style="background:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(15,23,42,0.08);">

    <tr><td style="background:#1E40AF; padding:28px 32px; color:#FFFFFF;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">MonitorPro — Rapport hebdomadaire</div>
        <div style="font-size:24px; font-weight:700; padding-top:8px;">Synthèse de supervision</div>
        <div style="font-size:13px; opacity:0.9; padding-top:4px;">
            Du {{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }}
            au {{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }}
        </div>
    </td></tr>

    <tr><td style="padding:32px;">
        <p style="font-size:14.5px; line-height:1.7; margin:0 0 22px;">Bonjour,</p>

        <p style="font-size:14.5px; line-height:1.7; margin:0 0 22px;">
            Veuillez trouver ci-dessous la synthèse de l'activité de surveillance
            pour la semaine écoulée concernant le site
            <strong>{{ $site->client_name }}</strong>.
        </p>

        {{-- KPI Grid --}}
        <table width="100%" cellpadding="0" cellspacing="8" style="margin:24px 0;">
            <tr>
                <td style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:18px; text-align:center; width:50%;">
                    <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:8px;">Disponibilité</div>
                    <div style="font-size:32px; font-weight:700; color:{{ $uptimePct > 99 ? '#15803D' : ($uptimePct > 95 ? '#B45309' : '#B91C1C') }};">
                        {{ number_format($uptimePct, 2) }}%
                    </div>
                </td>
                <td style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:18px; text-align:center; width:50%;">
                    <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:8px;">Temps moyen</div>
                    <div style="font-size:32px; font-weight:700; color:#0F172A;">{{ round($avgResponse) }}<span style="font-size:18px; color:#64748B;"> ms</span></div>
                </td>
            </tr>
            <tr>
                <td style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:18px; text-align:center;">
                    <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:8px;">Incidents</div>
                    <div style="font-size:32px; font-weight:700; color:{{ $incidentsCount === 0 ? '#15803D' : '#B91C1C' }};">{{ $incidentsCount }}</div>
                </td>
                <td style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:18px; text-align:center;">
                    <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:8px;">Vérifications</div>
                    <div style="font-size:32px; font-weight:700; color:#0F172A;">{{ $verifsCount }}</div>
                </td>
            </tr>
        </table>

        {{-- SSL / Domain status --}}
        <div style="margin:24px 0;">
            <div style="font-size:14px; font-weight:700; color:#0F172A; margin-bottom:12px;">Sécurité et infrastructure</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0; border-radius:8px; overflow:hidden;">
                <tr style="background:#F8FAFC;">
                    <td style="padding:12px 16px; font-size:12px; color:#64748B; border-bottom:1px solid #E2E8F0;">Certificat SSL</td>
                    <td style="padding:12px 16px; font-size:13px; border-bottom:1px solid #E2E8F0; text-align:right;">
                        @if(isset($sslDays) && $sslDays > 0)
                            <strong style="color:{{ $sslDays > 30 ? '#15803D' : ($sslDays > 7 ? '#B45309' : '#B91C1C') }};">
                                {{ $sslDays }} jours restants
                            </strong>
                            @if(isset($sslExpiresAt))
                                <span style="color:#64748B;"> — Expire le {{ $sslExpiresAt }}</span>
                            @endif
                        @else
                            <span style="color:#94A3B8;">Données non disponibles</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 16px; font-size:12px; color:#64748B;">Nom de domaine</td>
                    <td style="padding:12px 16px; font-size:13px; text-align:right;">
                        @if(isset($domainDays) && $domainDays > 0)
                            <strong style="color:{{ $domainDays > 60 ? '#15803D' : ($domainDays > 30 ? '#B45309' : '#B91C1C') }};">
                                {{ $domainDays }} jours restants
                            </strong>
                        @else
                            <span style="color:#94A3B8;">Données non disponibles</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Conclusion --}}
        <div style="margin:24px 0; padding:18px 20px; background:#DBEAFE; border-radius:8px;">
            <div style="font-size:13px; font-weight:700; color:#1E3A8A; margin-bottom:6px;">Conclusion de la semaine</div>
            <p style="font-size:13.5px; line-height:1.7; margin:0; color:#1E3A8A;">
                @if($uptimePct >= 99.5 && $incidentsCount === 0)
                    Excellente semaine. Le site a fonctionné sans incident notable.
                @elseif($uptimePct >= 95)
                    Le site a globalement bien fonctionné. Quelques incidents mineurs ont été relevés.
                @else
                    La disponibilité est en deçà des attentes. Une analyse approfondie est recommandée.
                @endif
            </p>
        </div>

        <p style="font-size:13.5px; line-height:1.7; margin:20px 0 0;">
            Le rapport détaillé au format PDF est joint à cet email.
            Vous pouvez également consulter l'historique complet depuis votre tableau de bord MonitorPro.
        </p>

        <p style="font-size:13.5px; line-height:1.7; margin:20px 0 0; color:#64748B;">
            Cordialement,<br>
            <strong>L'équipe MonitorPro — Soft Seven Art</strong>
        </p>
    </td></tr>

    <tr><td style="padding:18px 32px; background:#F8FAFC; border-top:1px solid #E2E8F0;">
        <div style="font-size:11px; color:#94A3B8; line-height:1.6;">
            <strong>MonitorPro</strong> — Système de monitoring intelligent<br>
            Soft Seven Art — Casablanca, Maroc<br>
            Rapport généré automatiquement — confidentiel et destiné à un usage interne
        </div>
    </td></tr>
</table>

</td></tr></table>
</body></html>