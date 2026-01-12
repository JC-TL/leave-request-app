<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('leave_type', [
                'Vacation Leave',
                'Sick Leave',
                'Emergency Leave',
                'Maternity Leave',
                'Paternity Leave',
                'Bereavement Leave',
                'Special Leave',
                'Unpaid Leave'
            ]);
            $table->decimal('balance', 5, 2);
            $table->decimal('used', 5, 2)->default(0);
            $table->integer('year');
            $table->timestamps();
            
            // One record per user, leave type, and year
            $table->unique(['user_id', 'leave_type', 'year']);
            
            // Indexes
            $table->index('user_id');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};