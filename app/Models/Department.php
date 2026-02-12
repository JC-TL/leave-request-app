<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'dept_id';

    protected $fillable = [
        'name',
        'color',
        'dept_manager_id'
    ];

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'dept_manager_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'dept_id');
    }
}
