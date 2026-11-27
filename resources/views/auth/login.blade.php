@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="card card-narrow">
        <h1>Log in</h1>

        {{-- Form only for now: no action yet, real login comes in Wave 1. --}}
        <form>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="input @error('email') input-error @enderror"
                   value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" class="input" required>

            <div class="form-actions">
                <button type="button" class="btn btn-primary">Log in</button>
            </div>
        </form>
    </div>
@endsection
