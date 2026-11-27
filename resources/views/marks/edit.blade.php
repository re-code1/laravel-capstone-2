@extends('layouts.app')

@section('title', 'Marks: '.$subject->name)

@section('content')
    <section class="card">
        <h1>Marks for {{ $subject->name }}</h1>

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        @error('grades')
            <p>{{ $message }}</p>
        @enderror

        @if ($students->isEmpty())
            <p>No students are enrolled in this subject yet.</p>
        @else
            <p>Enter a grade from 60 to 100 (up to 2 decimals). Leave it empty if there is no grade yet.</p>

            <form method="POST" action="{{ route('marks.update', $subject) }}">
                @csrf
                @method('PUT')

                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td><label for="grade-{{ $student->id }}">{{ $student->fullName() }}</label></td>
                                <td>
                                    <input type="number" step="0.01" min="60" max="100"
                                           id="grade-{{ $student->id }}"
                                           name="grades[{{ $student->id }}]"
                                           value="{{ old('grades.'.$student->id, $grades[$student->id] ?? '') }}">
                                    @error('grades.'.$student->id)
                                        <p>{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="btn">Save marks</button>
            </form>
        @endif

        <p><a href="{{ route('subjects.index') }}">Back to my subjects</a></p>
    </section>
@endsection
