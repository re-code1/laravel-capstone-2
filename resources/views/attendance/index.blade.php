@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <div class="page-header">
        <h1>Attendance</h1>
    </div>

    @if (! $section)
        <div class="empty-state">
            {{ auth()->user()->hasRole('admin') ? 'No sections yet.' : 'You are not an adviser of a section.' }}
        </div>
    @else
        {{-- Choose the day (and, for the admin, the section). This reloads the sheet. --}}
        <div class="card">
            <form method="GET" action="{{ route('attendance.index') }}">
                @if ($sections->isNotEmpty())
                    <label for="section">Section</label>
                    <select id="section" name="section" class="input">
                        @foreach ($sections as $option)
                            <option value="{{ $option->id }}" @selected($option->id === $section->id)>{{ $option->fullName() }}</option>
                        @endforeach
                    </select>
                @else
                    <p><strong>{{ $section->fullName() }}</strong></p>
                @endif

                <label for="date">Date</label>
                <input id="date" name="date" type="date" max="{{ today()->toDateString() }}"
                       class="input" value="{{ $date }}">

                <p class="text-muted text-small">Pick the date first, then set each student's status below.</p>

                <div class="form-actions">
                    <button type="submit" class="btn btn-secondary">Show this day</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>{{ $section->fullName() }}, {{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}</h2>

            @if ($students->isEmpty())
                <div class="empty-state">No students in this section yet.</div>
            @else
                <form method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                    <input type="hidden" name="date" value="{{ $date }}">

                    @error('statuses') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror
                    @error('date') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student no.</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    <tr>
                                        <td>{{ $student->student_no }}</td>
                                        <td>{{ $student->user->name }}</td>
                                        <td>
                                            <label for="status-{{ $student->id }}" class="sr-only">Status for {{ $student->user->name }}</label>
                                            <select id="status-{{ $student->id }}" name="statuses[{{ $student->id }}]" class="input">
                                                @foreach (\App\Models\Attendance::STATUSES as $status)
                                                    <option value="{{ $status }}"
                                                        @selected(old('statuses.'.$student->id, $statuses[$student->id] ?? 'present') === $status)>
                                                        {{ ucfirst($status) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save attendance</button>
                    </div>
                </form>
            @endif
        </div>
    @endif
@endsection
