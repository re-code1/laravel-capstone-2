@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="page-header">
        <h1>Welcome, {{ auth()->user()->name }}</h1>
    </div>
    <p>You are logged in as <strong>{{ auth()->user()->role }}</strong>.</p>

    @if (Route::has('students.index'))
        <div class="card">
            <h2>Students</h2>
            <p>See your students and add new ones.</p>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Open students</a>
        </div>
    @endif

    @if (Route::has('subjects.index'))
        <div class="card">
            <h2>Subjects and marks</h2>
            <p>See your subjects and enter marks for each one.</p>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Open subjects</a>
        </div>
    @endif

    @if (Route::has('attendance.index'))
        <div class="card">
            <h2>Attendance</h2>
            <p>Record attendance for the section you advise.</p>
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Open attendance</a>
        </div>
    @endif
@endsection
