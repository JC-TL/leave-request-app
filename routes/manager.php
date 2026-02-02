<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;

Route::middleware(['auth', 'role:dept_manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/manager/request/{id}', [ManagerController::class, 'showRequest'])->name('manager.show-request');
    Route::patch('/manager/request/{id}/approve', [ManagerController::class, 'approveRequest'])->name('manager.approve-request');
    Route::patch('/manager/request/{id}/reject', [ManagerController::class, 'rejectRequest'])->name('manager.reject-request');
});

