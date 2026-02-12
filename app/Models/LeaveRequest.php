<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'leave_request_id';

    protected $fillable = [
        'emp_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'number_of_days',
        'status',
        'dept_manager_comment',
        'hr_comment',
        'approved_by_dept_at',
        'approved_by_hr_at',
        'rejected_at',
        'approved_by_dept_manager_id',
        'approved_by_hr_id',
    ];

    protected $appends = ['leave_type_name'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_by_dept_at' => 'datetime',
            'approved_by_hr_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function getLeaveTypeNameAttribute(): string
    {
        return $this->leaveType?->leave_type ?? 'Unknown';
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function departmentManager()
    {
        return $this->belongsTo(Employee::class, 'approved_by_dept_manager_id');
    }

    public function hrApprover()
    {
        return $this->belongsTo(Employee::class, 'approved_by_hr_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDeptManagerApproved(): bool
    {
        return $this->status === 'dept_manager_approved';
    }

    public function isApproved(): bool
    {
        return $this->status === 'hr_approved';
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['dept_manager_rejected', 'hr_rejected'], true);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'dept_manager_approved' => 'Manager Approved',
            'dept_manager_rejected' => 'Manager Rejected',
            'hr_approved' => 'Approved',
            'hr_rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'hr_approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereIn('status', ['dept_manager_rejected', 'hr_rejected']);
    }

    public function scopeByEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('emp_id', $employeeId);
    }

    public function scopeBetweenDates(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }
}
