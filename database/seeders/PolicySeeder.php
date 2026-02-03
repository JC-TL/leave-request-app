<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        $defaultPolicies = [
            ['leave_type' => 'Vacation Leave', 'annual_entitlement' => 15],
            ['leave_type' => 'Sick Leave', 'annual_entitlement' => 10],
            ['leave_type' => 'Emergency Leave', 'annual_entitlement' => 3],
            ['leave_type' => 'Maternity Leave', 'annual_entitlement' => 0],
            ['leave_type' => 'Paternity Leave', 'annual_entitlement' => 0],
            ['leave_type' => 'Bereavement Leave', 'annual_entitlement' => 5],
            ['leave_type' => 'Special Leave', 'annual_entitlement' => 3],
            ['leave_type' => 'Unpaid Leave', 'annual_entitlement' => 0],
        ];

        foreach ($defaultPolicies as $policy) {
            Policy::create($policy);
        }
    }
}
