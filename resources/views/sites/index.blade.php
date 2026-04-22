@extends('layouts.monitoring')

@section('title', 'Sites Surveillés')
@section('subtitle', 'Gestion des sites à monitorer')

@section('content')

<div class="table-wrapper">
    <div class="table-header">
        <div style="font-size:15px; font-weight:600; color:#fff;">
            🌐 Sites surveillés
        </div>
        <a href="{{ route('sites.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Ajouter un site
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Statut</th>
                <th>Client</th>
                <th>URL</th>
                <th>Fréquence</th>
                <th>Seuil</th>
                <th>SSL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sites as $site)
            <tr>
                <td>
                    @if($site->is_active)
                        <span class="status-dot online"></span>
                    @else
                        <span class="status-dot offline"></span>
                    @endif
                </td>
                <td style="font-weight:600; color:#fff;">{{ $site->client_name }}</td>
                <td>
                    <a href="{{ $site->url }}" target="_blank"
                       style="color:#818CF8; font-size:12px; text-decoration:none;">
                        {{ $site->url }}
                    </a>
                </td>
                <td><span class="badge badge-blue">{{ $site->frequency_min }} min</span></td>
                <td><span style="color:#9CA3AF;">{{ $site->response_threshold_ms }} ms</span></td>
                <td>
                    @if($site->ssl_check)
                        <span class="ssl-ok">🔒 Oui</span>
                    @else
                        <span style="color:#6B7280;">Non</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <a href="{{ route('sites.show', $site) }}" class="btn-primary" style="padding:5px 10px; font-size:11px;">
                            <i class="fas fa-eye"></i> Détail
                        </a>
                        <a href="{{ route('sites.edit', $site) }}" class="btn-primary btn-warning" style="padding:5px 10px; font-size:11px;">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('sites.toggle', $site) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-primary {{ $site->is_active ? 'btn-danger' : 'btn-success' }}" style="padding:5px 10px; font-size:11px;">
                                <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }}"></i>
                                {{ $site->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                        <form action="{{ route('sites.destroy', $site) }}" method="POST"
                              onsubmit="return confirm('Supprimer ce site ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-primary btn-danger" style="padding:5px 10px; font-size:11px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:40px; color:#6B7280;">
                    Aucun site surveillé.
                    <a href="{{ route('sites.create') }}" style="color:#818CF8;">Ajouter un site</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection