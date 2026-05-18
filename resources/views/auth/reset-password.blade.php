<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonitorPro — Réinitialiser le mot de passe</title>
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
            min-height: 560px;
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
            display: flex; align-items: center; gap: 14px;
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
            font-size: 13px; opacity: 0.85; line-height: 1.7;
            margin-bottom: 28px;
        }

        .info-box {
            background: rgba(83,234,253,0.15);
            border: 1px solid rgba(83,234,253,0.3);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 12px;
            line-height: 1.6;
        }

        .info-box i { color: #53EAFD; margin-right: 6px; }

        .rules { margin-top: 24px; display: flex; flex-direction: column; gap: 10px; }
        .rule  { display: flex; align-items: center; gap: 10px; font-size: 12px; opacity: 0.85; }
        .rule i { color: #6EE7B7; }

        /* ── Panneau droit ── */
        .right-panel {
            width: 400px;
            background: #fff;
            padding: 40px;
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
            margin-bottom: 24px; line-height: 1.5;
        }

        .alert-error {
            background: #FEF2F2; border: 1px solid #FCA5A5;
            color: #DC2626; padding: 10px 14px;
            border-radius: 10px; font-size: 12px;
            margin-bottom: 16px;
        }

        .form-group { margin-bottom: 16px; }

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

        .form-input.readonly {
            background: #F8FAFC;
            cursor: not-allowed;
        }

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
            margin-top: 6px;
        }

        .btn-submit:hover { opacity: 0.9; }

        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 16px;
            font-size: 13px; color: #1697C2;
            text-decoration: none; font-weight: 600;
        }

        .back-link:hover { text-decoration: underline; }

        .footer-text {
            text-align: center; font-size: 11px;
            color: #94A3B8; margin-top: 24px;
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

            <div class="left-title">Nouveau mot<br>de passe</div>
            <div class="left-desc">
                Vous êtes presque prêt ! Choisissez un nouveau mot de passe sécurisé pour votre compte MonitorPro.
            </div>

            <div class="info-box">
                <i class="fas fa-shield-alt"></i>
                <strong>Conseil de sécurité :</strong> utilisez un mot de passe unique et fort que vous n'utilisez pas ailleurs.
            </div>

            <div class="rules">
                <div class="rule"><i class="fas fa-check-circle"></i> Au moins 8 caractères</div>
                <div class="rule"><i class="fas fa-check-circle"></i> Mélange de lettres, chiffres et symboles</div>
                <div class="rule"><i class="fas fa-check-circle"></i> Confirmation obligatoire</div>
            </div>
        </div>

        <!-- ── Panneau droit ── -->
        <div class="right-panel">
            <div class="form-title">Réinitialisation</div>
            <div class="form-subtitle">
                Définissez votre nouveau mot de passe ci-dessous.
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope" style="color:#1697C2;"></i> Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input readonly"
                        value="{{ old('email', $request->email) }}"
                        readonly>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock" style="color:#1697C2;"></i> Nouveau mot de passe
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Min. 8 caractères"
                        required
                        autofocus
                        autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">
                        <i class="fas fa-lock" style="color:#1697C2;"></i> Confirmer le mot de passe
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Retapez le mot de passe"
                        required
                        autocomplete="new-password">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-check-circle" style="margin-right:6px;"></i>
                    Réinitialiser le mot de passe
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