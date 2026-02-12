<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->char('gender', 1)->nullable();
            $table->string('email', 100);
            $table->string('password');
            $table->string('role', 30);
            $table->unsignedBigInteger('dept_id');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->timestamps();

            $table->foreign('dept_id')->references('dept_id')->on('departments')->onDelete('cascade');
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
