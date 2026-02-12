<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = LeaveType::all()->keyBy('leave_type_id');
        $roleMultipliers = [
            'employee' => 1.0,
            'dept_manager' => 1.2,
            'hr_admin' => 1.33,
            'ceo' => 1.67,
        ];

        $hrDept = Department::where('name', 'HR')->first();
        $salesDept = Department::where('name', 'Sales')->first();
        $itDept = Department::where('name', 'IT')->first();

        $ceo = Employee::create([
            'first_name' => 'David',
            'last_name' => 'Williams',
            'gender' => 'M',
            'email' => 'david@vglobal.com',
            'password' => 'password123',
            'role' => 'ceo',
        ]);

        $hrAdmin1 = Employee::create([
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'gender' => 'F',
            'email' => 'sarah@vglobal.com',
            'password' => 'password123',
            'role' => 'hr_admin',
            'dept_id' => $hrDept->dept_id,
        ]);

        $hrAdmin2 = Employee::create([
            'first_name' => 'Lisa',
            'last_name' => 'Brown',
            'gender' => 'F',
            'email' => 'lisa@vglobal.com',
            'password' => 'password123',
            'role' => 'hr_admin',
            'dept_id' => $hrDept->dept_id,
        ]);

        $salesManager = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'gender' => 'M',
            'email' => 'john@vglobal.com',
            'password' => 'password123',
            'role' => 'dept_manager',
            'dept_id' => $salesDept->dept_id,
        ]);

        $itManager = Employee::create([
            'first_name' => 'Mary',
            'last_name' => 'Johnson',
            'gender' => 'F',
            'email' => 'mary@vglobal.com',
            'password' => 'password123',
            'role' => 'dept_manager',
            'dept_id' => $itDept->dept_id,
        ]);

        $salesDept->update(['dept_manager_id' => $salesManager->emp_id]);
        $itDept->update(['dept_manager_id' => $itManager->emp_id]);

        $alice = Employee::create([
            'first_name' => 'Alice',
            'last_name' => 'Brown',
            'gender' => 'F',
            'email' => 'alice@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'dept_id' => $salesDept->dept_id,
            'manager_id' => $salesManager->emp_id,
        ]);

        $bob = Employee::create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'gender' => 'M',
            'email' => 'bob@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'dept_id' => $salesDept->dept_id,
            'manager_id' => $salesManager->emp_id,
        ]);

        $carol = Employee::create([
            'first_name' => 'Carol',
            'last_name' => 'Davis',
            'gender' => 'F',
            'email' => 'carol@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'dept_id' => $itDept->dept_id,
            'manager_id' => $itManager->emp_id,
        ]);

        $brad = Employee::create([
            'first_name' => 'Brad',
            'last_name' => 'Smith',
            'gender' => 'M',
            'email' => 'brad@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'dept_id' => $itDept->dept_id,
            'manager_id' => $itManager->emp_id,
        ]);

        $allEmployees = [
            $ceo,
            $hrAdmin1,
            $hrAdmin2,
            $salesManager,
            $itManager,
            $alice,
            $bob,
            $carol,
            $brad,
        ];

        $currentYear = now()->year;

        foreach ($allEmployees as $emp) {
            $multiplier = $roleMultipliers[$emp->role] ?? 1.0;

            foreach (LeaveType::all() as $leaveType) {
                $allocated = $leaveType->annual_entitlement;

                if (in_array($leaveType->leave_type, ['Vacation Leave', 'Sick Leave']) && $multiplier > 1.0) {
                    $allocated = (int) ceil($leaveType->annual_entitlement * $multiplier);
                }

                LeaveBalance::create([
                    'emp_id' => $emp->emp_id,
                    'leave_type_id' => $leaveType->leave_type_id,
                    'year' => $currentYear,
                    'allocated_days' => $allocated,
                    'used_days' => 0,
                ]);
            }
        }

        $baseVacation = LeaveType::where('leave_type', 'Vacation Leave')->first()->annual_entitlement ?? 15;
        $baseSick = LeaveType::where('leave_type', 'Sick Leave')->first()->annual_entitlement ?? 10;

        $this->command->info('Created employees with leave credits.');
        $this->command->table(
            ['Name', 'Email', 'Role', 'Vacation Leave', 'Sick Leave'],
            [
                ['David Williams', 'david@vglobal.com', 'ceo', ceil($baseVacation * 1.67) . ' days', ceil($baseSick * 1.67) . ' days'],
                ['Sarah Johnson', 'sarah@vglobal.com', 'hr_admin', ceil($baseVacation * 1.33) . ' days', ceil($baseSick * 1.33) . ' days'],
                ['Lisa Brown', 'lisa@vglobal.com', 'hr_admin', ceil($baseVacation * 1.33) . ' days', ceil($baseSick * 1.33) . ' days'],
                ['John Smith', 'john@vglobal.com', 'dept_manager', ceil($baseVacation * 1.2) . ' days', ceil($baseSick * 1.2) . ' days'],
                ['Mary Johnson', 'mary@vglobal.com', 'dept_manager', ceil($baseVacation * 1.2) . ' days', ceil($baseSick * 1.2) . ' days'],
                ['Alice Brown', 'alice@vglobal.com', 'employee', $baseVacation . ' days', $baseSick . ' days'],
                ['Bob Wilson', 'bob@vglobal.com', 'employee', $baseVacation . ' days', $baseSick . ' days'],
                ['Carol Davis', 'carol@vglobal.com', 'employee', $baseVacation . ' days', $baseSick . ' days'],
                ['Brad Smith', 'brad@vglobal.com', 'employee', $baseVacation . ' days', $baseSick . ' days'],
            ]
        );
    }
}
