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
     * The attributes that should be hidden for serialization.
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

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function isDeptManager()
    {
        return $this->role === 'dept_manager';
    }

    public function isHRAdmin()
    {
        return $this->role === 'hr_admin';
    }

    public function isCEO()
    {
        return $this->role === 'ceo';
    }

    // Get leave balance for a specific leave type and year
    public function getLeaveBalance($leaveType, $year = null)
    {
        if ($year === null) {
            $year = now()->year;
        }
        
        return $this->leaveBalances()
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->first();
    }
}


