@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="page-header">
        <h1>Welcome, {{ auth()->user()->name }}</h1>
    </div>
    <p>You are logged in as <strong>{{ auth()->user()->role }}</strong>.</p>

    @if (Route::has('portal.subjects'))
        <div class="card">
            <h2>My subjects</h2>
            <p>See the subjects you take and who teaches them.</p>
            <a href="{{ route('portal.subjects') }}" class="btn btn-secondary">Open my subjects</a>
        </div>
    @endif

    @if (Route::has('portal.marks'))
        <div class="card">
            <h2>My marks</h2>
            <p>See your marks for each subject.</p>
            <a href="{{ route('portal.marks') }}" class="btn btn-secondary">Open my marks</a>
        </div>
    @endif

    @if (Route::has('portal.attendance'))
        <div class="card">
            <h2>My attendance</h2>
            <p>See the days you were present, absent, late or excused.</p>
            <a href="{{ route('portal.attendance') }}" class="btn btn-secondary">Open my attendance</a>
        </div>
    @endif
@endsection
