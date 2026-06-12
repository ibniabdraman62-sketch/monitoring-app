<div class="notif-bell" id="notif-bell">
    <button type="button" class="notif-bell__btn" id="notif-bell-btn" aria-label="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
        </svg>
        <span class="notif-bell__badge" id="notif-bell-badge" hidden>0</span>
    </button>

    <div class="notif-bell__panel" id="notif-bell-panel" hidden>
        <div class="notif-bell__header">
            <span class="notif-bell__title">
                <span class="notif-bell__dot"></span> Notifications
            </span>
            <button type="button" class="notif-bell__mark-all" id="notif-mark-all">
                Tout marquer comme lu
            </button>
        </div>

        <div class="notif-bell__list" id="notif-bell-list">
            <div class="notif-bell__empty">Chargement…</div>
        </div>

        <a href="{{ route('alertes.index') }}" class="notif-bell__footer" id="notif-voir-tout">
            Voir toutes les alertes →
        </a>
    </div>
</div>

@once
<style>
    .notif-bell { position: relative; display: inline-flex; }
    .notif-bell__btn {
        position: relative; display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 10px;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
        color: #cbd5e1; cursor: pointer; transition: all .2s ease;
    }
    .notif-bell__btn:hover {
        background: rgba(212,168,87,0.1); border-color: rgba(212,168,87,0.3); color: #D4A857;
    }
    .notif-bell__badge {
        position: absolute; top: -4px; right: -4px;
        min-width: 18px; height: 18px; padding: 0 5px;
        background: #ef4444; color: #fff; border-radius: 999px;
        font-size: 11px; font-weight: 700; line-height: 18px; text-align: center;
        box-shadow: 0 0 0 2px #1E3A5F;
        animation: notif-pulse 2s infinite;
    }
    @keyframes notif-pulse {
        0%, 100% { box-shadow: 0 0 0 2px #1E3A5F, 0 0 0 0 rgba(239,68,68,0.5); }
        50%      { box-shadow: 0 0 0 2px #1E3A5F, 0 0 0 6px rgba(239,68,68,0); }
    }
    .notif-bell__panel {
        position: absolute; top: calc(100% + 10px); right: 0;
        width: 380px; max-width: calc(100vw - 30px);
        background: linear-gradient(180deg, #2C5F8B 70%, #4078A9 100%); color: #e2e8f0;
        border: 1px solid rgba(212,168,87,0.2); border-radius: 14px;
        box-shadow: 0 18px 50px rgba(0,0,0,0.45);
        z-index: 1050; overflow: hidden;
    }
    .notif-bell__header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px; border-bottom: 1px solid rgba(212,168,87,0.15);
    }
    .notif-bell__title {
        font-weight: 700; color: #D4A857; display: flex; align-items: center; gap: 8px;
    }
    .notif-bell__dot {
        width: 8px; height: 8px; border-radius: 50%; background: #D4A857;
        box-shadow: 0 0 8px rgba(212,168,87,0.6);
    }
    .notif-bell__mark-all { 
        background: none; border: none; color: #e2e8f0;
        font-size: 12px; cursor: pointer; padding: 0;
    }
    .notif-bell__mark-all:hover { color: #D4A857; text-decoration: underline; }
    .notif-bell__list { max-height: 420px; overflow-y: auto; }
    .notif-bell__list::-webkit-scrollbar { width: 6px; }
    .notif-bell__list::-webkit-scrollbar-thumb { background: rgba(212,168,87,0.2); border-radius: 3px; }
    .notif-item {
        display: flex; gap: 12px; padding: 12px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        border-left: 3px solid transparent;
        text-decoration: none; color: inherit;
        transition: background .15s ease, border-color .15s ease;
    }
    .notif-item:hover { background: rgba(212,168,87,0.06); }
    .notif-item--unread {
        background: rgba(212,168,87,0.05);
        border-left-color: #D4A857;
    }
    .notif-item__icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        font-size: 16px; font-weight: 700;
    }
    .notif-item__icon--danger  { background: rgba(239,68,68,0.15);  color: #ef4444; }
    .notif-item__icon--warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .notif-item__icon--info    { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .notif-item__body { flex: 1; min-width: 0; }
    .notif-item__site { font-weight: 600; font-size: 13px; color: #ffffff; }
    .notif-item__msg {
        font-size: 12px; color: #e2e8f0;
        overflow: hidden; text-overflow: ellipsis;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        margin: 2px 0;
    }
    .notif-item__date { font-size: 11px; color: #cbd5e1; }
    .notif-bell__empty {
        padding: 32px 16px; text-align: center; color: #64748b; font-size: 13px;
    }
    .notif-bell__footer {
        display: block; padding: 12px; text-align: center;
        font-size: 13px; color: #D4A857; text-decoration: none; font-weight: 600;
        border-top: 1px solid rgba(212,168,87,0.15);
        background: rgba(0,0,0,0.15);
        transition: background .15s ease;
    }
    .notif-bell__footer:hover { background: rgba(212,168,87,0.1); }
</style>

<script>
(function () {
    const bell    = document.getElementById('notif-bell');
    const btn     = document.getElementById('notif-bell-btn');
    const panel   = document.getElementById('notif-bell-panel');
    const badge   = document.getElementById('notif-bell-badge');
    const list    = document.getElementById('notif-bell-list');
    const markAll = document.getElementById('notif-mark-all');

    const urls = {
        index:   "{{ route('notifications.index') }}",
        markAll: "{{ route('notifications.toutes-lues') }}",
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function setBadge(n) {
        if (n > 0) {
            badge.hidden = false;
            badge.textContent = n > 99 ? '99+' : n;
        } else {
            badge.hidden = true;
        }
    }

    function iconClass(sev) {
        const s = (sev || '').toLowerCase();
        if (['critique','high','danger','critical'].includes(s)) return 'notif-item__icon--danger';
        if (['avertissement','warning','medium'].includes(s))     return 'notif-item__icon--warning';
        return 'notif-item__icon--info';
    }

    function iconChar(sev) {
        const s = (sev || '').toLowerCase();
        if (['critique','high','danger','critical'].includes(s)) return '!';
        if (['avertissement','warning','medium'].includes(s))     return '⚠';
        return 'i';
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    function render(alertes) {
        if (!alertes.length) {
            list.innerHTML = '<div class="notif-bell__empty">Aucune notification</div>';
            return;
        }
        list.innerHTML = alertes.map(a => `
             
            <a href="${escapeHtml(a.url)}" 
   class="notif-item ${a.lue ? '' : 'notif-item--unread'}"
   onclick="marquerLueEtRediriger(event, ${a.id}, '${escapeHtml(a.url)}')">

                <span class="notif-item__icon ${iconClass(a.severite)}">${iconChar(a.severite)}</span>
                <div class="notif-item__body">
                    <div class="notif-item__site">${escapeHtml(a.site)}</div>
                    <div class="notif-item__msg">${escapeHtml(a.message || a.type)}</div>
                    <div class="notif-item__date">${escapeHtml(a.date || '')}</div>
                </div>
            </a>
        `).join('');
    }

    async function load() {
        try {
            const r = await fetch(urls.index, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const data = await r.json();
            setBadge(data.count);
            render(data.alertes);
        } catch (e) {
            list.innerHTML = '<div class="notif-bell__empty">Erreur de chargement</div>';
        }
    }

    async function markAllRead() {
        try {
            await fetch(urls.markAll, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            });
            await load();
        } catch (e) {}
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const willOpen = panel.hidden;
        panel.hidden = !willOpen;
        if (willOpen) load();
    });

    document.addEventListener('click', (e) => {
    if (!bell.contains(e.target)) panel.hidden = true;
});

bell.addEventListener('mouseleave', () => {
    setTimeout(() => { panel.hidden = true; }, 300);
});

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') panel.hidden = true;
    });

    markAll.addEventListener('click', (e) => {
        e.stopPropagation();
        markAllRead();
    });

    async function marquerLueEtRediriger(e, id, url) {
    e.preventDefault();
    try {
        const lueUrl = "{{ url('/') }}/notifications/" + id + "/lue";
        console.log('URL appelée:', lueUrl); // Pour déboguer
        const response = await fetch(lueUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('Réponse:', response.status); // Pour déboguer
        await load();
    } catch(err) {
        console.error('Erreur:', err);
    }
    window.location.href = url;
}
//             method: 'POST',
//             headers: {
//                 'X-CSRF-TOKEN': csrf,
//                 'Accept': 'application/json',
//                 'X-Requested-With': 'XMLHttpRequest'
//             }
//         });
//         await load(); // Rafraîchit le badge
//     } catch(e) {}
//     window.location.href = url; // Redirige après
// }


    // Init + polling toutes les 30s
    load();
    setInterval(load, 30000);

    document.getElementById('notif-voir-tout').addEventListener('click', async (e) => {
    await fetch(urls.markAll, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
    });
    setBadge(0);
});

})();
</script>
@endonce