<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur MonitorPro</title>
</head>
<body style="margin:0; padding:0; background:#FBF8F0; font-family:Arial,sans-serif; color:#3D2F1F;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#FBF8F0; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#FFFFFF; border:1px solid #E8DFC9; border-radius:12px; overflow:hidden;
                          box-shadow:0 4px 12px rgba(61,47,31,0.08);">

                {{-- ═══ HEADER ═══ --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#2C5F8B 0%,#4078A9 100%); padding:32px 40px; text-align:center;">
                        <div style="background:rgba(255,255,255,0.15); width:60px; height:60px; border-radius:14px;
                                    margin:0 auto 14px; display:inline-block; line-height:60px; font-size:24px;
                                    color:#FFFFFF; font-weight:700;">
                            S7A
                        </div>
                        <h1 style="color:#FFFFFF; font-size:24px; margin:0; font-weight:700;">
                            Bienvenue sur MonitorPro
                        </h1>
                        <p style="color:rgba(255,255,255,0.85); font-size:13px; margin:8px 0 0;">
                            Soft Seven Art — Surveillance intelligente de vos sites web
                        </p>
                    </td>
                </tr>

                {{-- ═══ BODY ═══ --}}
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="font-size:16px; color:#3D2F1F; margin:0 0 14px;">
                            Bonjour <strong>{{ $client->name }}</strong>,
                        </p>

                        <p style="font-size:14px; line-height:1.7; color:#5C4B36; margin:0 0 22px;">
                            Votre compte client sur la plateforme <strong>MonitorPro</strong> a été créé avec succès
                            par l'équipe Soft Seven Art. Vous pouvez dès maintenant accéder à votre espace personnel
                            pour consulter l'état de vos sites surveillés.
                        </p>

                        {{-- ═══ Identifiants ═══ --}}
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:#FBF8F0; border:1px solid #E8DFC9; border-radius:10px; margin:22px 0;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <div style="font-size:11px; color:#8B7855; text-transform:uppercase;
                                                letter-spacing:1px; font-weight:700; margin-bottom:14px;">
                                        Vos identifiants de connexion
                                    </div>

                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:6px 0; color:#8B7855; font-size:13px; width:130px;">
                                                Email :
                                            </td>
                                            <td style="padding:6px 0; color:#3D2F1F; font-size:13px; font-weight:700;">
                                                {{ $client->email }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; color:#8B7855; font-size:13px;">
                                                Mot de passe :
                                            </td>
                                            <td style="padding:6px 0;">
                                                <code style="background:#FFFFFF; border:1px solid #D9CDB0;
                                                             padding:4px 10px; border-radius:5px;
                                                             font-family:monospace; font-size:13px; color:#3D2F1F;
                                                             font-weight:700;">
                                                    {{ $plainPassword }}
                                                </code>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ═══ Bouton CTA ═══ --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $loginUrl }}"
                                       style="display:inline-block; background:#5B95C4; color:#FFFFFF;
                                              padding:13px 36px; border-radius:8px; text-decoration:none;
                                              font-size:14px; font-weight:700;">
                                        Accéder à mon espace MonitorPro
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- ═══ Conseils sécurité ═══ --}}
                        <div style="background:#F5E9D6; border-left:4px solid #C48A4A;
                                    padding:14px 18px; border-radius:6px; margin:22px 0;">
                            <div style="font-size:13px; font-weight:700; color:#9C6A2F; margin-bottom:6px;">
                                Recommandations de sécurité
                            </div>
                            <ul style="margin:6px 0 0; padding-left:20px; font-size:12.5px;
                                       color:#5C4B36; line-height:1.6;">
                                <li>Changez ce mot de passe dès votre première connexion</li>
                                <li>Ne partagez jamais vos identifiants avec un tiers</li>
                                <li>Utilisez un mot de passe unique pour MonitorPro</li>
                            </ul>
                        </div>

                        <p style="font-size:13px; line-height:1.6; color:#8B7855; margin:22px 0 0;">
                            Pour toute question ou assistance, vous pouvez répondre directement à cet email
                            ou contacter notre équipe à
                            <a href="mailto:contact@softseven.ma" style="color:#5B95C4; text-decoration:none;">
                                contact@softseven.ma
                            </a>.
                        </p>

                        <p style="font-size:13px; color:#3D2F1F; margin:18px 0 0;">
                            Cordialement,<br>
                            <strong>L'équipe Soft Seven Art</strong>
                        </p>
                    </td>
                </tr>

                {{-- ═══ FOOTER ═══ --}}
                <tr>
                    <td style="background:#FBF8F0; padding:18px 40px; border-top:1px solid #E8DFC9; text-align:center;">
                        <p style="font-size:11px; color:#8B7855; margin:0; line-height:1.6;">
                            MonitorPro © {{ date('Y') }} — Soft Seven Art<br>
                            Casablanca, Maroc · <a href="{{ config('app.url') }}" style="color:#5B95C4; text-decoration:none;">{{ config('app.url') }}</a>
                        </p>
                    </td>
                </tr>
            </table>

            <p style="font-size:10.5px; color:#B5A684; margin:14px 0 0; text-align:center;">
                Cet email contient des identifiants confidentiels. Ne le transférez pas.
            </p>
        </td>
    </tr>
</table>

</body>
</html>