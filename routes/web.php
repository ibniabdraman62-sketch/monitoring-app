<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StatistiqueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'active_user'])->group(function () {


// ═════════════════════════════════════════════════
// Exports Excel
// ═════════════════════════════════════════════════
Route::prefix('export')->name('export.')->group(function () {
    Route::get('/sites',     [ExportController::class, 'sites'])->name('sites');
    Route::get('/incidents', [ExportController::class, 'incidents'])->name('incidents');
    Route::get('/alertes',   [ExportController::class, 'alertes'])->name('alertes');
    Route::get('/rapports',  [ExportController::class, 'rapports'])->name('rapports');
});


    // ═════════════════════════════════════════════════
    // Création rapide d'un client (depuis formulaire site)
    // ═════════════════════════════════════════════════
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

        \App\Services\AuditService::log('user_created', 'user', "Création rapide du client « {$client->name} » ({$client->email}) depuis formulaire site", $client);

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
            'success'     => true,
            'email_sent'  => $emailSent,
            'email_error' => $emailError,
            'client'      => [
                'id'    => $client->id,
                'name'  => $client->name,
                'email' => $client->email,
            ],
        ]);
    })->name('clients.quick-create');

    // ═════════════════════════════════════════════════
    // Chatbot
    // ═════════════════════════════════════════════════
    Route::get('/chatbot', function () {
        return view('chatbot.index');
    })->name('chatbot.index');

    Route::post('/chatbot/send', function (\Illuminate\Http\Request $request) {
        $message = $request->input('message', '');
        if (empty(trim($message))) {
            return response()->json(['reply' => 'Veuillez saisir un message.']);
        }

        $sites = \App\Models\Site::where('is_active', 1)->get();
        $totalSites   = $sites->count();
        $sitesOnline  = 0;
        $sitesOffline = 0;
        $sitesDetails = '';

        foreach ($sites as $site) {
            $lastVerif = $site->verifications()->latest('checked_at')->first();
            $isUp      = $lastVerif?->is_up;
            $status    = $isUp ? '✅ En ligne' : '❌ Hors ligne';
            $rt        = $lastVerif?->response_time_ms ?? 'N/D';
            $ssl       = $lastVerif?->ssl_days_remaining ?? 'N/D';

            if ($isUp) $sitesOnline++; else $sitesOffline++;

            $sitesDetails .= "- {$site->client_name} ({$site->url}) : {$status}, ";
            $sitesDetails .= "temps réponse: {$rt}ms, SSL: {$ssl} jours\n";
        }

        $incidentsActifs = \App\Models\Incident::whereNull('resolved_at')->count();

        $systemPrompt  = "Tu es MonitorPro Assistant, l'assistant intelligent de la plateforme de surveillance web de Soft Seven Art (Casablanca, Maroc).\n\n";
        $systemPrompt .= "Réponds toujours en français, de manière professionnelle et concise.\n\n";
        $systemPrompt .= "Utilise le format Markdown pour structurer tes réponses (titres, listes, gras).\n\n";
        $systemPrompt .= "=== ÉTAT ACTUEL DU PARC SURVEILLÉ ===\n";
        $systemPrompt .= "Total sites : {$totalSites}\n";
        $systemPrompt .= "Sites en ligne : {$sitesOnline}\n";
        $systemPrompt .= "Sites hors ligne : {$sitesOffline}\n";
        $systemPrompt .= "Incidents actifs : {$incidentsActifs}\n\n";
        $systemPrompt .= "=== DÉTAIL DES SITES ===\n{$sitesDetails}";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openrouter.key'),
                'HTTP-Referer'  => config('app.url'),
                'X-Title'       => 'MonitorPro — Soft Seven Art',
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'    => 'google/gemini-2.5-flash',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $message],
                ],
                'max_tokens'  => 1000,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                \Log::error('OpenRouter error: ' . $response->body());
                return response()->json(['reply' => 'Le service IA est temporairement indisponible.'], 500);
            }

            $data  = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'Je n\'ai pas pu générer de réponse.';

            return response()->json(['reply' => $reply]);

        } catch (\Exception $e) {
            \Log::error('OpenRouter exception: ' . $e->getMessage());
            return response()->json(['reply' => 'Erreur de connexion au service IA.'], 500);
        }
    })->name('chatbot.send');

    // ═════════════════════════════════════════════════
    // Dashboard
    // ═════════════════════════════════════════════════
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ═════════════════════════════════════════════════
    // Profile
    // ═════════════════════════════════════════════════
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ═════════════════════════════════════════════════
    // Sites
    // ═════════════════════════════════════════════════
    Route::resource('sites', SiteController::class);
    Route::patch('sites/{site}/toggle',    [SiteController::class, 'toggle'])->name('sites.toggle');
    Route::post('sites/{site}/check-now',  [SiteController::class, 'checkNow'])->name('sites.check-now');

    // ═════════════════════════════════════════════════
    // Rapports
    // ═════════════════════════════════════════════════
    Route::get('/rapports',                       [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/generate/{site}',       [RapportController::class, 'generate'])->name('rapports.generate');
    Route::get('/rapports/download/{rapport}',    [RapportController::class, 'download'])->name('rapports.download');

    Route::post('/rapports/{site}/send-email', function (\Illuminate\Http\Request $request, \App\Models\Site $site) {
        $request->validate(['email' => 'required|email']);

        $rapport = \App\Models\Rapport::where('site_id', $site->id)->latest('generated_at')->first();

        if (!$rapport) {
            $rapport = \App\Models\Rapport::create([
                'site_id'         => $site->id,
                'period_start'    => now()->subDays(7)->toDateString(),
                'period_end'      => now()->toDateString(),
                'uptime_pct'      => ($site->verifications()->where('checked_at', '>=', now()->subDays(7))->avg('is_up') ?? 1) * 100,
                'incidents_count' => $site->incidents()->where('started_at', '>=', now()->subDays(7))->count(),
                'avg_response_ms' => $site->verifications()->where('checked_at', '>=', now()->subDays(7))->avg('response_time_ms') ?? 0,
                'generated_at'    => now(),
            ]);
        }

        $verifications = $site->verifications()->where('checked_at', '>=', now()->subDays(7))->orderByDesc('checked_at')->get();
        $incidents     = $site->incidents()->where('started_at', '>=', now()->subDays(7))->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rapports.pdf', [
            'site'          => $site,
            'rapport'       => $rapport,
            'verifications' => $verifications,
            'incidents'     => $incidents,
            'periodStart'   => now()->subDays(7)->toDateString(),
            'periodEnd'     => now()->toDateString(),
            'uptimePct'     => $rapport->uptime_pct,
            'avgResponse'   => round($rapport->avg_response_ms),
        ]);

        $pdfFileName = 'rapport_' . $site->id . '_' . now()->format('Ymd_His') . '.pdf';
        $pdfPath = storage_path('app/rapports/' . $pdfFileName);

        if (!file_exists(storage_path('app/rapports'))) {
            mkdir(storage_path('app/rapports'), 0755, true);
        }

        $pdf->save($pdfPath);

        $data = [
            'uptime_pct'      => round($rapport->uptime_pct, 2),
            'incidents_count' => $rapport->incidents_count,
            'avg_response_ms' => round($rapport->avg_response_ms),
            'period_start'    => $rapport->period_start,
            'period_end'      => $rapport->period_end,
        ];

        try {
            \Illuminate\Support\Facades\Mail::to($request->email)
                ->send(new \App\Mail\RapportHebdoMail($site, $data, $pdfPath));

            // ═══ AUDIT LOG ═══
            try {
                \App\Services\AuditService::log(
                    action:      'report_emailed',
                    category:    'report',
                    description: "Envoi par email du rapport « {$site->client_name} » vers {$request->email}",
                    model:       $site,
                    newValues:   [
                        'site'       => $site->client_name,
                        'email_to'   => $request->email,
                        'uptime_pct' => round($rapport->uptime_pct, 2),
                    ]
                );
            } catch (\Throwable $auditEx) {
                \Illuminate\Support\Facades\Log::error('[AUDIT FAIL] report_emailed : ' . $auditEx->getMessage());
            }

            if (file_exists($pdfPath)) unlink($pdfPath);

            return back()->with('success', "Rapport de {$site->client_name} envoyé à {$request->email}.");
        } catch (\Exception $e) {
            if (file_exists($pdfPath)) unlink($pdfPath);
            return back()->with('error', 'Erreur envoi : ' . $e->getMessage());
        }
    })->name('rapports.send-email')->middleware('not_client');

    // ═════════════════════════════════════════════════
    // Incidents
    // ═════════════════════════════════════════════════
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');

    // ═════════════════════════════════════════════════
    // Alertes
    // ═════════════════════════════════════════════════
    Route::get('/alertes', [\App\Http\Controllers\AlerteController::class, 'index'])->name('alertes.index');

    // ═════════════════════════════════════════════════
    // Notifications cloche (in-app)
    // ═════════════════════════════════════════════════
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',              [NotificationController::class, 'index'])->name('index');
        Route::post('/{alerte}/lue', [NotificationController::class, 'marquerLue'])->name('lue');
        Route::post('/toutes-lues',  [NotificationController::class, 'marquerToutesLues'])->name('toutes-lues');
    });

    // ═════════════════════════════════════════════════
    // Routes Admin (not_client)
    // ═════════════════════════════════════════════════
    Route::middleware('not_client')->group(function () {

        // ─── Statistiques globales (admin) ───
        Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

        // ─── Historique d'audit ───
        Route::get('/audit',              [\App\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/{auditLog}',   [\App\Http\Controllers\AuditController::class, 'show'])->name('audit.show');

        // ─── Gestion Clients ───
        Route::get('/clients', function () {
            return view('admin.clients');
        })->name('clients.index');

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
            \App\Services\AuditService::log('user_created', 'user', "Création du client « {$client->name} » ({$client->email})", $client);

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

        Route::patch('/clients/{client}', function (\Illuminate\Http\Request $request, \App\Models\User $client) {
            if ($client->role !== 'client') abort(404);
            $validated = $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $client->id,
            ]);
            $client->update($validated);
            \App\Services\AuditService::log('user_updated', 'user', "Modification du client « {$client->name} »", $client, [], $validated);
            return redirect()->route('clients.index')->with('success', 'Client modifié.');
        })->name('clients.update');

        Route::patch('/clients/{client}/reset-password', function (\Illuminate\Http\Request $request, \App\Models\User $client) {
            if ($client->role !== 'client') abort(404);
            $validated = $request->validate(['password' => 'required|string|min:8']);
            $client->update(['password' => bcrypt($validated['password'])]);
            \App\Services\AuditService::log('password_reset_admin', 'user', "Réinitialisation du mot de passe du client « {$client->name} »", $client);
            try {
                \Illuminate\Support\Facades\Mail::to($client->email)
                    ->send(new \App\Mail\ClientWelcomeMail($client, $validated['password']));
                return redirect()->route('clients.index')->with('success', "Mot de passe réinitialisé. Email envoyé à {$client->email}.");
            } catch (\Exception $e) {
                return redirect()->route('clients.index')->with('success', "Mot de passe réinitialisé. ⚠ L'email n'a pas pu être envoyé.");
            }
        })->name('clients.reset-password');

        Route::patch('/clients/{client}/toggle', function (\App\Models\User $client) {
            if ($client->role !== 'client') abort(404);
            $client->update(['is_active' => !$client->is_active]);
            $statusLabel = $client->is_active ? 'activé' : 'désactivé';
\App\Services\AuditService::log($client->is_active ? 'user_activated' : 'user_deactivated', 'user', "Compte du client « {$client->name} » {$statusLabel}", $client);
            return redirect()->route('clients.index')
                ->with('success', $client->is_active ? 'Client activé.' : 'Client désactivé.');
        })->name('clients.toggle');

        Route::delete('/clients/{client}', function (\App\Models\User $client) {
            if ($client->role !== 'client') abort(404);
            $name = $client->name;
            \App\Services\AuditService::log('user_deleted', 'user', "Suppression du client « {$client->name} » ({$client->email})", $client);
            $client->delete();
            return redirect()->route('clients.index')->with('success', "Client '{$name}' supprimé.");
        })->name('clients.destroy');

        // ─── Cron Jobs ───
        Route::get('/cron-jobs', function () {
            return view('admin.cron_jobs');
        })->name('cron.index')->middleware('super_admin');

        Route::post('/cron-jobs/run', function (\Illuminate\Http\Request $request) {
            if (!auth()->user()->isSuperAdmin()) abort(403);

            $command = $request->input('command');
            $allowed = [
                'monitor:check-uptime',
                'monitor:check-ssl',
                'monitor:check-whois',
                'monitor:send-weekly-report',
                'monitor:cleanup',
            ];
            if (!in_array($command, $allowed)) abort(403);

            if ($command === 'monitor:send-weekly-report') {
                $start      = microtime(true);
                $sitesCount = 0;
                $errors     = 0;

                $sites = \App\Models\Site::where('is_active', 1)->get();

                foreach ($sites as $site) {
                    try {
                        $uptimeAvg = $site->verifications()->where('checked_at', '>=', now()->subDays(7))->avg('is_up');

                        $rapport = \App\Models\Rapport::create([
                            'site_id'         => $site->id,
                            'period_start'    => now()->subDays(7)->toDateString(),
                            'period_end'      => now()->toDateString(),
                            'uptime_pct'      => $uptimeAvg !== null ? round($uptimeAvg * 100, 2) : 100,
                            'incidents_count' => $site->incidents()->where('started_at', '>=', now()->subDays(7))->count(),
                            'avg_response_ms' => $site->verifications()->where('checked_at', '>=', now()->subDays(7))->avg('response_time_ms') ?? 0,
                            'generated_at'    => now(),
                        ]);

                        $emailTo = null;
                        if ($site->client_email && str_contains($site->client_email, '.')) {
                            $emailTo = trim($site->client_email);
                        } elseif ($site->notify_emails) {
                            $first = trim(explode(',', $site->notify_emails)[0]);
                            if (str_contains($first, '.')) $emailTo = $first;
                        }
                        if (!$emailTo) {
                            $clientUser = \App\Models\User::find($site->user_id);
                            $emailTo    = $clientUser ? $clientUser->email : auth()->user()->email;
                        }

                        $data = [
                            'uptime_pct'      => $rapport->uptime_pct,
                            'incidents_count' => $rapport->incidents_count,
                            'avg_response_ms' => round($rapport->avg_response_ms),
                            'period_start'    => $rapport->period_start,
                            'period_end'      => $rapport->period_end,
                        ];

                        $verifications = $site->verifications()->where('checked_at', '>=', now()->subDays(7))->orderByDesc('checked_at')->get();
                        $incidents     = $site->incidents()->where('started_at', '>=', now()->subDays(7))->get();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rapports.pdf', [
                            'site'          => $site,
                            'rapport'       => $rapport,
                            'verifications' => $verifications,
                            'incidents'     => $incidents,
                            'periodStart'   => now()->subDays(7)->toDateString(),
                            'periodEnd'     => now()->toDateString(),
                            'uptimePct'     => $rapport->uptime_pct,
                            'avgResponse'   => round($rapport->avg_response_ms),
                        ]);

                        $pdfFileName = 'rapport_' . $site->id . '_' . now()->format('Ymd_His') . '.pdf';
                        $pdfPath     = storage_path('app/rapports/' . $pdfFileName);

                        if (!file_exists(storage_path('app/rapports'))) {
                            mkdir(storage_path('app/rapports'), 0755, true);
                        }

                        $pdf->save($pdfPath);
                        $rapport->update(['pdf_path' => $pdfFileName]);

                        \Illuminate\Support\Facades\Mail::to($emailTo)->send(new \App\Mail\RapportHebdoMail($site, $data, $pdfPath));

                        if (file_exists($pdfPath)) unlink($pdfPath);

                        $sitesCount++;
                    } catch (\Exception $e) {
                        $errors++;
                        \Illuminate\Support\Facades\Log::error('Erreur rapport site ' . $site->client_name . ' : ' . $e->getMessage());
                    }
                }

                $durationMs = round((microtime(true) - $start) * 1000);

                \App\Models\CronLog::create([
                    'command'       => $command,
                    'status'        => $errors === 0 ? 'success' : 'error',
                    'duration_ms'   => $durationMs,
                    'sites_checked' => $sitesCount,
                    'errors_count'  => $errors,
                    'executed_at'   => now(),
                ]);

                return back()->with('success',
                    "Rapports envoyés pour {$sitesCount} sites en {$durationMs}ms."
                    . ($errors > 0 ? " ({$errors} erreurs — voir les logs)" : '')
                );
            }

            $start    = microtime(true);
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
            \App\Services\AuditService::log('cron_run', 'system', "Exécution manuelle de la commande « {$command} » — " . ($exitCode === 0 ? 'succès' : 'erreur') . " ({$durationMs}ms)");
            return back()->with('success', "Commande {$command} exécutée en {$durationMs}ms");
        })->name('cron.run');

        // ─── Gestion Agents ───
        Route::get('/agents',                  [AgentController::class, 'index'])->name('agents.index')->middleware('super_admin');
        Route::post('/agents',                 [AgentController::class, 'store'])->name('agents.store')->middleware('super_admin');
        Route::patch('/agents/{user}/toggle',  [AgentController::class, 'toggle'])->name('agents.toggle')->middleware('super_admin');
        Route::patch('/agents/{user}',         [AgentController::class, 'update'])->name('agents.update')->middleware('super_admin');

    }); // fin admin not_client

}); // fin auth + active_user

require __DIR__.'/auth.php';