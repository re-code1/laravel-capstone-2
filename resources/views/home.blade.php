@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="card">
        <h1>Student Management System</h1>
        <p>Welcome. This is the starter page for our project.</p>
        <a href="{{ route('login') }}" class="btn">Go to Login</a>
    </section>
@endsection
