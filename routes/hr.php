<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRController;


Route::middleware(['auth', 'role:hr_admin'])->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    Route::get('/hr/request/{id}', [HRController::class, 'showRequest'])->name('hr.show-request');
    Route::patch('/hr/request/{id}/approve', [HRController::class, 'approveRequest'])->name('hr.approve-request');
    Route::patch('/hr/request/{id}/reject', [HRController::class, 'rejectRequest'])->name('hr.reject-request');
    Route::get('/hr/policies', [HRController::class, 'policies'])->name('hr.policies');
    Route::patch('/hr/policy/{id}', [HRController::class, 'updatePolicy'])->name('hr.update-policy');
    Route::get('/hr/employees', [HRController::class, 'employees'])->name('hr.employees');
    Route::patch('/hr/registrations/{id}/approve', [HRController::class, 'approveRegistration'])->name('hr.approve-registration');
    Route::delete('/hr/registrations/{id}', [HRController::class, 'rejectRegistration'])->name('hr.reject-registration');
    Route::get('/hr/employees/create', [HRController::class, 'createEmployee'])->name('hr.create-employee');
    Route::post('/hr/employees', [HRController::class, 'storeEmployee'])->name('hr.store-employee');
    Route::get('/hr/employees/{id}/edit', [HRController::class, 'editEmployee'])->name('hr.edit-employee');
    Route::patch('/hr/employees/{id}', [HRController::class, 'updateEmployee'])->name('hr.update-employee');
    Route::delete('/hr/employees/{id}', [HRController::class, 'destroyEmployee'])->name('hr.destroy-employee');
    Route::get('/hr/departments', [HRController::class, 'departments'])->name('hr.departments');
    Route::post('/hr/departments', [HRController::class, 'storeDepartment'])->name('hr.store-department');
    Route::patch('/hr/departments/{id}', [HRController::class, 'updateDepartment'])->name('hr.update-department');
    Route::delete('/hr/departments/{id}', [HRController::class, 'destroyDepartment'])->name('hr.destroy-department');
});

