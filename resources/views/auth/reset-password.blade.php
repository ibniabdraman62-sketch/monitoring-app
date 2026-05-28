<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe — MonitorPro</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-soft7art.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#FBF8F0 0%,#F5EFE0 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; -webkit-font-smoothing:antialiased; }
        .auth-container { display:grid; grid-template-columns:1fr 460px; width:100%; max-width:960px; min-height:580px; background:#FFFFFF; border-radius:16px; overflow:hidden; box-shadow:0 20px 50px rgba(61,47,31,0.12); border:1px solid #E8DFC9; }

        .left-panel { background:linear-gradient(150deg,#2C5F8B 0%,#4078A9 100%); color:#FFFFFF; padding:48px 44px; display:flex; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden; }
        .left-panel::before { content:''; position:absolute; top:-100px; right:-100px; width:300px; height:300px; background:rgba(255,255,255,0.06); border-radius:50%; }

        .brand { display:flex; align-items:center; gap:12px; position:relative; }
        .brand img { width:42px; height:42px; background:rgba(255,255,255,0.15); border-radius:10px; padding:2px; }
        .brand-text { line-height:1.2; }
        .brand-title { font-size:18px; font-weight:700; }
        .brand-subtitle { font-size:11px; opacity:0.7; text-transform:uppercase; letter-spacing:0.6px; font-weight:500; }

        .left-content { position:relative; }
        .left-headline { font-size:28px; font-weight:700; line-height:1.25; margin-bottom:12px; }
        .left-text { font-size:14px; line-height:1.7; opacity:0.85; margin-bottom:24px; }

        .info-box { background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.18); border-radius:10px; padding:14px 16px; font-size:13px; line-height:1.6; margin-bottom:24px; }

        .rules { display:flex; flex-direction:column; gap:10px; }
        .rule { display:flex; align-items:center; gap:10px; font-size:13px; opacity:0.9; }
        .rule i { width:18px; height:18px; background:rgba(255,255,255,0.18); border-radius:4px; display:inline-flex; align-items:center; justify-content:center; font-size:9px; }

        .left-footer { position:relative; font-size:11.5px; opacity:0.7; }

        .right-panel { padding:48px; display:flex; flex-direction:column; justify-content:center; background:#FFFFFF; }
        .form-title { font-size:22px; font-weight:700; color:#3D2F1F; margin-bottom:6px; }
        .form-subtitle { font-size:13.5px; color:#8B7855; margin-bottom:24px; }

        .alert-error { background:#F2DCD8; color:#B66258; border:1px solid #E5BAB3; padding:10px 14px; border-radius:8px; font-size:12.5px; margin-bottom:18px; }

        .form-group { margin-bottom:16px; }
        .form-label { display:block; font-size:12px; font-weight:600; color:#5C4B36; margin-bottom:7px; }
        .input-group { position:relative; }
        .input-group i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#B5A684; }
        .form-input { width:100%; padding:11px 14px 11px 40px; border:1px solid #D9CDB0; border-radius:8px; font-size:14px; outline:none; transition:border-color .15s,box-shadow .15s; font-family:inherit; color:#3D2F1F; }
        .form-input:focus { border-color:#5B95C4; box-shadow:0 0 0 3px rgba(91,149,196,0.12); }
        .form-input.readonly { background:#F5EFE0; cursor:not-allowed; color:#8B7855; }

        .btn-submit { width:100%; padding:12px; background:#5B95C4; color:#FFFFFF; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:background .15s; font-family:inherit; margin-top:6px; }
        .btn-submit:hover { background:#4078A9; }

        .back-link { display:flex; justify-content:center; align-items:center; gap:6px; margin-top:18px; font-size:13px; color:#5B95C4; text-decoration:none; font-weight:600; }
        .back-link:hover { text-decoration:underline; }

        .form-footer { text-align:center; margin-top:24px; font-size:11.5px; color:#B5A684; }

        @media (max-width:900px) { .auth-container { grid-template-columns:1fr; max-width:460px; } .left-panel { display:none; } }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="left-panel">
        <div class="brand">
            <img src="{{ asset('images/logo-soft7art.svg') }}" alt="Soft7Art">
            <div class="brand-text">
                <div class="brand-title">MonitorPro</div>
                <div class="brand-subtitle">Soft Seven Art</div>
            </div>
        </div>

        <div class="left-content">
            <h1 class="left-headline">Définition du<br>nouveau mot de passe</h1>
            <p class="left-text">Choisissez un mot de passe fort et unique pour sécuriser votre compte MonitorPro.</p>

            <div class="info-box">
                <i class="fas fa-shield-alt"></i>
                <strong>Recommandation :</strong> utilisez un mot de passe unique que vous n'utilisez sur aucun autre service.
            </div>

            <div class="rules">
                <div class="rule"><i class="fas fa-check"></i> Au moins 8 caractères</div>
                <div class="rule"><i class="fas fa-check"></i> Combinez lettres, chiffres et symboles</div>
                <div class="rule"><i class="fas fa-check"></i> Confirmation obligatoire</div>
            </div>
        </div>

        <div class="left-footer">© 2026 Soft Seven Art — Casablanca, Maroc</div>
    </div>

    <div class="right-panel">
        <h2 class="form-title">Nouveau mot de passe</h2>
        <p class="form-subtitle">Définissez votre nouveau mot de passe ci-dessous</p>

        @if ($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                @foreach ($errors->all() as $error){{ $error }}<br>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-input readonly"
                        value="{{ old('email', $request->email) }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nouveau mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-input"
                        placeholder="Minimum 8 caractères" required autofocus autocomplete="new-password">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_confirmation" class="form-input"
                        placeholder="Retapez le mot de passe" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-submit">Définir le nouveau mot de passe</button>
        </form>

        <a href="{{ route('login') }}" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
        <div class="form-footer">MonitorPro © 2026 — Tous droits réservés</div>
    </div>
</div>

</body>
</html>