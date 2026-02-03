<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestLog extends Model
{
    use HasFactory;

    // Disable updated_at since we only need created_at for logs
    public $timestamps = false;
    
    protected $fillable = [
        'leave_request_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'comment',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relationship to the leave request
     */
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    /**
     * Relationship to the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method to create a log entry
     */
    public static function createLog(
        int $leaveRequestId,
        ?int $userId,
        string $action,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $comment = null
    ): self {
        return self::create([
            'leave_request_id' => $leaveRequestId,
            'user_id' => $userId,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
            'created_at' => now(),
        ]);
    }
}
