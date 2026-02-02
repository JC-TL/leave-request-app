<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::create([
            'name' => 'HR',
            'color' => '#3b82f6',
            'dept_manager_id' => null,
        ]);

        Department::create([
            'name' => 'Sales',
            'color' => '#10b981',
            'dept_manager_id' => null,
        ]);

        Department::create([
            'name' => 'IT',
            'color' => '#8b5cf6',
            'dept_manager_id' => null,
        ]);
    }
}
