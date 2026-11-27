<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    // List only the subjects the logged-in teacher teaches (sections loaded in one query).
    public function index(Request $request): View
    {
        $subjects = $request->user()->subjectsTaught()
            ->with('section')
            ->orderBy('code')
            ->get();

        return view('subjects.index', ['subjects' => $subjects]);
    }

    // The form needs the list of sections for its dropdown.
    public function create(): View
    {
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();

        return view('subjects.create', ['sections' => $sections]);
    }

    // The teacher comes from the login, never from the form.
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create([
            ...$request->validated(),
            'teacher_id' => $request->user()->id,
        ]);

        return redirect()->route('subjects.index')->with('status', 'Subject added.');
    }
}
