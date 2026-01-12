<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'manager_id',
    ];

    /**
     * Tid (PK)             
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(Request::class, 'employee_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(Balance::class);
    }

    // Role checking methods
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

    // Get leave balance for a specific type and year
    public function getLeaveBalance(string $leaveType, int $year = null): ?Balance
    {
        $year = $year ?? now()->year;
        return $this->leaveBalances()
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->first();
    }
}


