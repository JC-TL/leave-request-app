<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            ['leave_type' => 'Vacation Leave', 'annual_entitlement' => 15, 'default_duration_days' => null],
            ['leave_type' => 'Sick Leave', 'annual_entitlement' => 10, 'default_duration_days' => null],
            ['leave_type' => 'Emergency Leave', 'annual_entitlement' => 3, 'default_duration_days' => null],
            ['leave_type' => 'Maternity Leave', 'annual_entitlement' => 0, 'default_duration_days' => 105],
            ['leave_type' => 'Paternity Leave', 'annual_entitlement' => 0, 'default_duration_days' => 5],
            ['leave_type' => 'Bereavement Leave', 'annual_entitlement' => 5, 'default_duration_days' => null],
            ['leave_type' => 'Special Leave', 'annual_entitlement' => 3, 'default_duration_days' => null],
            ['leave_type' => 'Unpaid Leave', 'annual_entitlement' => 0, 'default_duration_days' => null],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::create($lt);
        }
    }
}
