<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyAttendanceController extends Controller
{
    // Always the logged-in student's own records: no id comes from the URL.
    public function __invoke(Request $request): View
    {
        $student = $request->user()->student;

        // The admin has no student record, so the page shows a short message.
        $attendances = $student
            ? $student->attendances()->orderByDesc('date')->get()
            : collect();

        // How many days of each status, in a fixed order (zero when there are none).
        $counts = collect(Attendance::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => $attendances->where('status', $status)->count()]);

        return view('attendance.mine', [
            'student' => $student,
            'attendances' => $attendances,
            'counts' => $counts,
        ]);
    }
}
