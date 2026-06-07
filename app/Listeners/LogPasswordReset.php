<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\PasswordReset;

class LogPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        AuditService::log(
            action:      'password_changed',
            category:    'auth',
            description: "Mot de passe réinitialisé pour {$user->name} ({$user->email})",
            status:      'success',
            userData:    [
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
            ]
        );
    }
}