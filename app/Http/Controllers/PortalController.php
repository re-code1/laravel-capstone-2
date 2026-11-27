<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Seat 5: the student portal. A student only ever sees their own data, because
// the student always comes from the login, never from the URL.
class PortalController extends Controller
{
    // My subjects: every subject in the student's section, with its teacher.
    public function subjects(Request $request): View
    {
        $student = $this->currentStudent($request);

        $subjects = $student->section->subjects()
            ->with('teacher')
            ->orderBy('code')
            ->get();

        return view('portal.subjects', [
            'student' => $student,
            'subjects' => $subjects,
        ]);
    }

    // My marks: only this student's marks, with the subject of each one.
    public function marks(Request $request): View
    {
        $student = $this->currentStudent($request);

        $marks = $student->marks()
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('quarter')
            ->get();

        return view('portal.marks', [
            'student' => $student,
            'marks' => $marks,
        ]);
    }

    // The logged-in user's student record, or 404 if there is none.
    private function currentStudent(Request $request): Student
    {
        $student = $request->user()->student;

        abort_if($student === null, 404);

        return $student->load(['user', 'section']);
    }
}
