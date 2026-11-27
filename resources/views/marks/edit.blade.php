@extends('layouts.app')

@section('title', 'Marks: '.$subject->name)

@section('content')
    <div class="page-header">
        <h1>{{ $subject->code }} {{ $subject->name }}</h1>
        <span class="text-muted">{{ $subject->section->fullName() }}</span>
    </div>

    <nav class="quarter-tabs" aria-label="Quarter">
        @foreach ($quarters as $q)
            <a href="{{ route('marks.edit', ['subject' => $subject, 'quarter' => $q]) }}"
               @class(['active' => $q === $quarter]) @if ($q === $quarter) aria-current="page" @endif>Quarter {{ $q }}</a>
        @endforeach
    </nav>

    @error('scores')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror

    @if ($students->isEmpty())
        <div class="empty-state">There are no students in this section yet.</div>
    @else
        <p class="text-muted text-small">Enter a score from 0 to 100 (up to 2 decimals). Leave it empty if there is no mark yet.</p>

        <form method="POST" action="{{ route('marks.update', $subject) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="quarter" value="{{ $quarter }}">

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student no.</th>
                            <th>Name</th>
                            <th class="num">Quarter {{ $quarter }} score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->student_no }}</td>
                                <td><label for="score-{{ $student->id }}" class="table-label">{{ $student->user->name }}</label></td>
                                <td class="num">
                                    <input type="number" step="0.01" min="0" max="100"
                                           class="input input-score @error('scores.'.$student->id) input-error @enderror"
                                           id="score-{{ $student->id }}"
                                           name="scores[{{ $student->id }}]"
                                           value="{{ old('scores.'.$student->id, $student->marks->first()?->score) }}">
                                    @error('scores.'.$student->id)
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save marks</button>
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Back to my subjects</a>
            </div>
        </form>
    @endif
@endsection
