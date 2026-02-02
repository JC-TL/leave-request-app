<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type',
        'balance',
        'used',
        'year'
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'used' => 'integer',
            'year' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate available balance (total - used)
    public function getAvailableBalance(): int
    {
        return max(0, $this->balance - $this->used);
    }

    // Deduct days from balance
    public function deductDays($days)
    {
        if (!$this->hasSufficientBalance($days)) {
            return false;
        }

        $this->used += $days;
        return $this->save();
    }

    // Add days back to balance (for cancellations)
    public function addDays($days)
    {
        $this->used = max(0, $this->used - $days);
        return $this->save();
    }

    // Check if user has enough balance
    public function hasSufficientBalance($days)
    {
        return $this->getAvailableBalance() >= $days;
    }
}
