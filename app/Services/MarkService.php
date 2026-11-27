<?php

namespace App\Services;

use App\Models\Mark;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MarkService
{
    public const QUARTERS = [1, 2, 3, 4];

    /**
     * Students of the subject's section, with their user (for the name) and
     * only this subject's mark for this quarter. A fixed number of queries,
     * however many students there are.
     */
    public function roster(Subject $subject, int $quarter): Collection
    {
        return $subject->section->students()
            ->with([
                'user',
                'marks' => fn ($query) => $query
                    ->where('subject_id', $subject->id)
                    ->where('quarter', $quarter),
            ])
            ->get()
            ->sortBy(fn ($student) => $student->user->name)
            ->values();
    }

    /** The ids of the students a teacher may grade for this subject. */
    public function studentIds(Subject $subject): array
    {
        return $subject->section->students()->pluck('id')->all();
    }

    /**
     * Save one quarter of scores. $scores looks like [studentId => score or null].
     * A score creates or updates the mark. An empty box deletes it, so the
     * student goes back to "no mark yet" (INC): the table cannot store a NULL score.
     */
    public function saveScores(Subject $subject, int $quarter, array $scores): void
    {
        // All changes are saved, or none are.
        DB::transaction(function () use ($subject, $quarter, $scores) {
            foreach ($scores as $studentId => $score) {
                $key = [
                    'student_id' => $studentId,
                    'subject_id' => $subject->id,
                    'quarter' => $quarter,
                ];

                if ($score === null || $score === '') {
                    Mark::where($key)->delete();
                } else {
                    Mark::updateOrCreate($key, ['score' => $score]);
                }
            }
        });
    }
}
