@extends('layouts.app')

@section('title', 'My leave requests')

@section('content')
    <div class="page-header">
        <h1>My leave requests</h1>
        <a href="{{ route('leave.create') }}" class="btn btn-compose">+ New leave request</a>
    </div>

    @if ($leaveRequests->isEmpty())
        <div class="empty-state">No leave requests yet.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Sent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaveRequests as $leaveRequest)
                        <tr>
                            <td>{{ $leaveRequest->start_date->format('M j, Y') }}</td>
                            <td>{{ $leaveRequest->end_date->format('M j, Y') }}</td>
                            <td>{{ $leaveRequest->reason }}</td>
                            <td><span class="badge badge-{{ $leaveRequest->status }}">{{ ucfirst($leaveRequest->status) }}</span></td>
                            <td>{{ $leaveRequest->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
