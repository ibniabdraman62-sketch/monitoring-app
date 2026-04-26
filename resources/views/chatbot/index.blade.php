@extends('layouts.monitoring')
@section('title', 'Assistant IA')
@section('subtitle', 'Posez vos questions en langage naturel')

@section('content')

<div style="display:grid; grid-template-columns:1fr 2fr; gap:24px;">

    <!-- Panneau gauche — Suggestions -->
    <div>
        <div class="card" style="margin-bottom:16px;">
            <div class="card-title">
                <i class="fas fa-lightbulb" style="color:#1697C2;"></i>
                Questions suggérées
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach([
                    'Quel site est le plus lent ?',
                    'Y a-t-il des sites en danger ?',
                    'Compare Amazon et Google',
                    'Donne-moi un rapport complet',
                    'Quels certificats SSL expirent bientôt ?',
                    'Prédis les pannes des prochaines 24h',
                    'Quel est le score global du système ?',
                    'Classe tous les sites par performance',
                ] as $question)
                <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
                   target="_blank"
                   style="background:#F0F9FF; border:1px solid #E0F2FE;
                          border-radius:8px; padding:10px 14px;
                          font-size:12px; color:#0C3547; text-decoration:none;
                          transition:all 0.2s; display:block;"
                   onmouseover="this.style.background='#E0F2FE'"
                   onmouseout="this.style.background='#F0F9FF'">
                    <i class="fas fa-chevron-right" style="color:#1697C2; font-size:10px;"></i>
                    {{ $question }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Infos système -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-info-circle" style="color:#1697C2;"></i>
                À propos de l'Assistant
            </div>
            <div style="font-size:12px; color:#64748B; line-height:1.8;">
                <div style="margin-bottom:8px;">
                    <i class="fas fa-robot" style="color:#1697C2;"></i>
                    <strong>Modèle :</strong> Google Gemini 2.5 Flash
                </div>
                <div style="margin-bottom:8px;">
                    <i class="fas fa-database" style="color:#1697C2;"></i>
                    <strong>Source :</strong> Données temps réel
                </div>
                <div style="margin-bottom:8px;">
                    <i class="fas fa-shield-alt" style="color:#1697C2;"></i>
                    <strong>Sécurité :</strong> API protégée
                </div>
                <div style="margin-bottom:8px;">
                    <i class="fas fa-language" style="color:#1697C2;"></i>
                    <strong>Langue :</strong> Français
                </div>
                <div>
                    <i class="fas fa-clock" style="color:#1697C2;"></i>
                    <strong>Disponible :</strong> 24h/24 7j/7
                </div>
            </div>
        </div>
    </div>

    <!-- Panneau droit — Chat iframe -->
    <div style="background:#fff; border:1px solid #E0F2FE; border-radius:16px;
                overflow:hidden; box-shadow:0 2px 12px rgba(22,151,194,0.08);
                height:700px; display:flex; flex-direction:column;">

        <!-- Header chat -->
        <div style="background:linear-gradient(135deg,#0C3547,#1697C2);
                    padding:18px 24px; display:flex; align-items:center; gap:14px;">
            <div style="width:44px; height:44px; background:rgba(255,255,255,0.15);
                        border-radius:12px; display:flex; align-items:center;
                        justify-content:center; font-size:22px;">🤖</div>
            <div style="flex:1;">
                <div style="font-size:16px; font-weight:800; color:#fff;">
                    MonitorPro Assistant
                </div>
                <div style="font-size:11px; color:rgba(255,255,255,0.7); margin-top:2px;">
                    Powered by Google Gemini AI · Données en temps réel
                </div>
            </div>
            <div style="display:flex; gap:6px;">
                <span style="background:rgba(16,185,129,0.25); color:#6EE7B7;
                             border:1px solid rgba(16,185,129,0.4);
                             padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700;">
                    ● EN LIGNE
                </span>
            </div>
        </div>

        <!-- Contenu -->
        <div style="flex:1; display:flex; flex-direction:column; align-items:center;
                    justify-content:center; padding:32px; text-align:center; background:#F8FAFF;">
            <div style="font-size:48px; margin-bottom:16px;">🤖</div>
            <div style="font-size:18px; font-weight:700; color:#0C3547; margin-bottom:8px;">
                Ouvrir le Chat IA
            </div>
            <div style="font-size:13px; color:#64748B; margin-bottom:24px; max-width:300px;">
                Le chatbot s'ouvre dans un nouvel onglet pour une meilleure expérience
            </div>
            <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
               target="_blank"
               class="btn-primary"
               style="font-size:14px; padding:12px 28px;">
                <i class="fas fa-comments"></i>
                Lancer l'Assistant IA
            </a>
            <div style="margin-top:20px; font-size:11px; color:#94A3B8;">
                Ou utilisez le bouton 🤖 en bas à droite de chaque page
            </div>
        </div>
    </div>
</div>

@endsection