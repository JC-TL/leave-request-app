<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops old tables and creates new schema (employees, leave_types, leave_balance, etc.)
     */
    public function up(): void
    {
        // Drop old tables - disable FK checks for circular dependency (users <-> departments)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('leave_request_logs');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();

        // 1. Create leave_types (no dependencies)
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id('leave_type_id');
            $table->string('leave_type', 50);
            $table->unsignedInteger('annual_entitlement')->default(0);
            $table->timestamps();
        });

        // 2. Create departments (dept_id as PK)
        Schema::create('departments', function (Blueprint $table) {
            $table->id('dept_id');
            $table->unsignedBigInteger('dept_manager_id')->nullable();
            $table->string('name', 30);
            $table->string('color', 30);
            $table->timestamps();
        });

        // 3. Create employees (emp_id as PK, depends on departments)
        Schema::create('employees', function (Blueprint $table) {
            $table->id('emp_id');
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->char('gender', 1)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('role', 30);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('dept_id')->references('dept_id')->on('departments')->onDelete('set null');
            $table->foreign('manager_id')->references('emp_id')->on('employees')->onDelete('set null');
        });

        // 4. Add dept_manager_id FK to departments
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('dept_manager_id')->references('emp_id')->on('employees')->onDelete('set null');
        });

        // 5. Create sessions (Laravel auth - user_id stores employees.emp_id)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')->references('emp_id')->on('employees')->onDelete('cascade');
        });

        // 6. Create password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 7. Create leave_balance
        Schema::create('leave_balance', function (Blueprint $table) {
            $table->id('balance_id');
            $table->unsignedBigInteger('emp_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->integer('year');
            $table->unsignedInteger('allocated_days');
            $table->unsignedInteger('used_days')->default(0);
            $table->timestamps();

            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('leave_type_id')->on('leave_types')->onDelete('cascade');
            $table->unique(['emp_id', 'leave_type_id', 'year']);
        });

        // 8. Create leave_requests
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('leave_request_id');
            $table->unsignedBigInteger('emp_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason', 255);
            $table->integer('number_of_days');
            $table->string('status', 30)->default('pending');
            $table->string('dept_manager_comment', 255)->nullable();
            $table->string('hr_comment', 255)->nullable();
            $table->timestamp('approved_by_dept_at')->nullable();
            $table->timestamp('approved_by_hr_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('approved_by_dept_manager_id')->nullable();
            $table->unsignedBigInteger('approved_by_hr_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('leave_type_id')->on('leave_types')->onDelete('cascade');
            $table->foreign('approved_by_dept_manager_id')->references('emp_id')->on('employees')->onDelete('set null');
            $table->foreign('approved_by_hr_id')->references('emp_id')->on('employees')->onDelete('set null');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balance');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('leave_types');
    }
};
