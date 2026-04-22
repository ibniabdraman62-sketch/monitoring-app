<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        .header { background: #1F3864; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0; font-size: 12px; opacity: 0.8; }
        .section { margin: 20px 0; }
        .section h2 { color: #1F3864; border-bottom: 2px solid #1F3864; padding-bottom: 5px; }
        .kpi-grid { display: flex; gap: 15px; margin: 15px 0; }
        .kpi { background: #EBF3FB; border-radius: 8px; padding: 15px; text-align: center; flex: 1; }
        .kpi .value { font-size: 28px; font-weight: bold; color: #1F3864; }
        .kpi .label { font-size: 11px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #2E75B6; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .badge-up { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 10px; }
        .badge-down { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 10px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <h1>Rapport de Monitoring</h1>
        <p>{{ $site->client_name }} — {{ $site->url }}</p>
        <p>Période : {{ $periodStart->format('d/m/Y') }} au {{ $periodEnd->format('d/m/Y') }}</p>
    </div>

    <!-- KPIs -->
    <div class="section">
        <h2>Résumé de la période</h2>
        <div class="kpi-grid">
            <div class="kpi">
                <div class="value">{{ $uptimePct }}%</div>
                <div class="label">Taux de disponibilité</div>
            </div>
            <div class="kpi">
                <div class="value">{{ round($avgResponse) }} ms</div>
                <div class="label">Temps de réponse moyen</div>
            </div>
            <div class="kpi">
                <div class="value">{{ $verifications->count() }}</div>
                <div class="label">Vérifications effectuées</div>
            </div>
            <div class="kpi">
                <div class="value">{{ $incidents->count() }}</div>
                <div class="label">Incidents détectés</div>
            </div>
        </div>
    </div>

    <!-- Incidents -->
    <div class="section">
        <h2>Incidents détectés</h2>
        @if($incidents->isEmpty())
            <p style="color: green;">✓ Aucun incident détecté durant cette période.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Résolu</th>
                    <th>Durée</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incidents as $incident)
                <tr>
                    <td><span class="badge-down">{{ strtoupper($incident->type) }}</span></td>
                    <td>{{ $incident->started_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $incident->resolved_at ? $incident->resolved_at->format('d/m/Y H:i') : 'En cours' }}</td>
                    <td>{{ $incident->duration_min ? $incident->duration_min . ' min' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Dernières vérifications -->
    <div class="section">
        <h2>Dernières vérifications (20)</h2>
        <table>
            <thead>
                <tr>
                    <th>Date / Heure</th>
                    <th>HTTP</th>
                    <th>Temps (ms)</th>
                    <th>SSL</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($verifications->take(20) as $v)
                <tr>
                    <td>{{ $v->checked_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $v->http_code }}</td>
                    <td>{{ $v->response_time_ms }}</td>
                    <td>{{ $v->ssl_valid ? '✓' : '✗' }}</td>
                    <td>
                        @if($v->is_up)
                            <span class="badge-up">EN LIGNE</span>
                        @else
                            <span class="badge-down">HORS LIGNE</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i:s') }} — Système de Monitoring Soft Seven Art</p>
    </div>

</body>
</html>