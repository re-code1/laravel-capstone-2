<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Save one day's attendance for every student of a section.
     * There is one row per student per day, so recording the same day again
     * updates the existing rows instead of adding new ones.
     *
     * @param  array<int|string, string>  $statuses  student id => present|absent|late|excused
     */
    public function record(Section $section, string $date, array $statuses, User $recorder): void
    {
        DB::transaction(function () use ($section, $date, $statuses, $recorder) {
            $studentIds = $section->students()->pluck('id')->all();

            foreach ($statuses as $studentId => $status) {
                // Only students of this section are saved, even if the caller sends more.
                if (! in_array((int) $studentId, $studentIds, true)) {
                    continue;
                }

                $attendance = Attendance::firstOrNew([
                    'student_id' => (int) $studentId,
                    'date' => $date,
                ]);
                $attendance->status = $status;
                $attendance->recorded_by = $recorder->id; // set in code, never from the form
                $attendance->save();
            }
        });
    }
}
