<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->unsignedInteger('default_duration_days')->nullable()->after('annual_entitlement');
        });

        DB::table('leave_types')
            ->where('leave_type', 'Paternity Leave')
            ->update(['default_duration_days' => 5]);

        DB::table('leave_types')
            ->where('leave_type', 'Maternity Leave')
            ->update(['default_duration_days' => null]);
    }

    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('default_duration_days');
        });
    }
};
