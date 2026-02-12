<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'employees';
    protected $primaryKey = 'emp_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'password',
        'role',
        'dept_id',
        'manager_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['name'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get full name for frontend compatibility (user.name)
     */
    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'emp_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'emp_id');
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isDeptManager(): bool
    {
        return $this->role === 'dept_manager';
    }

    public function isHRAdmin(): bool
    {
        return $this->role === 'hr_admin';
    }

    public function isCEO(): bool
    {
        return $this->role === 'ceo';
    }

    /**
     * Get leave balance for a specific leave type and year
     */
    public function getLeaveBalance(int $leaveTypeId, ?int $year = null): ?LeaveBalance
    {
        if ($year === null) {
            $year = now()->year;
        }

        return $this->leaveBalances()
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    /**
     * Sum of days from pending or manager-approved requests for a leave type (not yet deducted).
     */
    public function getPendingLeaveDays(int $leaveTypeId, ?int $year = null): int
    {
        if ($year === null) {
            $year = now()->year;
        }

        return (int) $this->leaveRequests()
            ->where('leave_type_id', $leaveTypeId)
            ->whereIn('status', ['pending', 'dept_manager_approved'])
            ->whereYear('start_date', $year)
            ->sum('number_of_days');
    }
}
