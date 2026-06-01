@extends('layouts.monitoring')
@section('title', 'Assistant IA')
@section('subtitle', 'Intelligence Artificielle — Monitoring Expert')

@section('content')

<div style="display:grid; grid-template-columns:300px 1fr; gap:24px; height:calc(100vh - 160px);">

    {{-- ═══ PANNEAU GAUCHE ═══ --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        <div class="card" style="text-align:center; padding:22px 18px;
                                  background:linear-gradient(135deg, var(--primary), var(--primary-dark));
                                  color:#FFFFFF; border:none;">
            <div style="width:64px; height:64px; background:rgba(255,255,255,0.15);
                        border-radius:50%; margin:0 auto 14px; display:flex;
                        align-items:center; justify-content:center; font-size:26px;">
                <i class="fas fa-robot"></i>
            </div>
            <div style="font-size:15px; font-weight:700; margin-bottom:4px;">Assistant MonitorPro</div>
            <div style="font-size:11px; opacity:0.85; margin-bottom:12px;">Google Gemini 2.5 Flash</div>
            <div style="display:flex; gap:6px; justify-content:center;">
                <span style="background:rgba(74,140,90,0.3); color:#FFFFFF;
                             padding:4px 10px; border-radius:20px; font-size:10.5px; font-weight:700;">EN LIGNE</span>
                <span style="background:rgba(255,255,255,0.18);
                             padding:4px 10px; border-radius:20px; font-size:10.5px; font-weight:700;">24h/7j</span>
            </div>
        </div>

        <div class="card" style="padding:16px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); margin-bottom:12px; text-transform:uppercase; letter-spacing:1px;">
                Capacités
            </div>
            @foreach([
                ['fa-chart-line',           'var(--success)', 'Analyse performances'],
                ['fa-exclamation-triangle', 'var(--danger)',  'Détection incidents'],
                ['fa-brain',                'var(--primary)', 'Prédiction pannes'],
                ['fa-balance-scale',        'var(--gold)',    'Comparaison sites'],
                ['fa-file-alt',             'var(--warning)', 'Rapports intelligents'],
                ['fa-lock',                 'var(--success)', 'Audit SSL & WHOIS'],
            ] as $cap)
            <div style="display:flex; align-items:center; gap:10px; padding:7px 0; border-bottom:1px solid var(--border-light);">
                <div style="width:26px; height:26px; border-radius:6px; background:{{ $cap[1] }}1A;
                            display:flex; align-items:center; justify-content:center;">
                    <i class="fas {{ $cap[0] }}" style="font-size:11px; color:{{ $cap[1] }};"></i>
                </div>
                <span style="font-size:12px; color:var(--text-secondary); font-weight:500;">{{ $cap[2] }}</span>
            </div>
            @endforeach
        </div>

        <div class="card" style="padding:14px 16px; flex:1; overflow-y:auto;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px;">
                Suggestions
            </div>
            @foreach([
                'Quel est l\'état général de mes sites ?',
                'Y a-t-il des incidents en cours ?',
                'Quel site est le plus rapide ?',
                'Y a-t-il des certificats SSL bientôt expirés ?',
                'Donne-moi un résumé des dernières 24h',
                'Quel site a le plus de problèmes ?',
            ] as $q)
                <button onclick="useExample('{{ addslashes($q) }}')"
                        style="display:block; width:100%; text-align:left; padding:9px 12px; margin-bottom:6px;
                               background:var(--bg-soft); border:1px solid var(--border-light);
                               border-radius:8px; cursor:pointer; font-size:12px;
                               color:var(--text-secondary); line-height:1.4; font-family:inherit;
                               transition:all .15s;"
                        onmouseover="this.style.background='var(--bg-alt)'; this.style.borderColor='var(--primary-light)';"
                        onmouseout="this.style.background='var(--bg-soft)'; this.style.borderColor='var(--border-light)';">
                    {{ $q }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ═══ PANNEAU DROIT — CHAT ═══ --}}
    <div class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column;">

        <div style="background:linear-gradient(135deg, var(--primary-dark), var(--primary));
                    padding:18px 24px; color:#FFFFFF;
                    display:flex; align-items:center; gap:14px;">
            <div style="width:42px; height:42px; background:rgba(255,255,255,0.18);
                        border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fas fa-robot"></i>
            </div>
            <div style="flex:1;">
                <div style="font-size:15px; font-weight:700;">MonitorPro Assistant IA</div>
                <div style="font-size:11.5px; opacity:0.85; margin-top:2px;">
                    Données temps réel · Google Gemini 2.5 Flash
                </div>
            </div>
            <span class="live-badge" style="background:rgba(74,140,90,0.25); color:#FFFFFF;">
                <span class="live-dot"></span> LIVE
            </span>
            {{-- ═══ BOUTON EFFACER L'HISTORIQUE ═══ --}}
            <button onclick="clearHistory()"
                    style="background:rgba(255,255,255,0.18); color:#FFFFFF; border:none;
                           padding:6px 12px; border-radius:6px; font-size:11px;
                           cursor:pointer; margin-left:8px; font-weight:600;">
                <i class="fas fa-trash"></i> Effacer
            </button>
        </div>

        {{-- Messages --}}
        <div id="chatMessages" style="flex:1; overflow-y:auto; padding:22px 24px;
                                       display:flex; flex-direction:column; gap:14px;
                                       background:var(--bg-soft);">
            {{-- Message d'accueil par défaut --}}
            <div class="bot-message">
                <div class="msg-avatar bot"><i class="fas fa-robot"></i></div>
                <div class="msg-bubble">Bonjour ! Je suis MonitorPro Assistant, expert en surveillance des sites web pour Soft Seven Art.

Je surveille 6 sites en temps réel et peux analyser :

• Les performances et temps de réponse
• Les incidents et pannes en cours
• Les certificats SSL et domaines
• Les prédictions de risques

Comment puis-je vous aider ?</div>
            </div>
        </div>

        {{-- Input --}}
        <div style="padding:14px 24px; border-top:1px solid var(--border); background:var(--bg-card);">
            <form onsubmit="return sendMessage(event)" style="display:flex; gap:10px;">
                <input type="text" id="chatInput" autocomplete="off"
                    placeholder="Posez votre question…"
                    class="form-input" style="flex:1;">
                <button type="submit" id="sendBtn" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Envoyer
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .bot-message, .user-message { display: flex; gap: 10px; max-width: 85%; }
    .user-message { align-self: flex-end; flex-direction: row-reverse; }
    .msg-avatar {
        width: 32px; height: 32px; min-width: 32px;
        border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        font-size: 13px; color: #FFFFFF;
    }
    .msg-avatar.bot  { background: var(--primary); }
    .msg-avatar.user { background: var(--gold); }
    .msg-bubble {
        background: #FFFFFF;
        border: 1px solid var(--border);
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.6;
        color: var(--text);
        white-space: pre-wrap;
        word-break: break-word;
        max-width: 100%;
        overflow-x: auto;
    }
    .user-message .msg-bubble {
        background: var(--primary);
        color: #FFFFFF;
        border-color: var(--primary);
    }

    /* Markdown styling dans la bulle bot */
    .msg-bubble h1 {
        font-size: 16px; font-weight: 700; color: var(--text);
        margin: 14px 0 8px; padding-bottom: 5px;
        border-bottom: 1px solid var(--border);
    }
    .msg-bubble h2 {
        font-size: 14px; font-weight: 700; color: var(--primary-dark);
        margin: 12px 0 6px; padding-left: 8px;
        border-left: 3px solid var(--primary);
    }
    .msg-bubble h3 {
        font-size: 13px; font-weight: 700; color: var(--text);
        margin: 10px 0 5px;
    }
    .msg-bubble strong { color: var(--text); font-weight: 700; }
    .msg-bubble code {
        background: var(--bg-alt); padding: 1px 6px;
        border-radius: 3px; font-size: 12px;
        font-family: 'SFMono-Regular', Menlo, monospace;
        color: var(--primary-dark);
    }
    .msg-bubble ul, .msg-bubble ol {
        margin: 6px 0; padding-left: 22px;
    }
    .msg-bubble li { margin: 3px 0; }

    /* Tableau dans bot bubble */
    .msg-bubble table {
        border-collapse: collapse;
        margin: 10px 0;
        font-size: 12px;
        width: 100%;
        background: var(--bg-card);
        border-radius: 6px;
        overflow: hidden;
    }
    .msg-bubble th {
        background: var(--primary);
        color: #FFFFFF;
        padding: 8px 12px;
        text-align: left;
        font-weight: 700;
        border: 1px solid var(--primary-dark);
    }
    .msg-bubble td {
        border: 1px solid var(--border);
        padding: 7px 12px;
        text-align: left;
    }
    .msg-bubble tr:nth-child(even) td { background: var(--bg-soft); }

    #chatMessages::-webkit-scrollbar { width: 8px; }
    #chatMessages::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 4px; }
</style>

<script>
    // ════════════════════════════════════════════════════════
    // 1. PERSISTANCE LOCALSTORAGE
    // ════════════════════════════════════════════════════════
    const STORAGE_KEY = 'monitorpro_chat_history_user_{{ auth()->id() }}';

    function saveHistory() {
        const msgs = [];
        document.querySelectorAll('#chatMessages > div').forEach(div => {
            if (div.classList.contains('loading-msg')) return;
            const isUser = div.classList.contains('user-message');
            const bubble = div.querySelector('.msg-bubble');
            if (!bubble) return;
            msgs.push({
                isUser: isUser,
                html: bubble.innerHTML
            });
        });
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(msgs));
        } catch (e) {
            console.warn('Impossible de sauvegarder l\'historique :', e);
        }
    }

    function loadHistory() {
        try {
            const data = localStorage.getItem(STORAGE_KEY);
            if (!data) return;
            const msgs = JSON.parse(data);
            if (!msgs || msgs.length === 0) return;

            const container = document.getElementById('chatMessages');
            container.innerHTML = ''; // Vide le message d'accueil par défaut

            msgs.forEach(m => {
                const div = document.createElement('div');
                div.className = m.isUser ? 'user-message' : 'bot-message';
                div.innerHTML = `
                    <div class="msg-avatar ${m.isUser ? 'user' : 'bot'}">
                        <i class="fas fa-${m.isUser ? 'user' : 'robot'}"></i>
                    </div>
                    <div class="msg-bubble">${m.html}</div>`;
                container.appendChild(div);
            });
            container.scrollTop = container.scrollHeight;
        } catch (e) {
            console.warn('Erreur chargement historique :', e);
        }
    }

    function clearHistory() {
        if (!confirm('Effacer toute la conversation ? Cette action est irréversible.')) return;
        localStorage.removeItem(STORAGE_KEY);
        location.reload();
    }

    // Charger l'historique au chargement de la page
    window.addEventListener('DOMContentLoaded', loadHistory);

    // ════════════════════════════════════════════════════════
    // 2. EXEMPLE PRÊT (suggestions cliquables)
    // ════════════════════════════════════════════════════════
    function useExample(text) {
        document.getElementById('chatInput').value = text;
        document.getElementById('chatInput').focus();
    }

    // ════════════════════════════════════════════════════════
    // 3. FORMATAGE MARKDOWN AMÉLIORÉ
    // ════════════════════════════════════════════════════════
    function formatBotResponse(text) {
        // Échapper le HTML pour la sécurité
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // ─── Titres (###, ##, #) ───
        html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.+)$/gm,  '<h2>$1</h2>');
        html = html.replace(/^# (.+)$/gm,   '<h1>$1</h1>');

        // ─── Gras **texte** ───
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

        // ─── Code inline `code` ───
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

        // ─── Tableaux Markdown | a | b | ───
        const lines = html.split('\n');
        let inTable = false;
        let result = [];
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            // Séparateur |---|---|
            if (line.match(/^\s*\|[\s\-:|]+\|\s*$/)) continue;
            // Ligne de tableau
            if (line.match(/^\s*\|.*\|\s*$/)) {
                const cells = line.split('|').slice(1, -1).map(c => c.trim());
                if (!inTable) {
                    result.push('<table>');
                    result.push('<tr>' + cells.map(c => '<th>' + c + '</th>').join('') + '</tr>');
                    inTable = true;
                } else {
                    result.push('<tr>' + cells.map(c => '<td>' + c + '</td>').join('') + '</tr>');
                }
            } else {
                if (inTable) {
                    result.push('</table>');
                    inTable = false;
                }
                result.push(line);
            }
        }
        if (inTable) result.push('</table>');
        html = result.join('\n');

        // ─── Listes à puces (•, -, *) ───
        html = html.replace(/^[\*\-•]\s+(.+)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.+<\/li>(\n|$))+/g, m => '<ul>' + m.replace(/\n/g, '') + '</ul>');

        // ─── Listes numérotées 1. 2. 3. ───
        html = html.replace(/^(\d+)\.\s+(.+)$/gm, '<li data-num="$1">$2</li>');

        // ─── Retours à la ligne en <br> ───
        html = html.replace(/\n/g, '<br>');

        // ─── Nettoyer les <br> superflus autour des balises bloc ───
        html = html.replace(/<br>(<\/?(?:h[1-3]|ul|ol|li|table|tr|td|th)>)/g, '$1');
        html = html.replace(/(<\/?(?:h[1-3]|ul|ol|li|table|tr|td|th)>)<br>/g, '$1');

        return html;
    }

    // ════════════════════════════════════════════════════════
    // 4. AJOUTER UN MESSAGE
    // ════════════════════════════════════════════════════════
    function addMessage(text, isUser = false) {
        const msg = document.createElement('div');
        msg.className = isUser ? 'user-message' : 'bot-message';
        const bubbleContent = isUser
            ? text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            : formatBotResponse(text);

        msg.innerHTML = `
            <div class="msg-avatar ${isUser ? 'user' : 'bot'}">
                <i class="fas fa-${isUser ? 'user' : 'robot'}"></i>
            </div>
            <div class="msg-bubble">${bubbleContent}</div>`;
        const container = document.getElementById('chatMessages');
        container.appendChild(msg);
        container.scrollTop = container.scrollHeight;

        // ─── Sauvegarder l'historique ───
        saveHistory();
    }

    // ════════════════════════════════════════════════════════
    // 5. ENVOI DE MESSAGE
    // ════════════════════════════════════════════════════════
    async function sendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text  = input.value.trim();
        if (!text) return false;

        addMessage(text, true);
        input.value = '';
        document.getElementById('sendBtn').disabled = true;

        // Message de chargement
        const loadingMsg = document.createElement('div');
        loadingMsg.className = 'bot-message loading-msg';
        loadingMsg.innerHTML = `
            <div class="msg-avatar bot"><i class="fas fa-robot"></i></div>
            <div class="msg-bubble"><i class="fas fa-circle-notch fa-spin"></i> Analyse en cours…</div>`;
        document.getElementById('chatMessages').appendChild(loadingMsg);
        document.getElementById('chatMessages').scrollTop = 99999;

        try {
            const res = await fetch('{{ route("chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });

            loadingMsg.remove();

            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();
            const response = data.reply || data.response || data.output || data.text
              || 'Désolé, je n\'ai pas pu traiter votre demande.';

            addMessage(response, false);
        } catch (err) {
            loadingMsg.remove();
            addMessage(
                'Désolé, une erreur est survenue.Le service IA est temporairement indisponible. <br><small>Détails : ' + err.message + '</small>',
                false
            );
        }

        document.getElementById('sendBtn').disabled = false;
        return false;
    }
</script>

@endsection