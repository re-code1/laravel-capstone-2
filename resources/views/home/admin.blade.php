@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="page-header">
        <h1>Welcome, {{ auth()->user()->name }}</h1>
    </div>
    <p>You are logged in as <strong>{{ auth()->user()->role }}</strong>. You can open every page.</p>

    {{-- Teacher pages --}}
    @if (Route::has('students.index'))
        <div class="card">
            <h2>Students</h2>
            <p>See students and add new ones.</p>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Open students</a>
        </div>
    @endif

    @if (Route::has('subjects.index'))
        <div class="card">
            <h2>Subjects and marks</h2>
            <p>See subjects and enter marks for each one.</p>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Open subjects</a>
        </div>
    @endif

    {{-- Student pages --}}
    @if (Route::has('portal.subjects'))
        <div class="card">
            <h2>My subjects</h2>
            <p>The student view of subjects.</p>
            <a href="{{ route('portal.subjects') }}" class="btn btn-secondary">Open my subjects</a>
        </div>
    @endif

    @if (Route::has('portal.marks'))
        <div class="card">
            <h2>My marks</h2>
            <p>The student view of marks.</p>
            <a href="{{ route('portal.marks') }}" class="btn btn-secondary">Open my marks</a>
        </div>
    @endif

    {{-- Principal pages --}}
    @if (Route::has('principal.students'))
        <div class="card">
            <h2>All students (principal)</h2>
            <p>The principal's read-only list of every student.</p>
            <a href="{{ route('principal.students') }}" class="btn btn-secondary">Open all students</a>
        </div>
    @endif

    @if (Route::has('principal.teachers'))
        <div class="card">
            <h2>Teachers (principal)</h2>
            <p>The principal's read-only list of every teacher.</p>
            <a href="{{ route('principal.teachers') }}" class="btn btn-secondary">Open teachers</a>
        </div>
    @endif
@endsection
