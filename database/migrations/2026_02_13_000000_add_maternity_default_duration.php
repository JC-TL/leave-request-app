<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('leave_types')
            ->where('leave_type', 'Maternity Leave')
            ->update(['default_duration_days' => 105]);
    }

    public function down(): void
    {
        DB::table('leave_types')
            ->where('leave_type', 'Maternity Leave')
            ->update(['default_duration_days' => null]);
    }
};
