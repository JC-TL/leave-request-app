<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LeaveTypeSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('Seeding complete!');
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
