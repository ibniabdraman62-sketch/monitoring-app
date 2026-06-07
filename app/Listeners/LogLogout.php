<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Logout;

class LogLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;

        if (!$user) return;

        AuditService::log(
            action:      'logout',
            category:    'auth',
            description: "Déconnexion de {$user->name}",
            status:      'success',
            userData:    [
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
            ]
        );
    }
}