@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <section class="card card-narrow">
        <h1>Login</h1>

        {{-- Form only for now: no action yet, real login comes in a later step. --}}
        <form>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com">

            <label for="password">Password</label>
            <input type="password" id="password" name="password">

            <button type="button" class="btn">Log in</button>
        </form>
    </section>
@endsection
