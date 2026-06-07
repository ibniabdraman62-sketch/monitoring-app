<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? '[email non fourni]';

        AuditService::log(
            action:      'login_failed',
            category:    'auth',
            description: "Échec de connexion pour l'adresse : {$email}",
            status:      'failure',
            userData:    [
                'user_id'   => null,
                'user_name' => $email,
                'user_role' => null,
            ]
        );
    }
}