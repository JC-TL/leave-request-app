<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRController;

// HR Admin routes
Route::middleware(['auth', 'role:hr_admin'])->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    Route::get('/hr/request/{id}', [HRController::class, 'showRequest'])->name('hr.show-request');
    Route::patch('/hr/request/{id}/approve', [HRController::class, 'approveRequest'])->name('hr.approve-request');
    Route::patch('/hr/request/{id}/reject', [HRController::class, 'rejectRequest'])->name('hr.reject-request');
    Route::get('/hr/policies', [HRController::class, 'policies'])->name('hr.policies');
    Route::patch('/hr/policy/{id}', [HRController::class, 'updatePolicy'])->name('hr.update-policy');
    Route::get('/hr/employees', [HRController::class, 'employees'])->name('hr.employees');
    Route::get('/hr/employees/create', [HRController::class, 'createEmployee'])->name('hr.create-employee');
    Route::post('/hr/employees', [HRController::class, 'storeEmployee'])->name('hr.store-employee');
});

