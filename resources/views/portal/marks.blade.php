@extends('layouts.app')

@section('title', 'My marks')

@section('content')
    <div class="page-header">
        <div>
            <h1>My marks</h1>
            <p>{{ $student->user->name }} &middot; {{ $student->section->fullName() }}</p>
        </div>
    </div>

    @if ($marks->isEmpty())
        <div class="empty-state">No marks yet.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Quarter</th>
                        <th class="num">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($marks as $mark)
                        <tr>
                            <td>{{ $mark->subject->code }} &ndash; {{ $mark->subject->name }}</td>
                            <td>Q{{ $mark->quarter }}</td>
                            <td class="num">{{ $mark->score ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <p><a href="{{ route('portal.subjects') }}">See my subjects</a></p>
@endsection
