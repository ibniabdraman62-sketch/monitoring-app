<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Arial', sans-serif; background: #F0F9FF; color: #0F172A; }

        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: 240px;
            background: linear-gradient(180deg, #0C3547 0%, #1697C2 100%);
            display: flex; flex-direction: column; z-index: 100;
            box-shadow: 4px 0 20px rgba(22,151,194,0.3);
        }
        .sidebar-logo {
            padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-logo .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #53EAFD, #4BC3EB);
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; font-size: 20px;
            box-shadow: 0 4px 12px rgba(83,234,253,0.4);
        }
        .sidebar-logo span { font-size: 17px; font-weight: 800; color: #fff; }
        .sidebar-logo small { font-size: 10px; color: rgba(255,255,255,0.6); display: block; margin-top: 2px; }

        .sidebar-nav { flex: 1; padding: 16px 12px; }
        .nav-section {
            font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.4);
            text-transform: uppercase; letter-spacing: 1.5px; padding: 10px 8px 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px; padding: 11px 14px;
            border-radius: 10px; color: rgba(255,255,255,0.75); text-decoration: none;
            font-size: 14px; font-weight: 500; margin-bottom: 3px; transition: all 0.2s;
        }
        .nav-item:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .nav-item.active {
            background: linear-gradient(135deg, #53EAFD, #4BC3EB);
            color: #0C3547; font-weight: 700;
            box-shadow: 0 4px 12px rgba(83,234,253,0.4);
        }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; }

        .sidebar-footer {
            padding: 16px; border-top: 1px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; gap: 10px;
        }
        .avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #53EAFD, #4BC3EB);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 14px; font-weight: 800; color: #0C3547;
        }
        .sidebar-footer .user-name { font-size: 13px; font-weight: 700; color: #fff; }
        .sidebar-footer .user-role { font-size: 11px; color: rgba(255,255,255,0.5); }

        .main { margin-left: 240px; min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #E0F2FE;
            padding: 16px 32px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
            box-shadow: 0 2px 10px rgba(22,151,194,0.08);
        }
        .topbar h1 { font-size: 22px; font-weight: 800; color: #0C3547; }
        .topbar .subtitle { font-size: 13px; color: #64748B; margin-top: 2px; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .btn-primary {
            background: linear-gradient(135deg, #1697C2, #4BC3EB);
            color: #fff; padding: 9px 18px; border-radius: 9px;
            font-size: 13px; font-weight: 700; text-decoration: none;
            border: none; cursor: pointer; display: inline-flex;
            align-items: center; gap: 6px; transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(22,151,194,0.3);
        }
        .btn-primary:hover { opacity: 0.9; color: #fff; transform: translateY(-1px); }
        .btn-danger  { background: linear-gradient(135deg, #DC2626, #EF4444) !important; box-shadow: 0 4px 12px rgba(220,38,38,0.3) !important; }
        .btn-success { background: linear-gradient(135deg, #059669, #10B981) !important; box-shadow: 0 4px 12px rgba(5,150,105,0.3) !important; }
        .btn-warning { background: linear-gradient(135deg, #D97706, #F59E0B) !important; box-shadow: 0 4px 12px rgba(217,119,6,0.3) !important; }

        .content { padding: 32px; }

        .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .status-dot.online  { background: #10B981; animation: pulse-green 2s infinite; }
        .status-dot.offline { background: #EF4444; animation: pulse-red 2s infinite; }
        .status-dot.unknown { background: #94A3B8; }

        @keyframes pulse-green {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
            70%  { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        @keyframes pulse-red {
            0%   { box-shadow: 0 0 0 0 rgba(239,68,68,0.7); }
            70%  { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
            100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }

        .card {
            background: #fff; border: 1px solid #E0F2FE;
            border-radius: 14px; padding: 24px;
            box-shadow: 0 2px 12px rgba(22,151,194,0.08);
        }
        .card-title { font-size: 15px; font-weight: 700; color: #0C3547; margin-bottom: 16px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .kpi-card {
            border-radius: 14px; padding: 24px; position: relative;
            overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .kpi-card.blue   { background: linear-gradient(135deg, #1697C2, #4BC3EB); }
        .kpi-card.green  { background: linear-gradient(135deg, #059669, #10B981); }
        .kpi-card.red    { background: linear-gradient(135deg, #DC2626, #EF4444); }
        .kpi-card.purple { background: linear-gradient(135deg, #7C3AED, #8B5CF6); }
        .kpi-card::before {
            content: ''; position: absolute; top: -20px; right: -20px;
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        .kpi-card::after {
            content: ''; position: absolute; bottom: -30px; right: 30px;
            width: 80px; height: 80px; border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .kpi-label { font-size: 12px; color: rgba(255,255,255,0.8); font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-value { font-size: 42px; font-weight: 900; color: #fff; line-height: 1; }
        .kpi-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 36px; opacity: 0.2; color: #fff; }

        .table-wrapper {
            background: #fff; border: 1px solid #E0F2FE;
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 2px 12px rgba(22,151,194,0.08);
        }
        .table-header {
            padding: 18px 24px; border-bottom: 1px solid #E0F2FE;
            display: flex; align-items: center; justify-content: space-between;
            background: #fff;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 12px 16px; text-align: left; font-size: 11px;
            font-weight: 700; color: #64748B; text-transform: uppercase;
            letter-spacing: 0.5px; background: #F0F9FF;
            border-bottom: 1px solid #E0F2FE;
        }
        tbody tr { border-top: 1px solid #F0F9FF; transition: background 0.15s; }
        tbody tr:hover { background: #F0F9FF; }
        tbody td { padding: 14px 16px; font-size: 13px; color: #334155; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-green  { background: #D1FAE5; color: #065F46; }
        .badge-red    { background: #FEE2E2; color: #991B1B; }
        .badge-yellow { background: #FEF3C7; color: #92400E; }
        .badge-gray   { background: #F1F5F9; color: #475569; }
        .badge-blue   { background: #E0F2FE; color: #0369A1; }

        .ssl-ok     { color: #059669; font-weight: 700; }
        .ssl-warn   { color: #D97706; font-weight: 700; }
        .ssl-danger { color: #DC2626; font-weight: 700; }

        .alert-success {
            background: #D1FAE5; border: 1px solid #6EE7B7;
            color: #065F46; padding: 12px 16px; border-radius: 10px;
            margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
            font-weight: 600;
        }
        .alert-error {
            background: #FEE2E2; border: 1px solid #FCA5A5;
            color: #991B1B; padding: 12px 16px; border-radius: 10px;
            margin-bottom: 20px; font-weight: 600;
        }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .form-input {
            width: 100%; background: #F8FAFC; border: 1.5px solid #CBD5E1;
            border-radius: 9px; padding: 10px 14px; color: #0F172A;
            font-size: 14px; outline: none; transition: border-color 0.2s;
        }
        .form-input:focus { border-color: #1697C2; background: #fff; box-shadow: 0 0 0 3px rgba(22,151,194,0.1); }
        .form-error { color: #DC2626; font-size: 12px; margin-top: 4px; font-weight: 600; }

        .live-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #FEE2E2; color: #DC2626;
            padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;
        }
        .live-dot {
            width: 7px; height: 7px; background: #DC2626;
            border-radius: 50%; animation: pulse-red 1s infinite;
        }

        .uptime-bar { height: 6px; background: #E0F2FE; border-radius: 3px; overflow: hidden; }
        .uptime-bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #1697C2, #53EAFD); }

        /* Chatbot bubble */
        .chat-bubble {
            position: fixed; bottom: 24px; right: 24px;
            width: 62px; height: 62px;
            background: linear-gradient(135deg, #0C3547, #1697C2);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; cursor: pointer; z-index: 9999;
            box-shadow: 0 4px 24px rgba(22,151,194,0.6);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
        }
        .chat-bubble:hover {
            transform: scale(1.12);
            box-shadow: 0 8px 32px rgba(22,151,194,0.7);
        }
        .chat-bubble span { font-size: 28px; }

        /* Tooltip chatbot */
        .chat-tooltip {
            position: fixed; bottom: 94px; right: 16px;
            background: #0C3547; color: #fff;
            padding: 10px 16px; border-radius: 12px;
            font-size: 12px; font-weight: 600; z-index: 9998;
            white-space: nowrap; pointer-events: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            animation: fadeInTooltip 0.3s ease;
        }
        .chat-tooltip::after {
            content: ''; position: absolute; bottom: -6px; right: 24px;
            width: 12px; height: 12px; background: #0C3547;
            transform: rotate(45deg);
        }
        .chat-tooltip-sub {
            font-size: 10px; color: rgba(255,255,255,0.65);
            margin-top: 2px; font-weight: 400;
        }
        @keyframes fadeInTooltip {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">📡</div>
        <div>
            <span>MonitorPro</span>
            <small>Soft Seven Art</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="{{ route('sites.index') }}"
           class="nav-item {{ request()->routeIs('sites.*') ? 'active' : '' }}">
            <i class="fas fa-globe"></i> Sites surveillés
        </a>

        <div class="nav-section" style="margin-top:12px;">Rapports</div>
        <a href="{{ route('rapports.index') }}"
           class="nav-item {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
            <i class="fas fa-file-pdf"></i> Rapports PDF
        </a>
        <a href="{{ route('incidents.index') }}"
           class="nav-item {{ request()->routeIs('incidents.*') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> Incidents
        </a>

        <div class="nav-section" style="margin-top:12px;">Intelligence IA</div>
        <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
           target="_blank"
           class="nav-item">
            <i class="fas fa-robot"></i> Assistant IA
        </a>

        <div class="nav-section" style="margin-top:12px;">Compte</div>
        <a href="{{ route('profile.edit') }}" class="nav-item">
            <i class="fas fa-user-circle"></i> Profil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item"
                    style="width:100%; background:none; border:none; cursor:pointer; text-align:left;">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </form>
    </nav>

    <div class="sidebar-footer">
        <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div class="user-info">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">Administrateur</div>
        </div>
    </div>
</div>

<!-- MAIN -->
<div class="main">
    <div class="topbar">
        <div>
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="subtitle">@yield('subtitle', 'Vue d\'ensemble du système')</div>
        </div>
        <div class="topbar-right">
            <a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
               target="_blank"
               style="display:inline-flex; align-items:center; gap:6px;
                      background:linear-gradient(135deg,#0C3547,#1697C2);
                      color:#fff; padding:7px 14px; border-radius:20px;
                      font-size:12px; font-weight:700; text-decoration:none;
                      box-shadow:0 2px 10px rgba(22,151,194,0.3); transition:all 0.2s;"
               onmouseover="this.style.opacity='0.85'"
               onmouseout="this.style.opacity='1'">
                <i class="fas fa-robot"></i> Assistant IA
            </a>
            <span class="live-badge"><span class="live-dot"></span> LIVE</span>
            <span style="font-size:12px; color:#64748B; font-weight:600;">
                {{ now()->format('d/m/Y H:i:s') }}
            </span>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Chatbot bubble flottant -->
<div id="chat-tooltip" class="chat-tooltip" style="display:none;">
    🤖 MonitorPro Assistant IA
    <div class="chat-tooltip-sub">Powered by Google Gemini</div>
</div>

<a href="https://abaloudjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat"
   target="_blank"
   class="chat-bubble"
   onmouseover="document.getElementById('chat-tooltip').style.display='block'"
   onmouseout="document.getElementById('chat-tooltip').style.display='none'"
   title="MonitorPro Assistant IA">
    <span>🤖</span>
</a>

</body>
</html>