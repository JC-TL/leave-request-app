<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $primaryKey = 'leave_type_id';

    protected $fillable = [
        'leave_type',
        'annual_entitlement',
        'default_duration_days',
    ];

    protected function casts(): array
    {
        return [
            'annual_entitlement' => 'integer',
            'default_duration_days' => 'integer',
        ];
    }

    public function isPaternity(): bool
    {
        return $this->leave_type === 'Paternity Leave';
    }

    public function isMaternity(): bool
    {
        return $this->leave_type === 'Maternity Leave';
    }

    public function isCreditedOnApproval(): bool
    {
        return $this->isPaternity() || $this->isMaternity();
    }

    public function isStartDateOnly(): bool
    {
        return ($this->isPaternity() || $this->isMaternity()) && $this->default_duration_days > 0;
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
