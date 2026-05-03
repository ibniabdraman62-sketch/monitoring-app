<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonitorPro — Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', 'Arial', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0C3547 0%, #0a2a38 40%, #1697C2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Cercles décoratifs en arrière-plan */
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: rgba(22,151,194,0.08);
            border-radius: 50%;
            top: -200px; right: -200px;
            animation: float 8s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(83,234,253,0.06);
            border-radius: 50%;
            bottom: -150px; left: -150px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.02); }
        }

        /* Points lumineux */
        .dots {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .dot {
            position: absolute;
            border-radius: 50%;
            background: rgba(83,234,253,0.15);
            animation: blink 3s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.3); }
        }

        .container {
            position: relative;
            z-index: 10;
            display: flex;
            gap: 0;
            width: 900px;
            max-width: 95vw;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.4);
        }

        /* Panel gauche — branding */
        .panel-left {
            width: 380px;
            flex-shrink: 0;
            background: linear-gradient(160deg, #0C3547, #0d4060);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(83,234,253,0.2);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
        }
        .logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #53EAFD, #1697C2);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 24px rgba(83,234,253,0.3);
        }
        .logo-text .name { font-size: 22px; font-weight: 900; color: #fff; }
        .logo-text .sub  { font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 2px; }

        .hero-title {
            font-size: 28px; font-weight: 800;
            color: #fff; line-height: 1.3;
            margin-bottom: 16px;
        }
        .hero-title span { color: #53EAFD; }

        .hero-desc {
            font-size: 13px; color: rgba(255,255,255,0.6);
            line-height: 1.8; margin-bottom: 36px;
        }

        .features { display: flex; flex-direction: column; gap: 14px; }
        .feature {
            display: flex; align-items: center; gap: 12px;
        }
        .feature-icon {
            width: 34px; height: 34px;
            background: rgba(22,151,194,0.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon i { font-size: 13px; color: #53EAFD; }
        .feature-text { font-size: 12px; color: rgba(255,255,255,0.7); font-weight: 500; }

        .footer-brand {
            font-size: 11px; color: rgba(255,255,255,0.3);
            margin-top: 32px;
        }

        /* Panel droit — formulaire */
        .panel-right {
            flex: 1;
            background: #fff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header { margin-bottom: 32px; }
        .form-header h2 {
            font-size: 24px; font-weight: 800; color: #0C3547;
            margin-bottom: 6px;
        }
        .form-header p { font-size: 13px; color: #64748B; }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #334155; margin-bottom: 8px;
        }

        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #94A3B8; font-size: 14px;
        }
        .form-input {
            width: 100%;
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 14px 12px 40px;
            font-size: 14px; color: #0F172A;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: #1697C2;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(22,151,194,0.12);
        }

        .remember-row {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #64748B; cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #1697C2;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 13px; color: #1697C2;
            text-decoration: none; font-weight: 600;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #0C3547, #1697C2);
            color: #fff;
            border: none; border-radius: 10px;
            padding: 14px;
            font-size: 15px; font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 8px 24px rgba(22,151,194,0.35);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 32px rgba(22,151,194,0.45);
        }
        .btn-login:active { transform: translateY(0); }

        .error-box {
            background: #FEE2E2; border: 1px solid #FCA5A5;
            color: #991B1B; padding: 12px 16px;
            border-radius: 10px; margin-bottom: 20px;
            font-size: 13px; font-weight: 600;
            display: flex; align-items: center; gap: 8px;
        }

        .divider {
            border: none; border-top: 1px solid #F1F5F9;
            margin: 28px 0;
        }

        .accounts-hint {
            background: #F0F9FF; border: 1px solid #BAE6FD;
            border-radius: 10px; padding: 14px 16px;
        }
        .accounts-hint .hint-title {
            font-size: 11px; font-weight: 700; color: #0369A1;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .account-row {
            display: flex; justify-content: space-between;
            font-size: 11px; color: #334155; margin-bottom: 4px;
        }
        .account-row .role {
            font-weight: 700; color: #0C3547;
            background: #E0F2FE; padding: 1px 8px;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .container { flex-direction: column; width: 95vw; }
            .panel-left { width: 100%; padding: 32px 28px; }
            .panel-right { padding: 32px 28px; }
            .features { display: none; }
        }
    </style>
</head>
<body>

<!-- Points décoratifs -->
<div class="dots">
    <div class="dot" style="width:8px;height:8px;top:15%;left:10%;animation-delay:0s;"></div>
    <div class="dot" style="width:5px;height:5px;top:35%;left:20%;animation-delay:1s;"></div>
    <div class="dot" style="width:6px;height:6px;top:65%;left:8%;animation-delay:2s;"></div>
    <div class="dot" style="width:4px;height:4px;top:80%;left:25%;animation-delay:0.5s;"></div>
    <div class="dot" style="width:7px;height:7px;top:20%;right:15%;animation-delay:1.5s;"></div>
    <div class="dot" style="width:5px;height:5px;top:50%;right:10%;animation-delay:2.5s;"></div>
    <div class="dot" style="width:9px;height:9px;top:75%;right:20%;animation-delay:0.8s;"></div>
</div>

<div class="container">

    <!-- Panel gauche — Branding -->
    <div class="panel-left">
        <div>
            <div class="logo-area">
                <div class="logo-icon">📡</div>
                <div class="logo-text">
                    <div class="name">MonitorPro</div>
                    <div class="sub">Soft Seven Art — Casablanca</div>
                </div>
            </div>

            <div class="hero-title">
                Surveillance<br>
                <span>intelligente</span><br>
                de vos sites web
            </div>

            <div class="hero-desc">
                Monitorer, alerter et analyser vos
                infrastructures web en temps reel,
                24h/24 et 7j/7.
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="feature-text">Verification uptime toutes les 5 minutes</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-bell"></i></div>
                    <div class="feature-text">Alertes email instantanees en cas de panne</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-robot"></i></div>
                    <div class="feature-text">Analyse intelligente Google Gemini AI</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-lock"></i></div>
                    <div class="feature-text">Surveillance SSL et expiration domaines</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="feature-text">Rapports PDF et historiques detailles</div>
                </div>
            </div>
        </div>

        <div class="footer-brand">
            © 2026 Soft Seven Art — FST Mohammedia
        </div>
    </div>

    <!-- Panel droit — Formulaire -->
    <div class="panel-right">

        <div class="form-header">
            <h2>Bienvenue 👋</h2>
            <p>Connectez-vous pour acceder a votre tableau de bord</p>
        </div>

        @if ($errors->any())
        <div class="error-box">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if (session('status'))
        <div style="background:#D1FAE5; border:1px solid #6EE7B7; color:#065F46;
                    padding:12px 16px; border-radius:10px; margin-bottom:20px;
                    font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-input"
                           value="{{ old('email') }}"
                           placeholder="admin@softseven.ma"
                           required autofocus autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-input"
                           placeholder="••••••••••"
                           required autocomplete="current-password"
                           id="password-input">
                    <i class="fas fa-eye" id="toggle-pwd"
                       onclick="togglePwd()"
                       style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                              color:#94A3B8; cursor:pointer; font-size:14px;"></i>
                </div>
            </div>

            <div class="remember-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">
                    Mot de passe oublié ?
                </a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Se connecter
            </button>
        </form>

        <hr class="divider">

        <div class="accounts-hint">
            <div class="hint-title">🔑 Comptes de demonstration</div>
            <div class="account-row">
                <span>admin@softseven.ma — SoftSeven@2026</span>
                <span class="role">Super Admin</span>
            </div>
            <div class="account-row">
                <span>agent@softseven.ma — Agent@2026</span>
                <span class="role">Agent</span>
            </div>
        </div>

    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('password-input');
    const icon  = document.getElementById('toggle-pwd');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

</body>
</html>