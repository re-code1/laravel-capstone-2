<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentService;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // Step 1: Display all students
    public function index()
    {
        $students = Student::with('user')->get();

        return view('students.index', compact('students'));
    }

    // Step 2: Display student registration form
    public function create()
    {
        return view('students.create');
    }

    // Step 3: Save a new student
    public function store(StoreStudentRequest $request, StudentService $studentService)
    {
        $studentService->createStudent($request->validated());

        return redirect()
            ->route('students.index')
            ->with('success', 'Student successfully registered!');
    }

    // Step 4: Display student editing form
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    // Step 5: Update existing student
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $student) {

            $student->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            $student->update([
                'student_number' => $data['student_number'],
                'grade_level' => $data['grade_level'],
            ]);

        });

        return redirect()
            ->route('students.index')
            ->with('success', 'Student information updated successfully!');
    }
}
