<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport — {{ $site->client_name }}</title>
    <style>
        @page { margin: 25mm 20mm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #3D2F1F; font-size: 11px; line-height: 1.6; }

        .header { border-bottom: 3px solid #5B95C4; padding-bottom: 15px; margin-bottom: 25px; }
        .header-grid { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }

        .brand-name { font-size: 22px; font-weight: bold; color: #4078A9; margin-bottom: 4px; }
        .brand-sub { font-size: 10px; color: #8B7855; text-transform: uppercase; letter-spacing: 1px; }
        .doc-info { font-size: 10px; color: #8B7855; }
        .doc-title { font-size: 13px; font-weight: bold; color: #3D2F1F; margin-bottom: 2px; }

        .section-title {
            font-size: 13px; font-weight: bold; color: #4078A9;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin: 22px 0 10px; padding-bottom: 5px;
            border-bottom: 1px solid #E8DFC9;
        }

        .info-box { background: #FBF8F0; border: 1px solid #E8DFC9; border-radius: 6px; padding: 12px 15px; margin-bottom: 15px; }
        .info-row { display: table; width: 100%; margin-bottom: 6px; }
        .info-label { display: table-cell; color: #8B7855; width: 35%; font-size: 11px; }
        .info-value { display: table-cell; font-weight: bold; color: #3D2F1F; font-size: 11px; }

        .kpi-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 18px; }
        .kpi-cell { display: table-cell; border: 1px solid #E8DFC9; background: #FFFFFF; border-radius: 6px; padding: 14px; width: 25%; text-align: center; }
        .kpi-label-pdf { font-size: 9px; color: #8B7855; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .kpi-value-pdf { font-size: 22px; font-weight: bold; color: #3D2F1F; }
        .kpi-value-pdf.green  { color: #4A8C5A; }
        .kpi-value-pdf.orange { color: #C48A4A; }
        .kpi-value-pdf.red    { color: #B66258; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        thead { background: #4078A9; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #E8DFC9; font-size: 10px; }
        tbody tr:nth-child(even) { background: #FBF8F0; }

        .badge-pdf { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .b-success { background: #DFF0E1; color: #4A8C5A; }
        .b-warning { background: #F5E9D6; color: #C48A4A; }
        .b-danger  { background: #F2DCD8; color: #B66258; }
        .b-neutral { background: #F0E8D4; color: #5C4B36; }

        .footer { position: fixed; bottom: -15mm; left: 0; right: 0; text-align: center; font-size: 9px; color: #B5A684; border-top: 1px solid #E8DFC9; padding-top: 6px; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-grid">
        <div class="header-left">
            <div class="brand-name">MonitorPro</div>
            <div class="brand-sub">Soft Seven Art — Casablanca</div>
        </div>
        <div class="header-right doc-info">
            <div class="doc-title">Rapport de disponibilité</div>
            <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div>Réf. {{ strtoupper(uniqid()) }}</div>
        </div>
    </div>
</div>

<div class="section-title">Informations du site</div>
<div class="info-box">
    <div class="info-row"><div class="info-label">Client</div><div class="info-value">{{ $site->client_name }}</div></div>
    <div class="info-row"><div class="info-label">URL surveillée</div><div class="info-value">{{ $site->url }}</div></div>
    <div class="info-row"><div class="info-label">Période d'analyse</div>
        <div class="info-value">Du {{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }}</div>
    </div>
    <div class="info-row"><div class="info-label">Fréquence</div><div class="info-value">Toutes les {{ $site->frequency_min }} minutes</div></div>
    <div class="info-row"><div class="info-label">Seuil de réponse</div><div class="info-value">{{ $site->response_threshold_ms }} ms</div></div>
</div>

<div class="section-title">Indicateurs clés de performance</div>
<table class="kpi-grid">
    <tr>
        <td class="kpi-cell">
            <div class="kpi-label-pdf">Disponibilité</div>
            <div class="kpi-value-pdf {{ $uptimePct > 99 ? 'green' : ($uptimePct > 95 ? 'orange' : 'red') }}">{{ number_format($uptimePct, 2) }}%</div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-label-pdf">Vérifications</div>
            <div class="kpi-value-pdf">{{ $verifications->count() }}</div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-label-pdf">Temps moyen</div>
            <div class="kpi-value-pdf">{{ round($avgResponse) }} ms</div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-label-pdf">Incidents</div>
            <div class="kpi-value-pdf {{ $incidents->count() === 0 ? 'green' : 'red' }}">{{ $incidents->count() }}</div>
        </td>
    </tr>
</table>

@php
    $latestVerif = $verifications->first();
    $sslDays = $latestVerif->ssl_days_remaining ?? null;
    $sslExpiry = $latestVerif->ssl_expires_at ?? null;
@endphp

<div class="section-title">Certificat SSL et nom de domaine</div>
<table>
    <thead><tr><th>Élément</th><th>Statut</th><th>Jours restants</th><th>Expiration</th></tr></thead>
    <tbody>
        <tr>
            <td>Certificat SSL/TLS</td>
            <td>@if($sslDays !== null && $sslDays > 0)<span class="badge-pdf b-success">Valide</span>@else<span class="badge-pdf b-danger">Non disponible</span>@endif</td>
            <td>@if($sslDays !== null)<strong style="color:{{ $sslDays > 30 ? '#4A8C5A' : ($sslDays > 7 ? '#C48A4A' : '#B66258') }};">{{ $sslDays }} jours</strong>@else—@endif</td>
            <td>{{ $sslExpiry ? \Carbon\Carbon::parse($sslExpiry)->format('d/m/Y') : '—' }}</td>
        </tr>
        <tr>
            <td>Nom de domaine</td>
            @php $domDays = $site->domain_expires_at ? \Carbon\Carbon::parse($site->domain_expires_at)->diffInDays(now(), false) * -1 : null; @endphp
            <td>@if($domDays !== null && $domDays > 0)<span class="badge-pdf b-success">Actif</span>@else<span class="badge-pdf b-neutral">N/D</span>@endif</td>
            <td>@if($domDays !== null)<strong style="color:{{ $domDays > 60 ? '#4A8C5A' : ($domDays > 30 ? '#C48A4A' : '#B66258') }};">{{ $domDays }} jours</strong>@else—@endif</td>
            <td>{{ $site->domain_expires_at ? \Carbon\Carbon::parse($site->domain_expires_at)->format('d/m/Y') : '—' }}</td>
        </tr>
    </tbody>
</table>

@if($incidents->count() > 0)
<div class="section-title">Incidents enregistrés</div>
<table>
    <thead><tr><th>Date de début</th><th>Type</th><th>Durée</th><th>Statut</th></tr></thead>
    <tbody>
    @foreach($incidents as $inc)
        <tr>
            <td>{{ $inc->started_at->format('d/m/Y H:i') }}</td>
            <td>
                @if($inc->type === 'offline')<span class="badge-pdf b-danger">Hors ligne</span>
                @elseif($inc->type === 'slow')<span class="badge-pdf b-warning">Lenteur</span>
                @else<span class="badge-pdf b-neutral">{{ ucfirst($inc->type) }}</span>@endif
            </td>
            <td>{{ $inc->duration_min ? $inc->duration_min . ' min' : 'En cours' }}</td>
            <td>@if($inc->is_resolved)<span class="badge-pdf b-success">Résolu</span>@else<span class="badge-pdf b-danger">Actif</span>@endif</td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
<div class="section-title">Incidents enregistrés</div>
<div style="padding:18px; background:#DFF0E1; border:1px solid #B0DBB6; border-radius:6px; color:#4A8C5A; font-size:11px; text-align:center;">
    Aucun incident enregistré sur cette période. Le site a été stable.
</div>
@endif

<div class="footer">
    MonitorPro — Soft Seven Art — Casablanca, Maroc | Rapport confidentiel — usage interne
</div>

</body>
</html>