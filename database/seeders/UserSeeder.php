<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get all policies from the database (source of truth)
        $policies = Policy::all()->keyBy('leave_type');
        
        // Define role-based multipliers/overrides for leave credits
        // Base is from policies table, but some roles get more
        $roleMultipliers = [
            'employee' => 1.0,      // Uses base policy amounts
            'dept_manager' => 1.2,  // 20% more vacation than base
            'hr_admin' => 1.33,     // 33% more vacation than base
            'ceo' => 1.67,          // 67% more vacation and sick leave
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
            $multiplier = $roleMultipliers[$user->role] ?? 1.0;

            foreach ($policies as $leaveType => $policy) {
                // Calculate balance based on policy and role multiplier
                // Only apply multiplier to vacation and sick leave for senior roles
                $balance = $policy->annual_entitlement;
                
                if (in_array($leaveType, ['Vacation Leave', 'Sick Leave']) && $multiplier > 1.0) {
                    $balance = (int) ceil($policy->annual_entitlement * $multiplier);
                }

                LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type' => $leaveType,
                    'balance' => $balance,
                    'used' => 0,
                    'year' => $currentYear,
                ]);
            }
        }

        // Display summary with actual calculated values
        $baseVacation = $policies['Vacation Leave']->annual_entitlement ?? 15;
        $baseSick = $policies['Sick Leave']->annual_entitlement ?? 10;
        
        $this->command->info('Created users with leave credits (based on policies table).');
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
