<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

// Employee routes
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::post('/employee/request', [EmployeeController::class, 'storeRequest'])->name('employee.store-request');
    Route::patch('/request/{id}/cancel', [EmployeeController::class, 'cancelRequest'])->name('request.cancel');
});

