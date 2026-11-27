<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Student Management System')</title>
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
</head>
<body>
    <header class="site-header">
        <a href="{{ route('home') }}" class="brand">Student Management System</a>
        <nav>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('login') }}">Login</a>
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>
