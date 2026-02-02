<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'number_of_days',
        'status',
        'approved_by_dept_manager_id',
        'dept_manager_comment',
        'approved_by_dept_at',
        'approved_by_hr_id',
        'hr_comment',
        'approved_by_hr_at',
        'rejected_at',
    ];

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

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function departmentManager()
    {
        return $this->belongsTo(User::class, 'approved_by_dept_manager_id');
    }

    public function hrApprover()
    {
        return $this->belongsTo(User::class, 'approved_by_hr_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDeptManagerApproved()
    {
        return $this->status === 'dept_manager_approved';
    }

    public function isApproved()
    {
        return $this->status === 'hr_approved';
    }

    public function isRejected()
    {
        return in_array($this->status, ['dept_manager_rejected', 'hr_rejected'], true);
    }

    // Get human-readable status label
    public function getStatusLabel()
    {
        return match($this->status) {
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
        return $query->where('employee_id', $employeeId);
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
