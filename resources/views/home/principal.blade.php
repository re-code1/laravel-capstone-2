@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="page-header">
        <h1>Welcome, {{ auth()->user()->name }}</h1>
    </div>
    <p>You are logged in as <strong>{{ auth()->user()->role }}</strong>.</p>

    @if (Route::has('principal.students'))
        <div class="card">
            <h2>Students</h2>
            <p>See every student in the school.</p>
            <a href="{{ route('principal.students') }}" class="btn btn-secondary">Open students</a>
        </div>
    @endif

    @if (Route::has('principal.teachers'))
        <div class="card">
            <h2>Teachers</h2>
            <p>See every teacher in the school.</p>
            <a href="{{ route('principal.teachers') }}" class="btn btn-secondary">Open teachers</a>
        </div>
    @endif
@endsection
