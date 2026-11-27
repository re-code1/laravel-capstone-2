@extends('layouts.app')

@section('title', 'Marks: '.$subject->name)

@section('content')
    <section class="card">
        <h1>{{ $subject->code }} {{ $subject->name }}</h1>
        <p>{{ $subject->section->fullName() }}</p>

        <p>
            Quarter:
            @foreach ($quarters as $q)
                @if ($q === $quarter)
                    <strong>{{ $q }}</strong>
                @else
                    <a href="{{ route('marks.edit', ['subject' => $subject, 'quarter' => $q]) }}">{{ $q }}</a>
                @endif
            @endforeach
        </p>

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        @error('scores')
            <p>{{ $message }}</p>
        @enderror

        @if ($students->isEmpty())
            <p>There are no students in this section yet.</p>
        @else
            <p>Enter a score from 0 to 100 (up to 2 decimals). Leave it empty if there is no mark yet.</p>

            <form method="POST" action="{{ route('marks.update', $subject) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="quarter" value="{{ $quarter }}">

                <table>
                    <thead>
                        <tr>
                            <th>Student no.</th>
                            <th>Name</th>
                            <th>Quarter {{ $quarter }} score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->student_no }}</td>
                                <td><label for="score-{{ $student->id }}">{{ $student->user->name }}</label></td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100"
                                           id="score-{{ $student->id }}"
                                           name="scores[{{ $student->id }}]"
                                           value="{{ old('scores.'.$student->id, $student->marks->first()?->score) }}">
                                    @error('scores.'.$student->id)
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
