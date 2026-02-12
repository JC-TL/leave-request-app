<?php

use App\Models\Department;
use App\Models\LeaveType;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\LeaveTypeSeeder;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->seed([LeaveTypeSeeder::class, DepartmentSeeder::class]);
    $department = Department::first();

    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'employee',
        'dept_id' => $department->dept_id,
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success');
});
