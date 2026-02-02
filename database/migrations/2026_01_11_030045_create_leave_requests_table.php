<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
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
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->integer('number_of_days');
            $table->enum('status', [
                'pending',
                'dept_manager_approved',
                'dept_manager_rejected',
                'hr_approved',
                'hr_rejected'
            ])->default('pending');
            
            // Manager approval tracking
            $table->foreignId('approved_by_dept_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('dept_manager_comment')->nullable();
            $table->timestamp('approved_by_dept_at')->nullable();
            
            // HR approval tracking
            $table->foreignId('approved_by_hr_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('hr_comment')->nullable();
            $table->timestamp('approved_by_hr_at')->nullable();
            
            // Rejection tracking
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('employee_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
