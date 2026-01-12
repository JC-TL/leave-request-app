<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Balance;
use App\Models\Policy;
use App\Models\Request;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Define leave credits by role
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
                'Vacation Leave' => 18,      // +3 days
                'Sick Leave' => 10,
                'Emergency Leave' => 3,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 3,
                'Unpaid Leave' => 0,
            ],
            'hr_admin' => [
                'Vacation Leave' => 20,      // +5 days
                'Sick Leave' => 10,
                'Emergency Leave' => 3,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 3,
                'Unpaid Leave' => 0,
            ],
            'ceo' => [
                'Vacation Leave' => 25,      // +10 days
                'Sick Leave' => 15,
                'Emergency Leave' => 5,
                'Maternity Leave' => 0,
                'Paternity Leave' => 0,
                'Bereavement Leave' => 5,
                'Special Leave' => 5,
                'Unpaid Leave' => 0,
            ],
        ];

        // Create leave policies
        $defaultPolicies = [
            ['leave_type' => 'Vacation Leave', 'annual_entitlement' => 15],
            ['leave_type' => 'Sick Leave', 'annual_entitlement' => 10],
            ['leave_type' => 'Emergency Leave', 'annual_entitlement' => 3],
            ['leave_type' => 'Maternity Leave', 'annual_entitlement' => 105],
            ['leave_type' => 'Paternity Leave', 'annual_entitlement' => 7],
            ['leave_type' => 'Bereavement Leave', 'annual_entitlement' => 5],
            ['leave_type' => 'Special Leave', 'annual_entitlement' => 3],
            ['leave_type' => 'Unpaid Leave', 'annual_entitlement' => 0],
        ];

        foreach ($defaultPolicies as $policy) {
            Policy::create($policy);
        }

        // Create Departments
        $hrDept = Department::create([
            'name' => 'HR',
            'dept_manager_id' => null,
        ]);

        $salesDept = Department::create([
            'name' => 'Sales',
            'dept_manager_id' => null,
        ]);

        $itDept = Department::create([
            'name' => 'IT',
            'dept_manager_id' => null,
        ]);

        // Create CEO (no department)
        $ceo = User::create([
            'name' => 'David Williams',
            'email' => 'david@vglobal.com',
            'password' => 'password123',
            'role' => 'ceo',
        ]);

        // Create 2 HR Admins (assigned to HR department)
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

        // Create Department Managers
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

        // Update department managers
        $salesDept->update(['dept_manager_id' => $salesManager->id]);
        $itDept->update(['dept_manager_id' => $itManager->id]);

        // Create Employees
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

        // Create leave balances with role-based credits
        $allUsers = [
            $ceo,
            $hrAdmin1,
            $hrAdmin2,
            $salesManager,
            $itManager,
            $alice,
            $bob,
            $carol,
        ];

        $currentYear = now()->year;

        foreach ($allUsers as $user) {
            // Get credits based on role
            $credits = $leaveCredits[$user->role] ?? $leaveCredits['employee'];

            foreach ($credits as $leaveType => $balance) {
                Balance::create([
                    'user_id' => $user->id,
                    'leave_type' => $leaveType,
                    'balance' => $balance,
                    'used' => 0,
                    'year' => $currentYear,
                ]);
            }
        }

        // Log created users for verification
        $this->command->info('Seeding complete!');
        $this->command->info('');
        $this->command->info('Created users with leave credits:');
        $this->command->info('');
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
        
        $this->command->info('');
        $this->command->info('Leave Types Available:');
        $this->command->info('  - Vacation Leave');
        $this->command->info('  - Sick Leave');
        $this->command->info('  - Emergency Leave');
        $this->command->info('  - Maternity Leave');
        $this->command->info('  - Paternity Leave');
        $this->command->info('  - Bereavement Leave');
        $this->command->info('  - Special Leave');
        $this->command->info('  - Unpaid Leave');
    }
}