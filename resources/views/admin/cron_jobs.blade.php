@extends('layouts.monitoring')
@section('title', 'Supervision Cron Jobs')
@section('subtitle', 'Tableau de bord Super Admin — 5 Cron Jobs actifs')

@section('content')

@if(!auth()->user()->isSuperAdmin())
    <div class="alert-error">Accès réservé au Super Admin.</div>
@else

<div style="display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin-bottom:24px;">
    @foreach([
        ['monitor:check-uptime',        'Uptime',         'Toutes les 5 min',  'blue',   'fa-heartbeat'],
        ['monitor:check-ssl',           'SSL',            'Toutes les heures', 'green',  'fa-lock'],
        ['monitor:check-whois',         'WHOIS Domaine',  'Chaque semaine',    'purple', 'fa-globe'],
        ['monitor:send-weekly-report',  'Rapport Hebdo',  'Lundi 08h00',       'yellow', 'fa-file-pdf'],
        ['monitor:cleanup',             'Nettoyage BDD',  'Chaque jour 00h',   'red',    'fa-trash'],
    ] as $job)
    <div class="card" style="text-align:center; border-top:3px solid
        {{ $job[3] == 'blue' ? '#1697C2' : ($job[3] == 'green' ? '#10B981' : ($job[3] == 'purple' ? '#7C3AED' : ($job[3] == 'yellow' ? '#D97706' : '#EF4444'))) }}">
        <i class="fas {{ $job[4] }}" style="font-size:28px; color:
            {{ $job[3] == 'blue' ? '#1697C2' : ($job[3] == 'green' ? '#10B981' : ($job[3] == 'purple' ? '#7C3AED' : ($job[3] == 'yellow' ? '#D97706' : '#EF4444'))) }};
            margin-bottom:10px; display:block;"></i>
        <div style="font-size:13px; font-weight:800; color:#0C3547; margin-bottom:4px;">{{ $job[1] }}</div>
        <div style="font-size:11px; color:#64748B; margin-bottom:8px;">{{ $job[2] }}</div>
        <span class="badge badge-green">● ACTIF</span>
        <div style="font-size:10px; color:#94A3B8; margin-top:8px; font-family:monospace;">{{ $job[0] }}</div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-title">
        <i class="fas fa-terminal" style="color:#1697C2;"></i>
        Lancer manuellement un Cron Job
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        @foreach([
            ['monitor:check-uptime',       'Vérifier uptime',    'btn-primary'],
            ['monitor:check-ssl',          'Vérifier SSL',       'btn-success'],
            ['monitor:check-whois',        'Vérifier WHOIS',     'btn-warning'],
            ['monitor:send-weekly-report', 'Rapport hebdo',      'btn-primary'],
            ['monitor:cleanup',            'Nettoyage BDD',      'btn-danger'],
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

@endif
@endsection