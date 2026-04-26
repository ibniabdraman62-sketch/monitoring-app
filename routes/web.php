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
});

require __DIR__.'/auth.php';