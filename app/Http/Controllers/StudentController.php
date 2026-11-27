<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Section;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    // List every student. User and section load in one query each, not one per row.
    public function index(): View
    {
        $students = Student::with(['user', 'section'])->orderBy('student_no')->get();

        return view('students.index', ['students' => $students]);
    }

    // The form needs the list of sections for its dropdown.
    public function create(): View
    {
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();

        return view('students.create', ['sections' => $sections]);
    }

    // The service creates the login account and the student record together.
    public function store(StoreStudentRequest $request, StudentService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('students.index')->with('status', 'Student added.');
    }
}
