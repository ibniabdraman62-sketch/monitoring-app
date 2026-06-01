@extends('layouts.monitoring')
@section('title', 'Supervision Cron Jobs')
@section('subtitle', 'Tableau de bord Super Admin — 5 Cron Jobs actifs')

@section('content')
@php
    $logs = App\Models\CronLog::orderBy('executed_at','desc')->take(100)->get();
    $lastRuns = [];
    foreach(['monitor:check-uptime','monitor:check-ssl','monitor:check-whois','monitor:send-weekly-report','monitor:cleanup'] as $cmd) {
        $lastRuns[$cmd] = App\Models\CronLog::where('command',$cmd)->latest('executed_at')->first();
    }
    $lastLog = App\Models\CronLog::latest('executed_at')->first();
    $schedulerActif = $lastLog && $lastLog->executed_at->diffInMinutes(now()) < 10;
@endphp

{{-- ═══ Indicateur Scheduler ═══ --}}
<div class="card mb-24" style="display:flex; align-items:center; gap:16px; padding:18px 22px;
            background:{{ $schedulerActif ? 'var(--success-bg)' : 'var(--danger-bg)' }};
            border:1px solid {{ $schedulerActif ? '#B0DBB6' : '#E5BAB3' }};">
    <div style="width:48px; height:48px; border-radius:50%; flex-shrink:0;
                background:{{ $schedulerActif ? 'var(--success)' : 'var(--danger)' }};
                display:flex; align-items:center; justify-content:center;">
        <i class="fas fa-{{ $schedulerActif ? 'check' : 'exclamation-triangle' }}" style="color:#FFFFFF; font-size:18px;"></i>
    </div>
    <div style="flex:1;">
        <div style="font-size:15px; font-weight:700;
                    color:{{ $schedulerActif ? 'var(--success)' : 'var(--danger)' }};">
            Scheduler Laravel : {{ $schedulerActif ? 'ACTIF' : 'INACTIF' }}
        </div>
        <div class="text-xs" style="color:var(--text-secondary); margin-top:3px;">
            @if($lastLog)
                Dernière exécution : {{ $lastLog->executed_at->diffForHumans() }} — <code style="font-family:monospace;">{{ $lastLog->command }}</code>
            @else
                Aucune exécution enregistrée — Lancez <code style="font-family:monospace;">php artisan schedule:work</code>
            @endif
        </div>
    </div>
    @if(!$schedulerActif)
    <div style="background:#FFFFFF; border:1px solid var(--border); padding:10px 14px; border-radius:8px;
                font-size:11.5px; font-weight:700; color:var(--text-secondary);">
        <i class="fas fa-terminal"></i> php artisan schedule:work
    </div>
    @endif
</div>

{{-- ═══ 5 CARDS SUR UNE SEULE LIGNE (comme avant) ═══ --}}
<div style="display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:24px;">
@foreach([
    ['monitor:check-uptime',       'Uptime',         'Toutes les 5 min',   'var(--primary)',  'fa-heartbeat'],
    ['monitor:check-ssl',          'SSL',            'Toutes les heures',  'var(--success)',  'fa-lock'],
    ['monitor:check-whois',        'WHOIS Domaine',  'Chaque semaine',     'var(--gold)',     'fa-globe'],
    ['monitor:send-weekly-report', 'Rapport Hebdo',  'Lundi 08h00',        'var(--warning)',  'fa-file-pdf'],
    ['monitor:cleanup',            'Nettoyage BDD',  'Chaque jour 00h',    'var(--danger)',   'fa-trash'],
] as $job)
    @php $last = $lastRuns[$job[0]] ?? null; @endphp
    <div class="card" style="text-align:center; border-top:3px solid {{ $job[3] }}; padding:14px 12px;">
        <i class="fas {{ $job[4] }}" style="font-size:24px; color:{{ $job[3] }}; margin-bottom:8px; display:block;"></i>
        <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:3px;">{{ $job[1] }}</div>
        <div class="text-xs text-muted" style="margin-bottom:8px;">{{ $job[2] }}</div>
        <span class="badge {{ $last ? ($last->status === 'error' ? 'badge-danger' : 'badge-success') : 'badge-neutral' }}">
            {{ $last ? ($last->status === 'error' ? 'Erreur' : 'Succès') : 'En attente' }}
        </span>
        @if($last)
            <div class="text-xs text-muted" style="margin-top:6px; line-height:1.4;">
                {{ $last->executed_at->diffForHumans() }}<br>
                <span class="font-mono" style="font-size:10px;">{{ $last->duration_ms }}ms · {{ $last->sites_checked }} sites</span>
            </div>
        @endif
        <div class="text-xs text-muted font-mono" style="margin-top:4px; font-size:9.5px;">{{ $job[0] }}</div>
    </div>
@endforeach
</div>

{{-- ═══ Boutons d'exécution manuelle ═══ --}}
<div class="card mb-24">
    <div class="card-title">
        <i class="fas fa-terminal" style="color:var(--primary);"></i>
        Exécution manuelle d'un Cron Job
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
    @foreach([
        ['monitor:check-uptime', 'Vérifier uptime', ''],
        ['monitor:check-ssl', 'Vérifier SSL', 'btn-success'],
        ['monitor:check-whois', 'Vérifier WHOIS', 'btn-gold'],
        ['monitor:send-weekly-report', 'Envoyer rapport hebdo', 'btn-warning'],
        ['monitor:cleanup', 'Nettoyer BDD', 'btn-danger'],
    ] as $cmd)
        <form method="POST" action="{{ route('cron.run') }}">
            @csrf
            <input type="hidden" name="command" value="{{ $cmd[0] }}">
            <button type="submit" class="btn-primary {{ $cmd[2] }}">
                <i class="fas fa-play"></i> {{ $cmd[1] }}
            </button>
        </form>
    @endforeach
    </div>
</div>

{{-- ═══ Historique d'exécution ═══ --}}
<div class="table-wrapper">
    <div class="table-header">
        <div class="card-title" style="margin:0;">
            <i class="fas fa-history" style="color:var(--primary);"></i>
            Historique des exécutions
        </div>
        <span class="badge badge-info">{{ $logs->count() }} entrées</span>
    </div>
    <div class="table-scroll" style="max-height:520px;">
        <table>
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Statut</th>
                    <th>Durée</th>
                    <th>Sites vérifiés</th>
                    <th>Erreurs</th>
                    <th>Exécutée le</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="font-mono text-xs" style="color:var(--primary);">{{ $log->command }}</td>
                    <td>
                        <span class="badge {{ $log->status === 'success' ? 'badge-success' : 'badge-danger' }}">
                            {{ $log->status === 'success' ? 'Succès' : 'Erreur' }}
                        </span>
                    </td>
                    <td class="font-mono" style="font-weight:600;">{{ $log->duration_ms }} ms</td>
                    <td class="font-mono" style="text-align:center;">{{ $log->sites_checked ?? 0 }}</td>
                    <td>
                        @if(($log->errors_count ?? 0) > 0)
                            <span class="badge badge-danger">{{ $log->errors_count }}</span>
                        @else
                            <span style="color:var(--success); font-weight:700;">0</span>
                        @endif
                    </td>
                    <td class="text-xs text-muted font-mono">{{ $log->executed_at->timezone('Africa/Casablanca')->format('d/m/Y H:i:s') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                    Aucune exécution enregistrée. Lancez un Cron Job ci-dessus.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection