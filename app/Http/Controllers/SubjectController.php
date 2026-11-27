<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// ASSUMPTION (Seat 1): subjects table has `name` and `teacher_id` (points to users.id),
// and Subject::$fillable includes both. Adjust once feat/schema is merged.
class SubjectController extends Controller
{
    // List only the subjects the logged-in teacher teaches.
    public function index(Request $request): View
    {
        $subjects = Subject::where('teacher_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        return view('subjects.index', ['subjects' => $subjects]);
    }

    public function create(): View
    {
        return view('subjects.create');
    }

    // The teacher comes from the login, never from the form.
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create([
            'name' => $request->validated('name'),
            'teacher_id' => $request->user()->id,
        ]);

        return redirect()->route('subjects.index')->with('status', 'Subject added.');
    }
}
