<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de disponibilité — {{ $site->client_name }}</title>
</head>
<body style="margin:0; padding:0; background:#FBF8F0; font-family:Arial,sans-serif; color:#3D2F1F;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#FBF8F0; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#FFFFFF; border:1px solid #E8DFC9;
                          border-radius:12px; overflow:hidden;
                          box-shadow:0 4px 12px rgba(61,47,31,0.08);">

                {{-- HEADER --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#2C5F8B 0%,#4078A9 100%);
                               padding:32px 40px; text-align:center;">
                        <div style="font-size:26px; font-weight:700; color:#FFFFFF; margin-bottom:6px;">
                            MonitorPro
                        </div>
                        <div style="font-size:13px; color:rgba(255,255,255,0.85);">
                            Rapport de disponibilité — {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}
                        </div>
                    </td>
                </tr>

                {{-- BODY --}}
                <tr>
                    <td style="padding:36px 40px;">

                        <p style="font-size:15px; color:#3D2F1F; margin:0 0 14px;">
                            Bonjour,
                        </p>

                        <p style="font-size:14px; line-height:1.7; color:#5C4B36; margin:0 0 22px;">
                            Veuillez trouver ci-joint le rapport de disponibilité hebdomadaire
                            du site <strong>{{ $site->client_name }}</strong>, généré automatiquement
                            par la plateforme <strong>MonitorPro</strong> de Soft Seven Art
                            pour la période du
                            <strong>{{ \Carbon\Carbon::parse($data['period_start'])->format('d/m/Y') }}</strong>
                            au
                            <strong>{{ \Carbon\Carbon::parse($data['period_end'])->format('d/m/Y') }}</strong>.
                        </p>

                        {{-- KPIs --}}
                        @php
                            $uptime    = $data['uptime_pct'] ?? 100;
                            $incidents = $data['incidents_count'] ?? 0;
                            $avgMs     = $data['avg_response_ms'] ?? 0;
                            $colorUptime    = $uptime >= 99 ? '#4A8C5A' : ($uptime >= 95 ? '#C48A4A' : '#B66258');
                            $colorIncidents = $incidents === 0 ? '#4A8C5A' : '#B66258';
                        @endphp

                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:#FBF8F0; border:1px solid #E8DFC9;
                                      border-radius:10px; margin:20px 0;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <div style="font-size:11px; color:#8B7855; text-transform:uppercase;
                                                letter-spacing:1px; font-weight:700; margin-bottom:14px;">
                                        Indicateurs clés de la semaine
                                    </div>
                                    <table width="100%">
                                        <tr>
                                            <td style="padding:6px 0; color:#8B7855; font-size:13px; width:50%;">
                                                Disponibilité globale
                                            </td>
                                            <td style="padding:6px 0; font-weight:700; font-size:13px; color:{{ $colorUptime }};">
                                                {{ number_format($uptime, 2) }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; color:#8B7855; font-size:13px;">
                                                Incidents détectés
                                            </td>
                                            <td style="padding:6px 0; font-weight:700; font-size:13px; color:{{ $colorIncidents }};">
                                                {{ $incidents }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; color:#8B7855; font-size:13px;">
                                                Temps de réponse moyen
                                            </td>
                                            <td style="padding:6px 0; font-weight:700; font-size:13px; color:#3D2F1F;">
                                                {{ round($avgMs) }} ms
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; color:#8B7855; font-size:13px;">
                                                URL surveillée
                                            </td>
                                            <td style="padding:6px 0; font-size:13px;">
                                                <a href="{{ $site->url }}" style="color:#5B95C4; text-decoration:none;">
                                                    {{ $site->url }}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:13.5px; line-height:1.7; color:#5C4B36; margin:18px 0;">
                            Le rapport PDF complet est joint à cet email. Il détaille
                            l'ensemble des vérifications effectuées, les incidents enregistrés
                            ainsi que les informations relatives au certificat SSL et au
                            nom de domaine.
                        </p>

                        <p style="font-size:13px; line-height:1.6; color:#8B7855; margin:22px 0 0;">
                            Pour toute question, contactez notre équipe à
                            <a href="mailto:abaloudjoko@gmail.com" style="color:#5B95C4; text-decoration:none;">
                                abaloudjoko@gmail.com
                            </a>.
                        </p>

                        <p style="font-size:13px; color:#3D2F1F; margin:18px 0 0;">
                            Cordialement,<br>
                            <strong>L'équipe Soft Seven Art</strong><br>
                            <span style="font-size:12px; color:#8B7855;">
                                Plateforme MonitorPro — Surveillance intelligente
                            </span>
                        </p>
                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td style="background:#FBF8F0; padding:18px 40px;
                               border-top:1px solid #E8DFC9; text-align:center;">
                        <p style="font-size:11px; color:#8B7855; margin:0; line-height:1.6;">
                            MonitorPro © {{ date('Y') }} — Soft Seven Art<br>
                            Casablanca, Maroc ·
                            <a href="http://monitoring-app.test" style="color:#5B95C4; text-decoration:none;">
                                monitoring-app.test
                            </a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>