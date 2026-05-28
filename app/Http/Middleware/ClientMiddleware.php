<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware bloquant l'accès aux utilisateurs avec le rôle "client".
 *
 * À utiliser sur les routes réservées aux Super Admin et Agents :
 *   Route::middleware(['auth', 'not_client'])->group(function () { ... });
 */
class ClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'client') {
            abort(403, 'Accès refusé. Cette section est réservée aux administrateurs et agents.');
        }

        return $next($request);
    }
}