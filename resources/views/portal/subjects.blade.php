@extends('layouts.app')

@section('title', 'My subjects')

@section('content')
    <section class="card">
        <h1>My subjects</h1>
        <p>{{ $student->user->name }} &middot; {{ $student->section->fullName() }}</p>

        @if ($subjects->isEmpty())
            <p>No subjects in your section yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->teacher->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p><a href="{{ route('portal.marks') }}">See my marks</a></p>
    </section>
@endsection
