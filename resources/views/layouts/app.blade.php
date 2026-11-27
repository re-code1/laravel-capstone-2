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
                {{-- Menu goes here. The real role menu comes in Wave 3. --}}
                <div class="menu-note">Menu goes here</div>
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
