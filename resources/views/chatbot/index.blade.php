@extends('layouts.monitoring')
@section('title', 'Assistant IA')
@section('subtitle', 'Intelligence Artificielle — Monitoring Expert')

@section('content')

<div style="display:grid; grid-template-columns:300px 1fr; gap:24px; height:calc(100vh - 160px);">

    <!-- Panneau gauche -->
    <div style="display:flex; flex-direction:column; gap:16px;">

        <!-- Info Assistant -->
        <div style="background:linear-gradient(135deg,#0C3547,#1697C2);
                    border-radius:16px; padding:24px; text-align:center;">
            <div style="width:72px; height:72px; background:rgba(255,255,255,0.15);
                        border-radius:50%; margin:0 auto 16px; display:flex;
                        align-items:center; justify-content:center;">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                    <circle cx="20" cy="17" r="11" fill="white" opacity="0.95"/>
                    <rect x="9" y="25" width="8" height="8" rx="2" fill="white" opacity="0.95"/>
                    <circle cx="15" cy="17" r="2.5" fill="#1697C2"/>
                    <circle cx="25" cy="17" r="2.5" fill="#1697C2"/>
                    <path d="M15 22 Q20 26 25 22" stroke="#1697C2" stroke-width="2" stroke-linecap="round" fill="none"/>
                    <circle cx="11" cy="14" r="1.5" fill="#53EAFD"/>
                    <circle cx="29" cy="14" r="1.5" fill="#53EAFD"/>
                </svg>
            </div>
            <div style="font-size:16px; font-weight:800; color:#fff; margin-bottom:4px;">
                MonitorPro Assistant
            </div>
            <div style="font-size:11px; color:rgba(255,255,255,0.7); margin-bottom:12px;">
                Powered by Google Gemini 2.5 Flash
            </div>
            <div style="display:flex; gap:8px; justify-content:center;">
                <span style="background:rgba(16,185,129,0.25); color:#6EE7B7;
                             border:1px solid rgba(16,185,129,0.4);
                             padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700;">
                    ● EN LIGNE
                </span>
                <span style="background:rgba(255,255,255,0.15); color:#fff;
                             padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700;">
                    24h/7j
                </span>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card" style="padding:16px;">
            <div style="font-size:12px; font-weight:700; color:#0C3547; margin-bottom:12px; text-transform:uppercase; letter-spacing:1px;">
                Capacités
            </div>
            @foreach([
                ['fas fa-chart-line', '#10B981', 'Analyse performances'],
                ['fas fa-exclamation-triangle', '#EF4444', 'Détection incidents'],
                ['fas fa-brain', '#7C3AED', 'Prédiction pannes'],
                ['fas fa-balance-scale', '#1697C2', 'Comparaison sites'],
                ['fas fa-file-alt', '#D97706', 'Rapports intelligents'],
                ['fas fa-lock', '#059669', 'Audit SSL & WHOIS'],
            ] as $cap)
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0;
                        border-bottom:1px solid #F0F9FF;">
                <div style="width:28px; height:28px; border-radius:8px;
                            background:{{ $cap[1] }}20; display:flex; align-items:center;
                            justify-content:center;">
                    <i class="fas {{ $cap[0] }}" style="font-size:12px; color:{{ $cap[1] }};"></i>
                </div>
                <span style="font-size:12px; color:#334155; font-weight:500;">{{ $cap[2] }}</span>
            </div>
            @endforeach
        </div>

        <!-- Questions suggérées -->
        <div class="card" style="padding:16px; flex:1; overflow-y:auto;">
            <div style="font-size:12px; font-weight:700; color:#0C3547; margin-bottom:12px; text-transform:uppercase; letter-spacing:1px;">
                Questions suggérées
            </div>
            @foreach([
                ['fas fa-trophy', '#D97706', 'Quel site est le plus rapide ?'],
                ['fas fa-exclamation-circle', '#EF4444', 'Y a-t-il des sites en danger ?'],
                ['fas fa-chart-bar', '#1697C2', 'Compare Amazon et Google'],
                ['fas fa-file-alt', '#7C3AED', 'Rapport complet du système'],
                ['fas fa-crystal-ball', '#10B981', 'Prédit les pannes des 24h'],
                ['fas fa-lock', '#059669', 'SSL expirant bientôt ?'],
                ['fas fa-sort-amount-down', '#D97706', 'Classement par performance'],
                ['fas fa-star', '#1697C2', 'Score global du système'],
            ] as $q)
            <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
               target="_blank"
               style="display:flex; align-items:center; gap:10px; padding:10px 12px;
                      margin-bottom:6px; background:#F8FAFF; border:1px solid #E0F2FE;
                      border-radius:10px; text-decoration:none; transition:all 0.2s;"
               onmouseover="this.style.background='#E0F2FE'; this.style.borderColor='#1697C2'; this.style.transform='translateX(4px)'"
               onmouseout="this.style.background='#F8FAFF'; this.style.borderColor='#E0F2FE'; this.style.transform='translateX(0)'">
                <div style="width:28px; height:28px; border-radius:8px;
                            background:{{ $q[1] }}20; display:flex; align-items:center;
                            justify-content:center; flex-shrink:0;">
                    <i class="{{ $q[0] }}" style="font-size:11px; color:{{ $q[1] }};"></i>
                </div>
                <span style="font-size:12px; color:#334155; font-weight:500;">{{ $q[2] }}</span>
                <i class="fas fa-arrow-right" style="font-size:10px; color:#94A3B8; margin-left:auto;"></i>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Panneau droit — Chat -->
    <div style="background:#fff; border:1px solid #E0F2FE; border-radius:16px;
                overflow:hidden; display:flex; flex-direction:column;
                box-shadow:0 4px 24px rgba(22,151,194,0.1);">

        <!-- Header -->
        <div style="background:linear-gradient(135deg,#0C3547,#1697C2);
                    padding:20px 28px; display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; background:rgba(255,255,255,0.15);
                        border-radius:50%; display:flex; align-items:center;
                        justify-content:center; flex-shrink:0;">
                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                    <circle cx="20" cy="17" r="11" fill="white" opacity="0.95"/>
                    <rect x="9" y="25" width="8" height="8" rx="2" fill="white" opacity="0.95"/>
                    <circle cx="15" cy="17" r="2.5" fill="#1697C2"/>
                    <circle cx="25" cy="17" r="2.5" fill="#1697C2"/>
                    <path d="M15 22 Q20 26 25 22" stroke="#1697C2" stroke-width="2" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:17px; font-weight:800; color:#fff;">
                    MonitorPro Assistant IA
                </div>
                <div style="font-size:12px; color:rgba(255,255,255,0.7); margin-top:2px;">
                    Analyse intelligente • Données temps réel • Google Gemini 2.5
                </div>
            </div>
            <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
               target="_blank"
               style="background:rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.3);
                      padding:8px 18px; border-radius:20px; font-size:12px; font-weight:700;
                      text-decoration:none; transition:all 0.2s; display:flex; align-items:center; gap:6px;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-external-link-alt"></i>
                Ouvrir le chat
            </a>
        </div>

        <!-- Corps -->
        <div style="flex:1; display:flex; flex-direction:column; align-items:center;
                    justify-content:center; padding:40px; text-align:center; background:#F8FAFF;">

            <!-- Animation cercles -->
            <div style="position:relative; width:120px; height:120px; margin-bottom:28px;">
                <div style="position:absolute; inset:0; border-radius:50%;
                            background:linear-gradient(135deg,#E0F2FE,#BAE6FD);
                            animation:pulse-ring 2s infinite;"></div>
                <div style="position:absolute; inset:12px; border-radius:50%;
                            background:linear-gradient(135deg,#1697C2,#53EAFD);
                            display:flex; align-items:center; justify-content:center;
                            box-shadow:0 8px 24px rgba(22,151,194,0.4);">
                    <svg width="48" height="48" viewBox="0 0 40 40" fill="none">
                        <circle cx="20" cy="17" r="11" fill="white" opacity="0.95"/>
                        <rect x="9" y="25" width="8" height="8" rx="2" fill="white" opacity="0.95"/>
                        <circle cx="15" cy="17" r="2.5" fill="#1697C2"/>
                        <circle cx="25" cy="17" r="2.5" fill="#1697C2"/>
                        <path d="M15 22 Q20 26 25 22" stroke="#1697C2" stroke-width="2" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
            </div>

            <div style="font-size:22px; font-weight:800; color:#0C3547; margin-bottom:10px;">
                Bonjour, je suis votre Assistant IA
            </div>
            <div style="font-size:14px; color:#64748B; margin-bottom:32px; max-width:400px; line-height:1.7;">
                Je surveille votre infrastructure en temps réel et peux répondre à toutes vos
                questions sur les performances, incidents et disponibilité de vos sites.
            </div>

            <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
               target="_blank"
               class="btn-primary"
               style="font-size:15px; padding:14px 36px; border-radius:12px;
                      box-shadow:0 8px 24px rgba(22,151,194,0.4);">
                <i class="fas fa-comments"></i>
                Démarrer une conversation
            </a>

            <div style="margin-top:28px; display:flex; gap:20px; justify-content:center;">
                <div style="text-align:center;">
                    <div style="font-size:20px; font-weight:900; color:#1697C2;">6</div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">SITES SURVEILLÉS</div>
                </div>
                <div style="width:1px; background:#E0F2FE;"></div>
                <div style="text-align:center;">
                    <div style="font-size:20px; font-weight:900; color:#10B981;">24/7</div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">DISPONIBILITÉ</div>
                </div>
                <div style="width:1px; background:#E0F2FE;"></div>
                <div style="text-align:center;">
                    <div style="font-size:20px; font-weight:900; color:#7C3AED;">IA</div>
                    <div style="font-size:10px; color:#64748B; font-weight:600;">GEMINI 2.5</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="padding:12px 24px; background:#F0F9FF; border-top:1px solid #E0F2FE;
                    display:flex; align-items:center; gap:8px;">
            <i class="fas fa-shield-alt" style="color:#1697C2; font-size:12px;"></i>
            <span style="font-size:11px; color:#64748B; font-weight:500;">
                Données sécurisées • API protégée par clé secrète • Soft Seven Art — Casablanca
            </span>
        </div>
    </div>
</div>

<style>
@keyframes pulse-ring {
    0%   { transform: scale(0.95); opacity: 0.7; }
    50%  { transform: scale(1.05); opacity: 0.4; }
    100% { transform: scale(0.95); opacity: 0.7; }
}
</style>

@endsection