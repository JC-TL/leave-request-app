<?php

use App\Models\Employee;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $employee = Employee::factory()->create(['role' => 'employee']);

    $response = $this->post('/login', [
        'email' => $employee->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('employee.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $employee = Employee::factory()->create();

    $this->post('/login', [
        'email' => $employee->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
