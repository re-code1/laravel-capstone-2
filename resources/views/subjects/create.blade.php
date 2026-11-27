@extends('layouts.app')

@section('title', 'Add a Subject')

@section('content')
    <section class="card card-narrow">
        <h1>Add a Subject</h1>

        <form method="POST" action="{{ route('subjects.store') }}">
            @csrf

            <label for="section_id">Section</label>
            <select id="section_id" name="section_id" required>
                <option value="">Choose a section</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                        {{ $section->fullName() }}
                    </option>
                @endforeach
            </select>
            @error('section_id')
                <p>{{ $message }}</p>
            @enderror

            <label for="code">Subject code (for example MATH7)</label>
            <input type="text" id="code" name="code" value="{{ old('code') }}" maxlength="20" required>
            @error('code')
                <p>{{ $message }}</p>
            @enderror

            <label for="name">Subject name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <p>{{ $message }}</p>
            @enderror

            <button type="submit" class="btn">Save</button>
        </form>

        <p><a href="{{ route('subjects.index') }}">Back to my subjects</a></p>
    </section>
@endsection
