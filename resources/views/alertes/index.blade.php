@extends('layouts.monitoring')
@section('title', 'Historique des Alertes')
@section('subtitle', 'Toutes les alertes email envoyées')

@section('content')

@php
    $query = App\Models\Alerte::with(['incident.site'])
        ->orderBy('sent_at','desc');

    if(request('site_id'))   $query->whereHas('incident', fn($q) => $q->where('site_id', request('site_id')));
    if(request('type'))      $query->where('type', request('type'));

    $alertes = $query->paginate(20);
    $sites = App\Models\Site::where('user_id', auth()->id())->get();

    $stats = [
        'total'    => App\Models\Alerte::count(),
        'down'     => App\Models\Alerte::where('type','down')->count(),
        'slow'     => App\Models\Alerte::where('type','slow')->count(),
        'resolved' => App\Models\Alerte::where('type','resolved')->count(),
    ];
@endphp

<!-- KPIs -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="card" style="text-align:center; border-top:3px solid #1697C2;">
        <div style="font-size:32px; font-weight:900; color:#1697C2;">{{ $stats['total'] }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">TOTAL ALERTES</div>
    </div>
    <div class="card" style="text-align:center; border-top:3px solid #EF4444;">
        <div style="font-size:32px; font-weight:900; color:#DC2626;">{{ $stats['down'] }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">PANNES</div>
    </div>
    <div class="card" style="text-align:center; border-top:3px solid #D97706;">
        <div style="font-size:32px; font-weight:900; color:#D97706;">{{ $stats['slow'] }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">LENTEURS</div>
    </div>
    <div class="card" style="text-align:center; border-top:3px solid #10B981;">
        <div style="font-size:32px; font-weight:900; color:#059669;">{{ $stats['resolved'] }}</div>
        <div style="font-size:11px; color:#64748B; font-weight:700; margin-top:4px;">RÉSOLUTIONS</div>
    </div>
</div>

<!-- Filtres -->
<div class="card" style="margin-bottom:16px; padding:16px 24px;">
    <form method="GET" action="{{ route('alertes.index') }}"
          style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
          <div style="position:relative; flex:1;">
    <i class="fas fa-search" style="position:absolute; left:12px; top:50%;
       transform:translateY(-50%); color:#94A3B8;"></i>
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Rechercher par site ou URL..."
           class="form-input" style="padding-left:36px;">
</div>
        <select name="site_id" class="form-input" style="width:200px;">
            <option value="">Tous les sites</option>
            @foreach($sites as $site)
            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                {{ $site->client_name }}
            </option>
            @endforeach
        </select>
        <select name="type" class="form-input" style="width:180px;">
            <option value="">Tous les types</option>
            <option value="down"     {{ request('type') === 'down'     ? 'selected' : '' }}>Panne</option>
            <option value="slow"     {{ request('type') === 'slow'     ? 'selected' : '' }}>Lenteur</option>
            <option value="resolved" {{ request('type') === 'resolved' ? 'selected' : '' }}>Résolution</option>
            <option value="ssl"      {{ request('type') === 'ssl'      ? 'selected' : '' }}>SSL</option>
            <option value="domain"   {{ request('type') === 'domain'   ? 'selected' : '' }}>Domaine</option>
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
        <a href="{{ route('alertes.index') }}" class="btn-primary btn-danger"><i class="fas fa-times"></i> Effacer</a>
        <input type="date" name="date_from" class="form-input" style="width:160px;"
       value="{{ request('date_from') }}" placeholder="Du">
<input type="date" name="date_to" class="form-input" style="width:160px;"
       value="{{ request('date_to') }}" placeholder="Au">
    </form>
</div>

<!-- Tableau alertes -->
<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:700; color:#0C3547;">
            <i class="fas fa-bell" style="color:#1697C2;"></i> Alertes envoyées
        </div>
        <span class="badge badge-blue">{{ $alertes->total() }} alertes</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Site</th>
                <th>Email destinataire</th>
                <th>Envoyée le</th>
            </tr>
        </thead>
        <tbody>
        @forelse($alertes as $alerte)
        <tr>
            <td>
                @php
                    $colors = ['down'=>'badge-red','slow'=>'badge-yellow','resolved'=>'badge-green','ssl'=>'badge-blue','domain'=>'badge-yellow'];
                    $labels = ['down'=>'🔴 PANNE','slow'=>'🟡 LENTEUR','resolved'=>'🟢 RÉSOLU','ssl'=>'🔒 SSL','domain'=>'🌐 DOMAINE'];
                @endphp
                <span class="badge {{ $colors[$alerte->type] ?? 'badge-gray' }}">
                    {{ $labels[$alerte->type] ?? strtoupper($alerte->type) }}
                </span>
            </td>
            <td style="font-weight:700; color:#0C3547;">
                {{ $alerte->incident->site->client_name ?? '—' }}
            </td>
            <td style="font-size:12px; color:#64748B;">{{ $alerte->email_to }}</td>
            <td style="font-size:11px; color:#64748B;">
                {{ \Carbon\Carbon::parse($alerte->sent_at)->format('d/m/Y à H:i:s') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align:center; padding:40px; color:#64748B;">
                Aucune alerte envoyée pour le moment.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    @if($alertes->hasPages())
    <div style="padding:16px; display:flex; justify-content:center;">
        {{ $alertes->links() }}
    </div>
    @endif
</div>
@endsection