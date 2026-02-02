<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $leaveCredits = [
            'employee' => [
                'Vacation Leave' => 15,
                'Sick Leave' => 10,
                'Emergency Leave' => 3,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 3,
                'Unpaid Leave' => 0,
            ],
            'dept_manager' => [
                'Vacation Leave' => 18,
                'Sick Leave' => 10,
                'Emergency Leave' => 3,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 3,
                'Unpaid Leave' => 0,
            ],
            'hr_admin' => [
                'Vacation Leave' => 20,
                'Sick Leave' => 10,
                'Emergency Leave' => 3,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 3,
                'Unpaid Leave' => 0,
            ],
            'ceo' => [
                'Vacation Leave' => 25,
                'Sick Leave' => 15,
                'Emergency Leave' => 5,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 5,
                'Unpaid Leave' => 0,
            ],
        ];

        $hrDept = Department::where('name', 'HR')->first();
        $salesDept = Department::where('name', 'Sales')->first();
        $itDept = Department::where('name', 'IT')->first();

        $ceo = User::create([
            'name' => 'David Williams',
            'email' => 'david@vglobal.com',
            'password' => 'password123',
            'role' => 'ceo',
        ]);

        $hrAdmin1 = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@vglobal.com',
            'password' => 'password123',
            'role' => 'hr_admin',
            'department_id' => $hrDept->id,
        ]);

        $hrAdmin2 = User::create([
            'name' => 'Lisa Brown',
            'email' => 'lisa@vglobal.com',
            'password' => 'password123',
            'role' => 'hr_admin',
            'department_id' => $hrDept->id,
        ]);

        $salesManager = User::create([
            'name' => 'John Smith',
            'email' => 'john@vglobal.com',
            'password' => 'password123',
            'role' => 'dept_manager',
            'department_id' => $salesDept->id,
        ]);

        $itManager = User::create([
            'name' => 'Mary Johnson',
            'email' => 'mary@vglobal.com',
            'password' => 'password123',
            'role' => 'dept_manager',
            'department_id' => $itDept->id,
        ]);

        $salesDept->update(['dept_manager_id' => $salesManager->id]);
        $itDept->update(['dept_manager_id' => $itManager->id]);

        $alice = User::create([
            'name' => 'Alice Brown',
            'email' => 'alice@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'department_id' => $salesDept->id,
            'manager_id' => $salesManager->id,
        ]);

        $bob = User::create([
            'name' => 'Bob Wilson',
            'email' => 'bob@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'department_id' => $salesDept->id,
            'manager_id' => $salesManager->id,
        ]);

        $carol = User::create([
            'name' => 'Carol Davis',
            'email' => 'carol@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'department_id' => $itDept->id,
            'manager_id' => $itManager->id,
        ]);

        $brad = User::create([
            'name' => 'Brad Smith',
            'email' => 'brad@vglobal.com',
            'password' => 'password123',
            'role' => 'employee',
            'department_id' => $itDept->id,
            'manager_id' => $itManager->id,
        ]);

        $allUsers = [
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

        foreach ($allUsers as $user) {
            $credits = $leaveCredits[$user->role] ?? $leaveCredits['employee'];

            foreach ($credits as $leaveType => $balance) {
                LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type' => $leaveType,
                    'balance' => $balance,
                    'used' => 0,
                    'year' => $currentYear,
                ]);
            }
        }

        $this->command->info('Created users with leave credits.');
        $this->command->table(
            ['Name', 'Email', 'Role', 'Vacation Leave', 'Sick Leave'],
            [
                ['David Williams', 'david@vglobal.com', 'ceo', '25 days', '15 days'],
                ['Sarah Johnson', 'sarah@vglobal.com', 'hr_admin', '20 days', '10 days'],
                ['Lisa Brown', 'lisa@vglobal.com', 'hr_admin', '20 days', '10 days'],
                ['John Smith', 'john@vglobal.com', 'dept_manager', '18 days', '10 days'],
                ['Mary Johnson', 'mary@vglobal.com', 'dept_manager', '18 days', '10 days'],
                ['Alice Brown', 'alice@vglobal.com', 'employee', '15 days', '10 days'],
                ['Bob Wilson', 'bob@vglobal.com', 'employee', '15 days', '10 days'],
                ['Carol Davis', 'carol@vglobal.com', 'employee', '15 days', '10 days'],
                ['Brad Smith', 'brad@vglobal.com', 'employee', '15 days', '10 days'],
            ]
        );
    }
}
