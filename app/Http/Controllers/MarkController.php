<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMarksRequest;
use App\Models\Mark;
use App\Models\Subject;
use App\Services\MarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MarkController extends Controller
{
    // Show the marks form for one subject.
    public function edit(Subject $subject): View
    {
        Gate::authorize('enter', [Mark::class, $subject]);

        // ASSUMPTION (Seat 1): Subject::students() is the enrollment relationship.
        // If Student::fullName() reads from the user row, change this to ->with('user')->get().
        $students = $subject->students()->get();

        // One query for all grades: [studentId => grade].
        $grades = Mark::where('subject_id', $subject->id)->pluck('grade', 'student_id');

        return view('marks.edit', [
            'subject' => $subject,
            'students' => $students,
            'grades' => $grades,
        ]);
    }

    // Save the marks form.
    public function update(UpdateMarksRequest $request, Subject $subject, MarkService $marks): RedirectResponse
    {
        Gate::authorize('enter', [Mark::class, $subject]);

        $marks->saveMarks($subject, $request->validated('grades'));

        return redirect()->route('marks.edit', $subject)->with('status', 'Marks saved.');
    }
}
