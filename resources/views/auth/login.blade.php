@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="card card-narrow">
        <div class="login-brand">
            <img src="{{ asset('images/pshs-logo.png') }}" alt="PSHS logo">
            <h1 class="login-title">Student Management System</h1>
            <p class="login-sub">Log in to continue</p>
        </div>

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="input @error('email') input-error @enderror"
                   value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" class="input @error('password') input-error @enderror" required>
            @error('password') <div class="field-error">{{ $message }}</div> @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Log in</button>
            </div>
        </form>
    </div>
@endsection
