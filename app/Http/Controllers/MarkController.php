<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowMarksRequest;
use App\Http\Requests\UpdateMarksRequest;
use App\Models\Mark;
use App\Models\Subject;
use App\Services\MarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MarkController extends Controller
{
    // Show the marks form for one subject and one quarter.
    public function edit(ShowMarksRequest $request, Subject $subject, MarkService $marks): View
    {
        Gate::authorize('enter', [Mark::class, $subject]);

        $quarter = $request->quarter();

        return view('marks.edit', [
            'subject' => $subject->load('section'),
            'quarter' => $quarter,
            'quarters' => MarkService::QUARTERS,
            'students' => $marks->roster($subject, $quarter),
        ]);
    }

    // Save the marks form.
    public function update(UpdateMarksRequest $request, Subject $subject, MarkService $marks): RedirectResponse
    {
        Gate::authorize('enter', [Mark::class, $subject]);

        $quarter = (int) $request->validated('quarter');
        $marks->saveScores($subject, $quarter, $request->validated('scores'));

        return redirect()
            ->route('marks.edit', ['subject' => $subject, 'quarter' => $quarter])
            ->with('status', 'Marks saved.');
    }
}
