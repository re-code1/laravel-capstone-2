@extends('layouts.app')

@section('title', 'Add a Student')

@section('content')
    <div class="card card-narrow">
        <h1>Add a Student</h1>

        <form method="POST" action="{{ route('students.store') }}">
            @csrf

            <label for="name">Full name</label>
            <input type="text" id="name" name="name" maxlength="255" required
                   class="input @error('name') input-error @enderror" value="{{ old('name') }}">
            @error('name') <div class="field-error">{{ $message }}</div> @enderror

            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="255" required
                   class="input @error('email') input-error @enderror" value="{{ old('email') }}">
            @error('email') <div class="field-error">{{ $message }}</div> @enderror

            <label for="student_no">Student number (for example 2026-0013)</label>
            <input type="text" id="student_no" name="student_no" maxlength="20" required
                   class="input @error('student_no') input-error @enderror" value="{{ old('student_no') }}">
            @error('student_no') <div class="field-error">{{ $message }}</div> @enderror

            <label for="section_id">Section</label>
            <select id="section_id" name="section_id" class="input @error('section_id') input-error @enderror" required>
                <option value="">Choose a section</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                        {{ $section->fullName() }}
                    </option>
                @endforeach
            </select>
            @error('section_id') <div class="field-error">{{ $message }}</div> @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <p class="text-muted text-small">The student logs in with this email and the default password.</p>
    </div>
@endsection
