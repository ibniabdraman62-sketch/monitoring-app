<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        return view('admin.agents');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $agent = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => bcrypt($request->password),
            'role'              => 'agent',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // ═══ AUDIT LOG ═══
        AuditService::log(
            action:      'user_created',
            category:    'user',
            description: "Création de l'agent « {$agent->name} » ({$agent->email})",
            model:       $agent,
            newValues:   [
                'name'  => $agent->name,
                'email' => $agent->email,
                'role'  => 'agent',
            ]
        );

        return back()->with('success', "Agent {$request->name} créé avec succès !");
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
        ]);

        // ═══ Capture des anciennes valeurs AVANT update ═══
        $original = $user->only(['name', 'email']);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        $passwordChanged = false;
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
            $passwordChanged = true;
        }

        User::where('id', $user->id)->update($data);
        $user->refresh();

        // ═══ AUDIT LOG ═══
        $changes = [];
        if ($original['name'] !== $user->name)   $changes['name']  = $user->name;
        if ($original['email'] !== $user->email) $changes['email'] = $user->email;

        if (!empty($changes) || $passwordChanged) {
            $oldFiltered = array_intersect_key($original, $changes);
            $newFiltered = $changes;

            $descParts = [];
            if (!empty($changes)) $descParts[] = implode(', ', array_keys($changes));
            if ($passwordChanged) $descParts[] = 'mot de passe';

            AuditService::log(
                action:      'user_updated',
                category:    'user',
                description: "Modification de l'agent « {$user->name} » — " . implode(', ', $descParts),
                model:       $user,
                oldValues:   $oldFiltered,
                newValues:   $newFiltered
            );

            // Log séparé spécifique pour la réinitialisation de mot de passe
            if ($passwordChanged) {
                AuditService::log(
                    action:      'password_reset_admin',
                    category:    'user',
                    description: "Réinitialisation du mot de passe de l'agent « {$user->name} » par l'administrateur",
                    model:       $user
                );
            }
        }

        return back()->with('success', "Agent {$request->name} modifié avec succès !");
    }

    public function toggle(User $user)
    {
        $oldStatus = $user->is_active;
        $newStatus = $oldStatus ? 0 : 1;

        User::where('id', $user->id)->update(['is_active' => $newStatus]);
        $user->refresh();

        // ═══ AUDIT LOG ═══
        $statusLabel = $newStatus ? 'activé' : 'désactivé';
        AuditService::log(
            action:      $newStatus ? 'user_activated' : 'user_deactivated',
            category:    'user',
            description: "Compte de l'agent « {$user->name} » {$statusLabel} par l'administrateur",
            model:       $user,
            oldValues:   ['is_active' => (bool) $oldStatus],
            newValues:   ['is_active' => (bool) $newStatus]
        );

        return back()->with('success', "Compte de {$user->name} {$statusLabel} avec succès !");
    }
}