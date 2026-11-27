<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Student Management System')</title>
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
</head>
<body>
    <header class="site-header">
        <a href="{{ url('/') }}" class="brand">Student Management System</a>
        @auth
            <div class="user-menu">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ url('/logout') }}" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Log out</button>
                </form>
            </div>
        @endauth
        @guest
            <div class="user-menu">
                <a href="{{ url('/login') }}" class="btn btn-secondary">Log in</a>
            </div>
        @endguest
    </header>

    <div class="app-shell">
        @auth
            <nav class="sidebar" aria-label="Main menu">
                <a href="{{ url('/') }}" @class(['active' => request()->is('/')])>Home</a>
                {{-- Seat 5: student portal links (added with seat 2's permission). --}}
                @if (auth()->user()->hasRole('student'))
                    <a href="{{ route('portal.subjects') }}" @class(['active' => request()->routeIs('portal.subjects')])>My subjects</a>
                    <a href="{{ route('portal.marks') }}" @class(['active' => request()->routeIs('portal.marks')])>My marks</a>
                @endif
                {{-- Menu goes here. The real role menu comes in Wave 3. --}}
                @unless (auth()->user()->hasRole('student'))
                    <div class="menu-note">Menu goes here</div>
                @endunless
            </nav>
        @endauth

        <main class="main">
            @if (session('status')) <div class="alert alert-success" role="status">{{ session('status') }}</div> @endif
            @if (session('error'))  <div class="alert alert-danger" role="alert">{{ session('error') }}</div>  @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
