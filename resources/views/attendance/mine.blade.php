@extends('layouts.app')

@section('title', 'My attendance')

@section('content')
    <div class="page-header">
        <h1>My attendance</h1>
    </div>

    @if (! $student)
        <div class="empty-state">Only students have attendance records.</div>
    @elseif ($attendances->isEmpty())
        <div class="empty-state">No attendance recorded yet.</div>
    @else
        <p class="text-muted">{{ $counts->map(fn ($count, $status) => ucfirst($status).' '.$count)->implode(', ') }}</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date->format('D, M j, Y') }}</td>
                            <td><span class="badge badge-{{ $attendance->status }}">{{ ucfirst($attendance->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
