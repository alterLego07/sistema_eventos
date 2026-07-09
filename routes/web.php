<?php

use App\Http\Controllers\Admin\BudgetItemController;
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

    // Presupuesto por evento (gastos: estimado vs real + pagos)
    Route::get('events/{event}/budget',        [BudgetItemController::class, 'index'])->name('events.budget.index');
    Route::get('events/{event}/budget/create', [BudgetItemController::class, 'create'])->name('events.budget.create');
    Route::post('events/{event}/budget',       [BudgetItemController::class, 'store'])->name('events.budget.store');
    Route::get('budget/{budgetItem}/edit',     [BudgetItemController::class, 'edit'])->name('budget.edit');
    Route::put('budget/{budgetItem}',          [BudgetItemController::class, 'update'])->name('budget.update');
    Route::delete('budget/{budgetItem}',       [BudgetItemController::class, 'destroy'])->name('budget.destroy');

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
