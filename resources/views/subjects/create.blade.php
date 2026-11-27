@extends('layouts.app')

@section('title', 'Add a Subject')

@section('content')
    <div class="card card-narrow">
        <h1>Add a Subject</h1>

        <form method="POST" action="{{ route('subjects.store') }}">
            @csrf

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

            <label for="code">Subject code (for example MATH7)</label>
            <input type="text" id="code" name="code" maxlength="20" required
                   class="input @error('code') input-error @enderror" value="{{ old('code') }}">
            @error('code') <div class="field-error">{{ $message }}</div> @enderror

            <label for="name">Subject name</label>
            <input type="text" id="name" name="name" required
                   class="input @error('name') input-error @enderror" value="{{ old('name') }}">
            @error('name') <div class="field-error">{{ $message }}</div> @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
