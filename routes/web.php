<?php
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/chatbot', function() {
    return view('chatbot.index');
})->name('chatbot.index');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('sites', SiteController::class);
    Route::patch('sites/{site}/toggle', [SiteController::class, 'toggle'])->name('sites.toggle');
    Route::post('sites/{site}/check-now', [SiteController::class, 'checkNow'])->name('sites.check-now');

    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/generate/{site}', [RapportController::class, 'generate'])->name('rapports.generate');
    Route::get('/rapports/download/{rapport}', [RapportController::class, 'download'])->name('rapports.download');

    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    
    // Route Super Admin — supervision Cron Jobs
Route::get('/cron-jobs', function() {
    return view('admin.cron_jobs');
})->name('cron.index')->middleware('super_admin');
Route::post('/cron-jobs/run', function(\Illuminate\Http\Request $request) {
    if (!auth()->user()->isSuperAdmin()) abort(403);
    $command = $request->input('command');
    $allowed = ['monitor:check-uptime','monitor:check-ssl','monitor:check-whois','monitor:send-weekly-report','monitor:cleanup'];
    if (!in_array($command, $allowed)) abort(403);
    \Illuminate\Support\Facades\Artisan::call($command);
    return back()->with('success', "Commande {$command} exécutée avec succès !");
})->name('cron.run')->middleware(['auth','super_admin']);

// Alertes
Route::get('/alertes', [\App\Http\Controllers\AlerteController::class, 'index'])
    ->name('alertes.index')->middleware('auth');

// Gestion agents (Super Admin)
Route::get('/agents', [\App\Http\Controllers\AgentController::class, 'index'])
    ->name('agents.index')->middleware(['auth','super_admin']);
Route::post('/agents', [\App\Http\Controllers\AgentController::class, 'store'])
    ->name('agents.store')->middleware(['auth','super_admin']);
Route::patch('/agents/{user}/toggle', [\App\Http\Controllers\AgentController::class, 'toggle'])
    ->name('agents.toggle')->middleware(['auth','super_admin']);
    
});

require __DIR__.'/auth.php';