<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/professionals', [ProfessionalController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/slots', [AppointmentController::class, 'availableSlots']);
Route::post('/appointments', [AppointmentController::class, 'store']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/appointments', [AdminController::class, 'appointments']);
    Route::get('/clients', [AdminController::class, 'clients']);
    Route::patch('/appointments/{appointment}/status', [AdminController::class, 'updateStatus']);
});
