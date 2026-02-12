<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    use HasFactory;

    protected $table = 'pending_registrations';

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
    ];

    protected $appends = ['name'];

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
}
