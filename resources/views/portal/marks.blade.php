@extends('layouts.app')

@section('title', 'My marks')

@section('content')
    <section class="card">
        <h1>My marks</h1>
        <p>{{ $student->user->name }} &middot; {{ $student->section->fullName() }}</p>

        @if ($marks->isEmpty())
            <p>No marks yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Quarter</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($marks as $mark)
                        <tr>
                            <td>{{ $mark->subject->code }} &ndash; {{ $mark->subject->name }}</td>
                            <td>Q{{ $mark->quarter }}</td>
                            <td>{{ $mark->score }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p><a href="{{ route('portal.subjects') }}">See my subjects</a></p>
    </section>
@endsection
