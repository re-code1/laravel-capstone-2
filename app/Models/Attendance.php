<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// 'recorded_by' is not fillable: it is set in code from the logged-in user.
#[Fillable(['student_id', 'date', 'status'])]
class Attendance extends Model
{
    use HasFactory;

    /** The allowed statuses. Same list as the ENUM in the attendances migration. */
    public const STATUSES = ['present', 'absent', 'late', 'excused'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** The user who recorded this attendance. */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
