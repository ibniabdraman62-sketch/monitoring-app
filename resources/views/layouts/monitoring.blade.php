<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MonitorPro') }}-MONITORPRO</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-soft7art.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            /* ── Palette beige cream + bleu ciel ── */
            --bg-page         : #FBF8F0;
            --bg-card         : #FFFFFF;
            --bg-alt          : #F5EFE0;
            --bg-soft         : #FAF5E6;

            --border          : #E8DFC9;
            --border-light    : #F0E8D4;
            --border-strong   : #D9CDB0;

            --text            : #3D2F1F;
            --text-secondary  : #5C4B36;
            --text-muted      : #8B7855;
            --text-light      : #B5A684;

            --primary         : #5B95C4;
            --primary-dark    : #4078A9;
            --primary-light   : #A6C8E0;
            --primary-bg      : #E1EEFA;
            --primary-soft    : #EFF5FB;

            --success         : #4A8C5A;
            --success-bg      : #DFF0E1;
            --warning         : #C48A4A;
            --warning-bg      : #F5E9D6;
            --danger          : #B66258;
            --danger-bg       : #F2DCD8;

            --gold            : #C9A876;
            --gold-light      : #E8D4A6;

            --sidebar-w       : 240px;
            --header-h        : 64px;
            --radius          : 10px;
            --radius-lg       : 14px;
            --shadow-sm       : 0 1px 2px rgba(61,47,31,.04);
            --shadow          : 0 2px 8px rgba(61,47,31,.06);
            --shadow-md       : 0 4px 12px rgba(61,47,31,.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-page);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ────── SIDEBAR ────── */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-w);
            background: linear-gradient(180deg, #2C5F8B 0%, #4078A9 100%);
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 2px 0 12px rgba(64,120,169,0.15);
        }

        .sidebar-logo {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            gap: 12px;
            height: var(--header-h);
        }
        .sidebar-logo img {
            width: 38px; height: 38px;
            border-radius: 9px;
            background: rgba(255,255,255,0.15);
            padding: 2px;
        }
        .sidebar-logo span {
            font-size: 15px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.2px;
        }
        .sidebar-logo small {
            font-size: 10.5px;
            color: rgba(255,255,255,0.65);
            display: block;
            margin-top: 2px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .sidebar-nav { flex: 1; padding: 14px 12px; overflow-y: auto; }

        .nav-section {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 12px 8px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 14px;
            border-radius: var(--radius);
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all .15s;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.12);
            color: #FFFFFF;
        }
        .nav-item.active {
            background: #FFFFFF;
            color: #2C5F8B;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .nav-item i {
            width: 16px;
            font-size: 13px;
            text-align: center;
            opacity: 0.85;
        }
        .nav-item.active i { opacity: 1; }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,0.12);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius);
            background: rgba(255,255,255,0.10);
        }
        .avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role { font-size: 10.5px; color: rgba(255,255,255,0.65); }

        .logout-btn {
            color: rgba(255,255,255,0.7);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: all .15s;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.15); color: #FFFFFF; }

        /* ────── MAIN ────── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }

        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            min-height: var(--header-h);
            box-shadow: var(--shadow-sm);
        }
        .topbar h1 {
            font-size: 19px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.3px;
        }
        .topbar .subtitle {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--success-bg);
            color: var(--success);
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
        }
        .live-dot {
            width: 7px; height: 7px;
            background: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(74,140,90,0.2);
        }

        .topbar-time {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 500;
            font-variant-numeric: tabular-nums;
        }

        .content { padding: 24px 28px 40px; }

        /* ────── CARDS ────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }
        .card-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.2px;
        }

        /* ────── BUTTONS ────── */
        .btn-primary {
            background: var(--primary);
            color: #FFFFFF;
            padding: 9px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--primary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: all .15s;
            white-space: nowrap;
            line-height: 1;
            font-family: inherit;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #FFFFFF;
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-secondary);
            border: 1px solid var(--border-strong);
            padding: 9px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            text-decoration: none;
            transition: all .15s;
            font-family: inherit;
        }
        .btn-secondary:hover { background: var(--bg-alt); color: var(--text); }

        .btn-success {
            background: var(--success) !important;
            color: #FFFFFF !important;
            border-color: var(--success) !important;
        }
        .btn-success:hover { background: #3D7549 !important; border-color: #3D7549 !important; }

        .btn-danger {
            background: var(--danger) !important;
            color: #FFFFFF !important;
            border-color: var(--danger) !important;
        }
        .btn-danger:hover { background: #99514A !important; border-color: #99514A !important; }

        .btn-warning {
            background: var(--warning) !important;
            color: #FFFFFF !important;
            border-color: var(--warning) !important;
        }
        .btn-warning:hover { background: #A6743C !important; border-color: #A6743C !important; }

        .btn-gold {
            background: var(--gold) !important;
            color: #FFFFFF !important;
            border-color: var(--gold) !important;
        }
        .btn-gold:hover { background: #B59342 !important; border-color: #B59342 !important; }

        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-xs { padding: 5px 10px; font-size: 11.5px; }

        /* ────── BADGES ────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
        }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-green   { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-yellow  { background: var(--warning-bg); color: var(--warning); }
        .badge-danger  { background: var(--danger-bg);  color: var(--danger);  }
        .badge-red     { background: var(--danger-bg);  color: var(--danger);  }
        .badge-info    { background: var(--primary-bg); color: var(--primary-dark); }
        .badge-blue    { background: var(--primary-bg); color: var(--primary-dark); }
        .badge-neutral { background: var(--bg-alt);     color: var(--text-secondary); }
        .badge-gray    { background: var(--bg-alt);     color: var(--text-secondary); }

        .badge-dot::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ────── STATUS DOTS ────── */
        .status-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-dot.online  { background: var(--success); box-shadow: 0 0 0 3px rgba(74,140,90,0.2); }
        .status-dot.offline { background: var(--danger); box-shadow: 0 0 0 3px rgba(182,98,88,0.2); }
        .status-dot.unknown { background: var(--text-light); }

        /* ────── FORMS ────── */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            padding: 10px 13px;
            color: var(--text);
            font-size: 13.5px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91,149,196,0.15);
        }
        .form-error {
            color: var(--danger);
            font-size: 11.5px;
            margin-top: 4px;
            font-weight: 600;
        }
        .form-help {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ────── TABLES ────── */
        .table-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .table-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-card);
            gap: 12px;
            flex-wrap: wrap;
        }
        .table-scroll {
            overflow-x: auto;
            max-height: 560px;
            overflow-y: auto;
        }
        .table-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .table-scroll::-webkit-scrollbar-track { background: var(--bg-alt); }
        .table-scroll::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 4px; }
        .table-scroll::-webkit-scrollbar-thumb:hover { background: var(--text-light); }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            position: sticky; top: 0;
            background: var(--bg-soft);
            padding: 11px 16px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody tr { border-top: 1px solid var(--border-light); transition: background .15s; }
        tbody tr:hover { background: var(--bg-soft); }
        tbody td { padding: 12px 16px; font-size: 13px; color: var(--text-secondary); vertical-align: middle; }

        /* ────── ALERTS / MESSAGES ────── */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            border: 1px solid;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: var(--success-bg); border-color: #B0DBB6; color: var(--success); }
        .alert-error   { background: var(--danger-bg);  border-color: #E5BAB3; color: var(--danger);  }
        .alert-warning { background: var(--warning-bg); border-color: #E5C58E; color: var(--warning); }
        .alert-info    { background: var(--primary-bg); border-color: var(--primary-light); color: var(--primary-dark); }

        /* ────── KPI CARDS ────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            background: var(--bg-card);
            border: 1px solid var(--border);
        }
        .kpi-card.blue   { background: linear-gradient(135deg, #5B95C4, #4078A9); color: #FFFFFF; border: none; }
        .kpi-card.green  { background: linear-gradient(135deg, #4A8C5A, #3D7549); color: #FFFFFF; border: none; }
        .kpi-card.red    { background: linear-gradient(135deg, #B66258, #99514A); color: #FFFFFF; border: none; }
        .kpi-card.gold   { background: linear-gradient(135deg, #C9A876, #B59342); color: #FFFFFF; border: none; }

        .kpi-card::before {
            content: ''; position: absolute;
            top: -20px; right: -20px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .kpi-card::after {
            content: ''; position: absolute;
            bottom: -30px; right: 30px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .kpi-label {
            font-size: 11.5px;
            color: rgba(255,255,255,0.85);
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .kpi-value {
            font-size: 34px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1;
            letter-spacing: -1px;
            font-variant-numeric: tabular-nums;
        }
        .kpi-icon {
            position: absolute;
            right: 20px; top: 50%;
            transform: translateY(-50%);
            font-size: 32px;
            opacity: 0.25;
            color: #FFFFFF;
        }

        /* ────── UPTIME BAR ────── */
        .uptime-bar {
            height: 6px;
            background: var(--bg-alt);
            border-radius: 3px;
            overflow: hidden;
        }
        .uptime-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        /* ────── MODAL ────── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(61,47,31,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 24px;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(61,47,31,0.25);
            overflow: hidden;
        }
        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title { font-size: 16px; font-weight: 700; color: var(--text); }
        .modal-close {
            background: none; border: none; cursor: pointer;
            padding: 4px; border-radius: 6px;
            color: var(--text-muted); font-size: 18px;
        }
        .modal-close:hover { background: var(--bg-alt); color: var(--text); }
        .modal-body { padding: 22px; }
        .modal-footer {
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            background: var(--bg-soft);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* ────── UTILS ────── */
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 12.5px; }
        .text-xs { font-size: 11.5px; }
        .font-mono { font-family: 'SFMono-Regular', Menlo, Consolas, monospace; font-variant-numeric: tabular-nums; }
        .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mb-12 { margin-bottom: 12px; }
        .mb-16 { margin-bottom: 16px; }
        .mb-24 { margin-bottom: 24px; }

        /* ────── CHATBOT FLOATING ────── */
        .chat-bubble {
            position: fixed; bottom: 24px; right: 24px;
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 9999;
            box-shadow: 0 4px 20px rgba(64,120,169,0.4);
            transition: transform .2s;
            text-decoration: none;
            color: #FFFFFF;
            font-size: 24px;
        }
        .chat-bubble:hover { transform: scale(1.1); }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

{{-- ═════════ SIDEBAR ═════════ --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo-soft7art.svg') }}" alt="Soft7Art">
        <div>
            <span>MonitorPro</span>
            <small>Soft Seven Art</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Pilotage</div>
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Tableau de bord
        </a>
        <a href="{{ route('sites.index') }}"
           class="nav-item {{ request()->routeIs('sites.*') ? 'active' : '' }}">
            <i class="fas fa-globe"></i> Sites surveillés
        </a>

        <div class="nav-section">Activité</div>
        <a href="{{ route('alertes.index') }}"
           class="nav-item {{ request()->routeIs('alertes.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Alertes
        </a>
        <a href="{{ route('incidents.index') }}"
           class="nav-item {{ request()->routeIs('incidents.*') ? 'active' : '' }}">
            <i class="fas fa-exclamation-circle"></i> Incidents
        </a>
        <a href="{{ route('rapports.index') }}"
           class="nav-item {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
            <i class="fas fa-file-pdf"></i> Rapports PDF
        </a>

        <div class="nav-section">Intelligence</div>
        <a href="{{ route('chatbot.index') }}"
           class="nav-item {{ request()->routeIs('chatbot.*') ? 'active' : '' }}">
            <i class="fas fa-robot"></i> Assistant IA
        </a>

    {{-- ═══════════ ADMINISTRATION ═══════════ --}}
        @auth
@if(auth()->user()->role !== 'client')
                <div class="nav-section">Administration</div>

                <a href="{{ route('statistiques.index') }}"
                   class="nav-item {{ request()->routeIs('statistiques.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Statistiques
                </a>

                <a href="{{ route('audit.index') }}"
                   class="nav-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                    <i class="fas fa-clock-rotate-left"></i> Historique d'audit
                </a>

                <a href="{{ route('clients.index') }}"
                   class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> Gestion Clients
                </a>

                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('cron.index') }}"
                       class="nav-item {{ request()->routeIs('cron.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i> Supervision Cron
                    </a>
                    <a href="{{ route('agents.index') }}"
                       class="nav-item {{ request()->routeIs('agents.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Gestion Agents
                    </a>
                @endif
            @endif
        @endauth

        <div class="nav-section">Compte</div>
        <a href="{{ route('profile.edit') }}"
           class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user"></i> Mon profil
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                <div class="user-role">
                    {{ auth()->user()->isSuperAdmin() ? 'Super Administrateur' : (auth()->user()->role === 'agent' ? 'Agent' : 'Client') }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Se déconnecter">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ═════════ MAIN ═════════ --}}
<div class="main">
    <div class="topbar">
        <div>
            <h1>@yield('title', 'Tableau de bord')</h1>
            <div class="subtitle">@yield('subtitle', 'Système intelligent de monitoring')</div>
        </div>
        <div class="topbar-right">
            <span class="live-badge"><span class="live-dot"></span> LIVE</span>
            <span class="topbar-time" id="liveClock"></span>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- ═════════ Chatbot bubble ═════════ --}}
<a href="{{ route('chatbot.index') }}" class="chat-bubble" title="Assistant IA">
    <i class="fas fa-robot"></i>
</a>

<script>
    function updateClock() {
        const now = new Date();
        const d = now.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const t = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const el = document.getElementById('liveClock');
        if (el) el.textContent = d + ' · ' + t;
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

@yield('scripts')
</body>
</html>