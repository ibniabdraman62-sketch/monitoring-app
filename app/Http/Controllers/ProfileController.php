<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // ═══ Capture des anciennes valeurs AVANT update ═══
        $original = $user->only(['name', 'email']);

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // ═══ AUDIT LOG ═══
        $changes = $user->getChanges();
        unset($changes['updated_at'], $changes['email_verified_at'], $changes['remember_token']);

        if (!empty($changes)) {
            $oldFiltered = array_intersect_key($original, $changes);

            AuditService::log(
                action:      'profile_updated',
                category:    'profile',
                description: "Mise à jour du profil — " . implode(', ', array_keys($changes)),
                model:       $user,
                oldValues:   $oldFiltered,
                newValues:   $changes
            );
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // ═══ AUDIT LOG AVANT déconnexion + suppression ═══
        $userData = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ];

        AuditService::log(
            action:      'profile_deleted',
            category:    'profile',
            description: "Auto-suppression du compte de {$user->name} ({$user->email})",
            oldValues:   $userData
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}