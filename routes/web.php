<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// ── Invitaciones públicas ────────────────────────────────────────────────────
Route::get('/i/{token}',        [PublicInvitationController::class, 'show'])->name('invitation.show');
Route::post('/i/{token}/rsvp',  [PublicInvitationController::class, 'rsvp'])->name('invitation.rsvp');

// Compat: Breeze redirige a /dashboard tras login
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:super-admin|admin|organizador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('events/{event}/stats', [EventController::class, 'stats'])->name('events.stats');
    Route::resource('events', EventController::class)->except('show');
    Route::resource('events.invitations', InvitationController::class)->shallow()->except('show');
    Route::resource('templates', TemplateController::class)->except('show');

    // Gestión de usuarios de la empresa (admin de empresa / super-admin)
    Route::resource('users', UserController::class)
        ->except('show')
        ->middleware('role:super-admin|admin');

    // Gestión de empresas (solo super-admin, dueño de plataforma)
    Route::resource('companies', CompanyController::class)
        ->except('show')
        ->middleware('role:super-admin');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
