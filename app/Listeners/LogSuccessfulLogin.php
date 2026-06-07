<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        AuditService::log(
            action:      'login',
            category:    'auth',
            description: "Connexion réussie de {$user->name} ({$user->email})",
            status:      'success',
            userData:    [
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
            ]
        );
    }
}