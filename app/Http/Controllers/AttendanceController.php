<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Section;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    // The sheet: a teacher sees the section they advise, the admin can pick any section.
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $sections = Section::orderBy('grade_level')->orderBy('name')->get();
            $section = $sections->firstWhere('id', (int) $request->query('section')) ?? $sections->first();
        } else {
            $sections = collect();
            $section = $user->advisedSection;
        }

        // The date is today unless a valid day that is not in the future was asked for.
        $date = $request->query('date');
        if (! is_string($date) || ! Carbon::canBeCreatedFromFormat($date, 'Y-m-d') || $date > today()->toDateString()) {
            $date = today()->toDateString();
        }

        $students = collect();
        $statuses = collect();

        if ($section) {
            Gate::authorize('record', [Attendance::class, $section]);

            $students = $section->students()->with('user')->get()->sortBy(fn ($student) => $student->user->name)->values();
            $statuses = Attendance::whereIn('student_id', $students->pluck('id'))
                ->where('date', $date)
                ->pluck('status', 'student_id');
        }

        return view('attendance.index', [
            'section' => $section,
            'sections' => $sections,
            'students' => $students,
            'statuses' => $statuses,
            'date' => $date,
        ]);
    }

    // The service saves the day's rows; the Policy decides who may do it for this section.
    public function store(StoreAttendanceRequest $request, AttendanceService $service): RedirectResponse
    {
        $data = $request->validated();
        $section = Section::findOrFail($data['section_id']);

        Gate::authorize('record', [Attendance::class, $section]);

        $service->record($section, $data['date'], $data['statuses'], $request->user());

        return redirect()
            ->route('attendance.index', ['section' => $section->id, 'date' => $data['date']])
            ->with('status', 'Attendance saved.');
    }
}
