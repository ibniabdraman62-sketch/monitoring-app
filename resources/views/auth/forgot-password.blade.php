<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonitorPro — Mot de passe oublié</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0C3547;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            display: flex;
            width: 900px;
            min-height: 520px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        }

        /* ── Panneau gauche ── */
        .left-panel {
            flex: 1;
            background: linear-gradient(145deg, #0C3547 0%, #1697C2 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.15);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .logo-text { font-size: 26px; font-weight: 900; letter-spacing: -0.5px; }
        .logo-sub  { font-size: 11px; opacity: 0.7; margin-top: 2px; letter-spacing: 1px; text-transform: uppercase; }

        .left-title {
            font-size: 22px; font-weight: 800;
            margin-bottom: 12px; line-height: 1.3;
        }

        .left-desc {
            font-size: 13px; opacity: 0.8; line-height: 1.7;
            margin-bottom: 32px;
        }

        .steps { display: flex; flex-direction: column; gap: 14px; }

        .step {
            display: flex; align-items: flex-start; gap: 12px;
            background: rgba(255,255,255,0.08);
            padding: 12px 16px; border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.12);
        }

        .step-num {
            width: 26px; height: 26px; min-width: 26px;
            background: rgba(83,234,253,0.25);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800;
            color: #53EAFD;
        }

        .step-text { font-size: 12px; opacity: 0.85; line-height: 1.5; }

        /* ── Panneau droit ── */
        .right-panel {
            width: 400px;
            background: #fff;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 22px; font-weight: 800;
            color: #0C3547; margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 13px; color: #64748B;
            margin-bottom: 28px; line-height: 1.5;
        }

        .alert-success {
            background: #ECFDF5; border: 1px solid #6EE7B7;
            color: #065F46; padding: 12px 16px;
            border-radius: 10px; font-size: 13px;
            margin-bottom: 20px; display: flex; gap: 8px; align-items: flex-start;
        }

        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block; font-size: 12px;
            font-weight: 700; color: #374151;
            margin-bottom: 6px; text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%; padding: 11px 14px;
            border: 2px solid #E5E7EB;
            border-radius: 10px; font-size: 14px;
            color: #0C3547; outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus { border-color: #1697C2; }

        .form-error {
            font-size: 11px; color: #DC2626;
            margin-top: 4px;
        }

        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #1697C2, #53EAFD);
            color: #0C3547; font-size: 14px;
            font-weight: 800; border: none;
            border-radius: 10px; cursor: pointer;
            transition: opacity 0.2s; letter-spacing: 0.3px;
        }

        .btn-submit:hover { opacity: 0.9; }

        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 20px;
            font-size: 13px; color: #1697C2;
            text-decoration: none; font-weight: 600;
        }

        .back-link:hover { text-decoration: underline; }

        .footer-text {
            text-align: center; font-size: 11px;
            color: #94A3B8; margin-top: 28px;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- ── Panneau gauche ── -->
        <div class="left-panel">
            <div class="logo-area">
                <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="logo-text">MonitorPro</div>
                    <div class="logo-sub">Soft Seven Art</div>
                </div>
            </div>

            <div class="left-title">Réinitialisation du<br>mot de passe</div>
            <div class="left-desc">
                Suivez ces étapes simples pour récupérer l'accès à votre compte MonitorPro.
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-text">Entrez votre adresse email associée au compte</div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-text">Recevez un lien sécurisé par email</div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-text">Choisissez un nouveau mot de passe et reconnectez-vous</div>
                </div>
            </div>
        </div>

        <!-- ── Panneau droit ── -->
        <div class="right-panel">
            <div class="form-title">Mot de passe oublié ?</div>
            <div class="form-subtitle">
                Pas de problème. Entrez votre email et nous vous enverrons un lien de réinitialisation.
            </div>

            @if (session('status'))
                <div class="alert-success">
                    <i class="fas fa-check-circle" style="margin-top:2px;"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope" style="color:#1697C2; margin-right:4px;"></i>
                        Adresse email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email') }}"
                        placeholder="admin@softseven.ma"
                        required
                        autofocus>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane" style="margin-right:8px;"></i>
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>

            <div class="footer-text">
                MonitorPro © 2026 — Soft Seven Art — Casablanca
            </div>
        </div>

    </div>
</body>
</html>