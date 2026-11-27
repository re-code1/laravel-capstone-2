@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')
    <section class="card">
        <h1>My Subjects</h1>

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        @if ($subjects->isEmpty())
            <p>You have no subjects yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Section</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->section->fullName() }}</td>
                            <td><a href="{{ route('marks.edit', $subject) }}">Enter marks</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('subjects.create') }}" class="btn">Add a subject</a>
    </section>
@endsection
