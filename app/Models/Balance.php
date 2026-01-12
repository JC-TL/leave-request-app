<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type',
        'balance',
        'used',
        'year'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function getAvailableBalance(): float
    {
        return max(0, $this->balance - $this->used);
    }

    public function deductDays(float $days): bool
    {
        if (!$this->hasSufficientBalance($days)) {
            return false;
        }

        $this->used += $days;
        return $this->save();
    }

    public function addDays(float $days): bool
    {
        $this->used = max(0, $this->used - $days);
        return $this->save();
    }

    public function hasSufficientBalance(float $days): bool
    {
        return $this->getAvailableBalance() >= $days;
    }
}

