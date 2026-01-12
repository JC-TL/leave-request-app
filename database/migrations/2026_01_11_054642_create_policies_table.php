<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->enum('leave_type', [
                'Vacation Leave',
                'Sick Leave',
                'Emergency Leave',
                'Maternity Leave',
                'Paternity Leave',
                'Bereavement Leave',
                'Special Leave',
                'Unpaid Leave'
            ])->unique();
            $table->integer('annual_entitlement');
            $table->timestamps();  // Changed from timestamp to timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};