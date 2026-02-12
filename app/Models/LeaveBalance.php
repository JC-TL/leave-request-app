<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'leave_balance';
    protected $primaryKey = 'balance_id';

    protected $fillable = [
        'emp_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
    ];

    protected function casts(): array
    {
        return [
            'allocated_days' => 'integer',
            'used_days' => 'integer',
            'year' => 'integer',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function getAvailableBalance(): int
    {
        return max(0, $this->allocated_days - $this->used_days);
    }

    public function deductDays(int $days): bool
    {
        if (!$this->hasSufficientBalance($days)) {
            return false;
        }

        $this->used_days += $days;
        return $this->save();
    }

    public function addDays(int $days): bool
    {
        $this->used_days = max(0, $this->used_days - $days);
        return $this->save();
    }

    public function hasSufficientBalance(int $days): bool
    {
        return $this->getAvailableBalance() >= $days;
    }
}
