<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfessionalController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Professional\AgendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Barbearia Turetta
|--------------------------------------------------------------------------
*/

// Rota raiz → booking público
Route::get('/', fn () => redirect()->route('client.booking'));

// ─── Autenticação ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Booking Público (sem auth) ──────────────────────────────────────────
Route::prefix('agendar')->group(function () {
    Route::get('/', [BookingController::class, 'selectService'])->name('client.booking');
    Route::get('/profissional/{service}', [BookingController::class, 'selectProfessional'])->name('client.booking.professional');
    Route::get('/horario/{service}/{professional}', [BookingController::class, 'selectSlot'])->name('client.booking.slot');
    Route::get('/slots', [BookingController::class, 'getAvailableSlots'])->name('client.booking.slots');
    Route::get('/confirmar', [BookingController::class, 'confirm'])->name('client.booking.confirm');
    Route::post('/finalizar', [BookingController::class, 'store'])->name('client.booking.store');
    Route::get('/sucesso', [BookingController::class, 'success'])->name('client.booking.success');
});

// ─── Admin (role_id = 1) ─────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'role:1'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('services', ServiceController::class)->except('show')->names([
        'index'   => 'admin.services.index',
        'create'  => 'admin.services.create',
        'store'   => 'admin.services.store',
        'edit'    => 'admin.services.edit',
        'update'  => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ]);

    Route::resource('professionals', ProfessionalController::class)->except('show')->names([
        'index'   => 'admin.professionals.index',
        'create'  => 'admin.professionals.create',
        'store'   => 'admin.professionals.store',
        'edit'    => 'admin.professionals.edit',
        'update'  => 'admin.professionals.update',
        'destroy' => 'admin.professionals.destroy',
    ]);

    Route::resource('schedules', ScheduleController::class)->except('show')->names([
        'index'   => 'admin.schedules.index',
        'create'  => 'admin.schedules.create',
        'store'   => 'admin.schedules.store',
        'edit'    => 'admin.schedules.edit',
        'update'  => 'admin.schedules.update',
        'destroy' => 'admin.schedules.destroy',
    ]);

    Route::get('/clients', [ClientController::class, 'index'])->name('admin.clients');

    // Gerenciamento de status dos agendamentos (DashboardController)
    Route::patch('/appointments/{appointment}/status', [DashboardController::class, 'updateStatus'])->name('admin.appointments.status');
    Route::delete('/appointments/{appointment}', [DashboardController::class, 'destroy'])->name('admin.appointments.destroy');
});

// ─── Profissional (role_id = 2) ──────────────────────────────────────────
Route::prefix('profissional')->middleware(['auth', 'role:2'])->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('professional.agenda');
    Route::get('/agendamento/{appointment}', [AgendaController::class, 'show'])->name('professional.appointment.show');
});
