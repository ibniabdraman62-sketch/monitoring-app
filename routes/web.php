<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'active_user'])->group(function () {
    // ═══ Création rapide d'un client depuis le formulaire d'ajout de site ═══
// ═══ Création rapide d'un client + envoi email de bienvenue ═══
Route::post('/clients/quick-create', function (\Illuminate\Http\Request $request) {
    if (auth()->user()->role === 'client') {
        abort(403);
    }

    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    $client = \App\Models\User::create([
        'name'              => $validated['name'],
        'email'             => $validated['email'],
        'password'          => bcrypt($validated['password']),
        'role'              => 'client',
        'is_active'         => true,
        'email_verified_at' => now(),
    ]);

    // ─── Envoi de l'email de bienvenue ───
    $emailSent = false;
    $emailError = null;
    try {
        \Illuminate\Support\Facades\Mail::to($client->email)
            ->send(new \App\Mail\ClientWelcomeMail($client, $validated['password']));
        $emailSent = true;
    } catch (\Exception $e) {
        $emailError = $e->getMessage();
        \Illuminate\Support\Facades\Log::error('Erreur envoi email bienvenue client : ' . $emailError);
    }

    return response()->json([
        'success'    => true,
        'email_sent' => $emailSent,
        'email_error' => $emailError,
        'client'     => [
            'id'    => $client->id,
            'name'  => $client->name,
            'email' => $client->email,
        ],
    ]);
})->name('clients.quick-create');

    // =========================
    // Chatbot
    // =========================

    Route::get('/chatbot', function () {
        return view('chatbot.index');
    })->name('chatbot.index');

    Route::post('/chatbot/send', function (\Illuminate\Http\Request $request) {

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $webhookUrl = 'https://tcharikoreydjoko.app.n8n.cloud/webhook/6e561f26-caed-4b69-b2c5-d336f116079b/chat';

        try {

            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($webhookUrl, [
                    'chatInput' => $request->message,
                    'sessionId' => 'monitorpro-user-' . auth()->id(),
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'response' => 'Service IA temporairement indisponible (HTTP ' . $response->status() . ').'
                ], 200);
            }

            $data = $response->json();

            $aiResponse =
                $data['output']
                ?? $data['response']
                ?? $data['text']
                ?? $data['message']
                ?? 'Je n\'ai pas pu traiter votre demande.';

            return response()->json([
                'response' => $aiResponse
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'response' => 'Erreur : ' . $e->getMessage()
            ], 200);

        }

    })->name('chatbot.send');


    // =========================
    // Dashboard
    // =========================

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    // =========================
    // Profile
    // =========================

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    // =========================
    // Sites
    // =========================

    Route::resource('sites', SiteController::class);

    Route::patch('sites/{site}/toggle', [SiteController::class, 'toggle'])
        ->name('sites.toggle');

    Route::post('sites/{site}/check-now', [SiteController::class, 'checkNow'])
        ->name('sites.check-now');


    // =========================
    // Rapports
    // =========================

    Route::get('/rapports', [RapportController::class, 'index'])
        ->name('rapports.index');

    Route::get('/rapports/generate/{site}', [RapportController::class, 'generate'])
        ->name('rapports.generate');

    Route::get('/rapports/download/{rapport}', [RapportController::class, 'download'])
        ->name('rapports.download');

    Route::post('/rapports/{site}/send-email', [RapportController::class, 'sendEmail'])
        ->name('rapports.send-email');


    // =========================
    // Incidents
    // =========================

    Route::get('/incidents', [IncidentController::class, 'index'])
        ->name('incidents.index');


    // =========================
    // Alertes
    // =========================

    Route::get('/alertes', [\App\Http\Controllers\AlerteController::class, 'index'])
        ->name('alertes.index');


    // =========================
    // Routes Admin
    // =========================

    Route::middleware(['auth', 'active_user', 'not_client'])->group(function () {

    // ═══ Page Gestion Clients ═══
Route::get('/clients', function () {
    return view('admin.clients');
})->name('clients.index');

// Créer un client (depuis le formulaire de la page)
Route::post('/clients', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    $client = \App\Models\User::create([
        'name'              => $validated['name'],
        'email'             => $validated['email'],
        'password'          => bcrypt($validated['password']),
        'role'              => 'client',
        'is_active'         => true,
        'email_verified_at' => now(),
    ]);

    // Envoi de l'email de bienvenue
    try {
        \Illuminate\Support\Facades\Mail::to($client->email)
            ->send(new \App\Mail\ClientWelcomeMail($client, $validated['password']));
        return redirect()->route('clients.index')
            ->with('success', "Client '{$client->name}' créé. Email de bienvenue envoyé à {$client->email}.");
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erreur envoi email client : ' . $e->getMessage());
        return redirect()->route('clients.index')
            ->with('success', "Client '{$client->name}' créé. ⚠ L'email n'a pas pu être envoyé.");
    }
})->name('clients.store');

// Modifier un client (nom + email)
Route::patch('/clients/{client}', function (\Illuminate\Http\Request $request, \App\Models\User $client) {
    if ($client->role !== 'client') abort(404);
    $validated = $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $client->id,
    ]);
    $client->update($validated);
    return redirect()->route('clients.index')->with('success', 'Client modifié.');
})->name('clients.update');

