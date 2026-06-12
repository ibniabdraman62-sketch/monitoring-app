@extends('layouts.monitoring')
@section('title', 'Ajouter un site')
@section('subtitle', 'Configuration d\'un nouveau site à surveiller')

@section('content')

<a href="{{ route('sites.index') }}" class="btn-secondary btn-sm mb-24">
    <i class="fas fa-arrow-left"></i> Retour à la liste
</a>

<div >  
    <div class="card">
        <div class="card-title">
            <i class="fas fa-plus-circle" style="color:var(--primary);"></i>
            Nouveau site à surveiller
        </div>

        <form method="POST" action="{{ route('sites.store') }}">
            @csrf

            {{-- ═══ CLIENT PROPRIÉTAIRE ═══ --}}
            <div class="form-group">
                <label class="form-label">Client propriétaire (qui verra ce site) </label>
                <div style="display:flex; gap:8px;">
                    <select name="user_id" id="userSelect" class="form-select" required style="flex:1;">
                        <option value="{{ auth()->id() }}">Soft Seven Art (interne)</option>
                        @foreach(\App\Models\User::where('role', 'client')->where('is_active', true)->orderBy('name')->get() as $client)
                            <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    <button type="button" onclick="openClientModal()" class="btn-primary" style="white-space:nowrap;">
                        <i class="fas fa-user-plus"></i> Nouveau client
                    </button>
                </div>
                <div class="form-help">Le client choisi pourra voir ce site dans son tableau de bord.</div>
                @error('user_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            {{-- ═══ NOM CLIENT + EMAIL ═══ --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Nom du site (label affiché) </label>
                    <input type="text" name="client_name" class="form-input"
                        value="{{ old('client_name') }}"
                        placeholder="Ex. Site officiel XYZ" required>
                    @error('client_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email de notification</label>
                    <input type="email" name="client_email" class="form-input"
                        value="{{ old('client_email') }}"
                        placeholder="contact@entreprise.com">
                    <div class="form-help">Pour recevoir les alertes par email</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">URL du site à surveiller </label>
                <input type="url" name="url" class="form-input"
                    value="{{ old('url') }}"
                    placeholder="https://www.exemple.com" required>
                @error('url') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Fréquence de vérification </label>
                    <select name="frequency_min" class="form-select" required>
                        <option value="5"  {{ old('frequency_min', '5')==='5' ? 'selected' : '' }}>5 minutes</option>
                        <option value="10" {{ old('frequency_min')==='10' ? 'selected' : '' }}>10 minutes</option>
                        <option value="15" {{ old('frequency_min')==='15' ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('frequency_min')==='30' ? 'selected' : '' }}>30 minutes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Seuil de lenteur (ms) </label>
                    <input type="number" name="response_threshold_ms" class="form-input"
                        value="{{ old('response_threshold_ms', 2000) }}"
                        min="500" max="30000" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Emails de notification additionnels</label>
                <input type="text" name="notify_emails" class="form-input"
                    value="{{ old('notify_emails') }}"
                    placeholder="email1@exemple.com, email2@exemple.com">
                <div class="form-help">Séparer plusieurs emails par des virgules</div>
            </div>

            <div style="background:var(--bg-soft); padding:18px; border-radius:var(--radius); margin-bottom:20px;">
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:14px;">
                    Options de surveillance
                </div>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:12px;">
                    <input type="checkbox" name="ssl_check" value="1" {{ old('ssl_check', true) ? 'checked' : '' }}>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:var(--text);">Surveillance du certificat SSL</div>
                        <div class="text-xs text-muted">Vérifie la validité et la date d'expiration</div>
                    </div>
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="whois_check" value="1" {{ old('whois_check', true) ? 'checked' : '' }}>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:var(--text);">Surveillance WHOIS du domaine</div>
                        <div class="text-xs text-muted">Vérifie l'expiration du nom de domaine</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('sites.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i> Ajouter le site
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- MODAL — CRÉATION RAPIDE D'UN CLIENT                     --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="clientModal" class="modal-overlay">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-user-plus" style="color:var(--primary); margin-right:6px;"></i>
                Créer un nouveau client
            </div>
            <button class="modal-close" onclick="closeClientModal()"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body">
            <p class="text-sm text-muted" style="margin-bottom:18px;">
                Le client recevra un compte pour consulter ses propres sites surveillés.
            </p>

            <div id="clientModalError" class="alert alert-error" style="display:none;"></div>

            <div class="form-group">
                <label class="form-label">Nom complet </label>
                <input type="text" id="newClientName" class="form-input"
                       placeholder="Ahmed Bennani" required>
            </div>

            <div class="form-group">
                <label class="form-label">Adresse email </label>
                <input type="email" id="newClientEmail" class="form-input"
                       placeholder="ahmed@entreprise.com" required>
                <div class="form-help">Servira d'identifiant de connexion</div>
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe </label>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="newClientPassword" class="form-input" style="flex:1;"
                           placeholder="Min. 8 caractères" required>
                    <button type="button" onclick="generatePassword()" class="btn-secondary"
                            title="Générer un mot de passe aléatoire">
                        <i class="fas fa-dice"></i> Générer
                    </button>
                </div>
                <div class="form-help">
                    <i class="fas fa-info-circle"></i>
                    Communiquez ce mot de passe au client de manière sécurisée.
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" onclick="closeClientModal()" class="btn-secondary">Annuler</button>
            <button type="button" onclick="submitNewClient()" id="submitClientBtn" class="btn-primary">
                <i class="fas fa-check"></i> Créer le client
            </button>
        </div>
    </div>
</div>

<script>
// ─── Ouvrir / fermer la modal ───
function openClientModal() {
    document.getElementById('clientModal').classList.add('active');
    document.getElementById('newClientName').focus();
    generatePassword(); // Génère un mot de passe par défaut
}

function closeClientModal() {
    document.getElementById('clientModal').classList.remove('active');
    document.getElementById('newClientName').value = '';
    document.getElementById('newClientEmail').value = '';
    document.getElementById('newClientPassword').value = '';
    document.getElementById('clientModalError').style.display = 'none';
}

// Fermer en cliquant en dehors
document.getElementById('clientModal').addEventListener('click', function(e) {
    if (e.target === this) closeClientModal();
});

// ─── Générer un mot de passe aléatoire fort ───
function generatePassword() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    const symbols = '@#$%&';
    let pwd = '';
    for (let i = 0; i < 10; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    pwd += symbols.charAt(Math.floor(Math.random() * symbols.length));
    pwd += Math.floor(Math.random() * 100);
    document.getElementById('newClientPassword').value = pwd;
}

// ─── Soumettre le nouveau client ───
async function submitNewClient() {
    const name = document.getElementById('newClientName').value.trim();
    const email = document.getElementById('newClientEmail').value.trim();
    const password = document.getElementById('newClientPassword').value.trim();
    const errorEl = document.getElementById('clientModalError');
    const btn = document.getElementById('submitClientBtn');

    // Validation côté client
    if (!name || !email || !password) {
        errorEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Tous les champs sont obligatoires.';
        errorEl.style.display = 'flex';
        return;
    }
    if (password.length < 8) {
        errorEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Le mot de passe doit faire au moins 8 caractères.';
        errorEl.style.display = 'flex';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Création…';

    try {
        const res = await fetch('{{ route("clients.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, email, password })
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            throw new Error(data.message || 'Erreur lors de la création');
        }

        // Ajouter le nouveau client au select et le sélectionner
const select = document.getElementById('userSelect');
const option = new Option(
    `${data.client.name} (${data.client.email})`,
    data.client.id,
    true,
    true
);
select.appendChild(option);

// Fermer la modal
closeClientModal();

// Message de confirmation avec statut email
if (data.email_sent) {
    alert('✅ Client "' + data.client.name + '" créé avec succès.\n\n📧 Un email de bienvenue avec les identifiants a été envoyé à ' + data.client.email);
} else {
    alert('✅ Client "' + data.client.name + '" créé avec succès.\n\n⚠️ Attention : l\'email de bienvenue n\'a pas pu être envoyé. Veuillez communiquer manuellement les identifiants au client.');
}

    } catch (err) {
        errorEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + err.message;
        errorEl.style.display = 'flex';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Créer le client';
    }
}
</script>

@endsection