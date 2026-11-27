<?php

namespace App\Services;

use App\Models\Mark;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

// ASSUMPTION (Seat 1): marks table has student_id, subject_id, grade (DECIMAL(5,2) NULL),
// and Mark::$fillable includes all three.
class MarkService
{
    /**
     * Save one grade per student for this subject.
     * $grades looks like [studentId => grade or null]; null means not graded yet (INC).
     */
    public function saveMarks(Subject $subject, array $grades): void
    {
        // All marks are saved, or none are.
        DB::transaction(function () use ($subject, $grades) {
            foreach ($grades as $studentId => $grade) {
                Mark::updateOrCreate(
                    ['student_id' => $studentId, 'subject_id' => $subject->id],
                    ['grade' => $grade],
                );
            }
        });
    }
}
