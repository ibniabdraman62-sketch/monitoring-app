

@php
    $lastLog = App\Models\CronLog::latest('executed_at')->first();
    $schedulerActif = $lastLog && $lastLog->executed_at->diffInMinutes(now()) < 10;
@endphp
<div class="card" style="margin-bottom:24px; display:flex; align-items:center; gap:16px;
     background:{{ $schedulerActif ? 'linear-gradient(135deg,#D1FAE5,#A7F3D0)' : 'linear-gradient(135deg,#FEE2E2,#FECACA)' }};
     border:1px solid {{ $schedulerActif ? '#6EE7B7' : '#FCA5A5' }};">
    <div style="width:48px; height:48px; border-radius:50%;
                background:{{ $schedulerActif ? '#059669' : '#DC2626' }};
                display:flex; align-items:center; justify-content:center;">
        <i class="fas fa-{{ $schedulerActif ? 'check' : 'times' }}" style="color:#fff; font-size:20px;"></i>
    </div>
    <div>
        <div style="font-size:15px; font-weight:800;
                    color:{{ $schedulerActif ? '#065F46' : '#991B1B' }}">
            Scheduler Laravel : {{ $schedulerActif ? '✅ ACTIF' : '⚠️ INACTIF' }}
        </div>
        <div style="font-size:12px; color:#64748B;">
            @if($lastLog)
                Dernière exécution : {{ $lastLog->executed_at->diffForHumans() }} ({{ $lastLog->command }})
            @else
                Aucune exécution enregistrée — lancez "php artisan schedule:work" dans un terminal
            @endif
        </div>
    </div>
</div>

@extends('layouts.monitoring')
@section('title', 'Supervision Cron Jobs')
@section('subtitle', 'Tableau de bord Super Admin — 5 Cron Jobs actifs')

@section('content')
@php
    $logs = App\Models\CronLog::orderBy('executed_at','desc')->take(50)->get();
    $lastRuns = [];
    foreach(['monitor:check-uptime','monitor:check-ssl','monitor:check-whois','monitor:send-weekly-report','monitor:cleanup'] as $cmd) {
        $lastRuns[$cmd] = App\Models\CronLog::where('command',$cmd)->latest('executed_at')->first();
    }
@endphp

<!-- 5 cartes Cron Jobs -->
<div style="display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin-bottom:24px;">
@foreach([
    ['monitor:check-uptime','Uptime','Toutes les 5 min','#1697C2','fa-heartbeat'],
    ['monitor:check-ssl','SSL','Toutes les heures','#10B981','fa-lock'],
    ['monitor:check-whois','WHOIS Domaine','Chaque semaine','#7C3AED','fa-globe'],
    ['monitor:send-weekly-report','Rapport Hebdo','Lundi 08h00','#D97706','fa-file-pdf'],
    ['monitor:cleanup','Nettoyage BDD','Chaque jour 00h','#EF4444','fa-trash'],
] as $job)
@php $last = $lastRuns[$job[0]] ?? null; @endphp
<div class="card" style="text-align:center; border-top:3px solid {{ $job[3] }}; padding:16px;">
    <i class="fas {{ $job[4] }}" style="font-size:26px; color:{{ $job[3] }}; margin-bottom:8px; display:block;"></i>
    <div style="font-size:13px; font-weight:800; color:#0C3547; margin-bottom:4px;">{{ $job[1] }}</div>
    <div style="font-size:11px; color:#64748B; margin-bottom:8px;">{{ $job[2] }}</div>
    <span class="badge {{ $last && $last->status === 'error' ? 'badge-red' : 'badge-green' }}">
        ● {{ $last ? strtoupper($last->status) : 'EN ATTENTE' }}
    </span>
    @if($last)
    <div style="font-size:10px; color:#94A3B8; margin-top:6px;">
        {{ $last->executed_at->diffForHumans() }}<br>
        {{ $last->duration_ms }}ms — {{ $last->sites_checked }} sites
    </div>
    @endif
    <div style="font-size:10px; color:#94A3B8; margin-top:4px; font-family:monospace;">{{ $job[0] }}</div>
</div>
@endforeach
</div>

<!-- Boutons lancement manuel -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title"><i class="fas fa-terminal" style="color:#1697C2;"></i> Lancer manuellement un Cron Job</div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
    @foreach([
        ['monitor:check-uptime','Vérifier uptime','btn-primary'],
        ['monitor:check-ssl','Vérifier SSL','btn-success'],
        ['monitor:check-whois','Vérifier WHOIS','btn-warning'],
        ['monitor:send-weekly-report','Rapport hebdo','btn-primary'],
        ['monitor:cleanup','Nettoyage BDD','btn-danger'],
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

<!-- Historique des 50 dernières exécutions -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            <i class="fas fa-history" style="color:#1697C2;"></i> Historique des exécutions
        </div>
        <span class="badge badge-blue">{{ $logs->count() }} entrées</span>
    </div>
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
            <td style="font-family:monospace; font-size:11px; color:#1697C2;">
                {{ $log->command }}
            </td>
            <td>
                <span class="badge {{ $log->status === 'success' ? 'badge-green' : 'badge-red' }}">
                    {{ $log->status === 'success' ? '✅ Succès' : '❌ Erreur' }}
                </span>
            </td>
            <td style="font-weight:700; color:#0C3547;">{{ $log->duration_ms }} ms</td>
            <td style="text-align:center;">{{ $log->sites_checked }}</td>
            <td>
                @if($log->errors_count > 0)
                    <span class="badge badge-red">{{ $log->errors_count }}</span>
                @else
                    <span style="color:#10B981; font-weight:700;">0</span>
                @endif
            </td>
            <td style="font-size:11px; color:#64748B;">
                {{ $log->executed_at->format('d/m/Y H:i:s') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; padding:40px; color:#64748B;">
                Aucune exécution enregistrée. Lancez un Cron Job ci-dessus.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection