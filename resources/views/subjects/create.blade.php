@extends('layouts.app')

@section('title', 'Add a Subject')

@section('content')
    <section class="card card-narrow">
        <h1>Add a Subject</h1>

        <form method="POST" action="{{ route('subjects.store') }}">
            @csrf

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
