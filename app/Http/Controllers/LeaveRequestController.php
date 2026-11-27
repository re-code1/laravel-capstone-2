<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    /** My leave requests: only the logged-in user's own, newest first. */
    public function index(Request $request): View
    {
        $leaveRequests = $request->user()->leaveRequests()->latest()->get();

        return view('leave.index', ['leaveRequests' => $leaveRequests]);
    }

    public function create(): View
    {
        return view('leave.create');
    }

    /** user_id comes from the relation and status from the database default (pending). */
    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $request->user()->leaveRequests()->create($request->validated());

        return redirect('/leave')->with('status', 'Leave request sent.');
    }
}
