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
            <ul>
                @foreach ($subjects as $subject)
                    <li>
                        {{ $subject->name }}
                        <a href="{{ route('marks.edit', $subject) }}">Enter marks</a>
                    </li>
                @endforeach
            </ul>
        @endif

        <a href="{{ route('subjects.create') }}" class="btn">Add a subject</a>
    </section>
@endsection
