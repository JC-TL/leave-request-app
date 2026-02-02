<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Guest routes (login, contact-admin)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/contact-admin', [AuthController::class, 'showContactAdmin'])->name('contact-admin');
    Route::post('/contact-admin', [AuthController::class, 'submitContact'])->name('contact-admin.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Role-specific routes
require __DIR__.'/employee.php';
require __DIR__.'/manager.php';
require __DIR__.'/hr.php';