// Réinitialiser le mot de passe d'un client
Route::patch('/clients/{client}/reset-password', function (\Illuminate\Http\Request $request, \App\Models\User $client) {
    if ($client->role !== 'client') abort(404);
    $validated = $request->validate(['password' => 'required|string|min:8']);

    $client->update(['password' => bcrypt($validated['password'])]);

    // Renvoyer un email avec le nouveau mot de passe
    try {
        \Illuminate\Support\Facades\Mail::to($client->email)
            ->send(new \App\Mail\ClientWelcomeMail($client, $validated['password']));
        return redirect()->route('clients.index')
            ->with('success', "Mot de passe réinitialisé. Email envoyé à {$client->email}.");
    } catch (\Exception $e) {
        return redirect()->route('clients.index')
            ->with('success', "Mot de passe réinitialisé. ⚠ L'email n'a pas pu être envoyé.");
    }
})->name('clients.reset-password');

// Activer / Désactiver un client
Route::patch('/clients/{client}/toggle', function (\App\Models\User $client) {
    if ($client->role !== 'client') abort(404);
    $client->update(['is_active' => !$client->is_active]);
    return redirect()->route('clients.index')
        ->with('success', $client->is_active ? 'Client activé.' : 'Client désactivé.');
})->name('clients.toggle');

// Supprimer un client
Route::delete('/clients/{client}', function (\App\Models\User $client) {
    if ($client->role !== 'client') abort(404);
    $name = $client->name;
    $client->delete();
    return redirect()->route('clients.index')->with('success', "Client '{$name}' supprimé.");
})->name('clients.destroy');

        // =========================
        // Cron Jobs
        // =========================

        Route::get('/cron-jobs', function () {

            return view('admin.cron_jobs');

        })->name('cron.index')->middleware('super_admin');


        Route::post('/cron-jobs/run', function (\Illuminate\Http\Request $request) {

            if (!auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $command = $request->input('command');

            $allowed = [
                'monitor:check-uptime',
                'monitor:check-ssl',
                'monitor:check-whois',
                'monitor:send-weekly-report',
                'monitor:cleanup'
            ];

            if (!in_array($command, $allowed)) {
                abort(403);
            }

            $start = microtime(true);

            $exitCode = 0;

            try {

                $exitCode = \Illuminate\Support\Facades\Artisan::call($command);

            } catch (\Exception $e) {

                $exitCode = 1;

            }

            $durationMs = round((microtime(true) - $start) * 1000);

            \App\Models\CronLog::create([
                'command'       => $command,
                'status'        => $exitCode === 0 ? 'success' : 'error',
                'duration_ms'   => $durationMs,
                'sites_checked' => 0,
                'errors_count'  => $exitCode === 0 ? 0 : 1,
                'executed_at'   => now(),
            ]);

            return back()->with(
                'success',
                "Commande {$command} exécutée en {$durationMs}ms"
            );

        })->name('cron.run')->middleware('super_admin');


        // =========================
        // Gestion Agents
        // =========================

        Route::get('/agents', [AgentController::class, 'index'])
            ->name('agents.index')
            ->middleware('super_admin');

        Route::post('/agents', [AgentController::class, 'store'])
            ->name('agents.store')
            ->middleware('super_admin');

        Route::patch('/agents/{user}/toggle', [AgentController::class, 'toggle'])
            ->name('agents.toggle')
            ->middleware('super_admin');

        Route::patch('/agents/{user}', [AgentController::class, 'update'])
            ->name('agents.update')
            ->middleware('super_admin');

    });

});

require __DIR__.'/auth.php';