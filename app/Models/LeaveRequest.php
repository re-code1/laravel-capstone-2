<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Only what the requester types is fillable. 'user_id' comes from the logged-in user;
// 'status' and the decided_* columns are set only when someone decides.
#[Fillable(['start_date', 'end_date', 'reason'])]
class LeaveRequest extends Model
{
    use HasFactory;

    /** The allowed statuses. Same list as the ENUM in the leave_requests migration. */
    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    /** Small helper: a request can be decided only once, while it is pending. */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** The requester (a student or a teacher). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Who approved or rejected it. */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
