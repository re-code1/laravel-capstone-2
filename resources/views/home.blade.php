@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="page-header">
        <h1>Student Management System</h1>
    </div>

    <div class="card">
        <h2>Welcome</h2>
        <p>This is the starter page for our project.</p>
        <a href="{{ url('/login') }}" class="btn btn-primary">Go to Login</a>
    </div>
@endsection
