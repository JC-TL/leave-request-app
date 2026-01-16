<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/contact-admin', [AuthController::class, 'showContactAdmin'])->name('contact-admin');
Route::post('/contact-admin', [AuthController::class, 'submitContact'])->name('contact-admin.store');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
