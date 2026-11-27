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
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/pshs-logo.png') }}" alt="PSHS logo" class="brand-logo">
            <span>Student Management System</span>
        </a>
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

                {{-- Admin sees every link, under small headings so the two "Students" links are clear. --}}
                @php($isAdmin = auth()->user()->hasRole('admin'))

                @if ($isAdmin || auth()->user()->hasRole('teacher'))
                    @if ($isAdmin) <div class="menu-heading">Teacher</div> @endif
                    @if (Route::has('students.index'))
                        <a href="{{ route('students.index') }}" @class(['active' => request()->routeIs('students.*')])>Students</a>
                    @endif
                    @if (Route::has('subjects.index'))
                        <a href="{{ route('subjects.index') }}" @class(['active' => request()->routeIs('subjects.*', 'marks.*')])>Subjects</a>
                    @endif
                    @if (Route::has('attendance.index'))
                        <a href="{{ route('attendance.index') }}" @class(['active' => request()->routeIs('attendance.*')])>Attendance</a>
                    @endif
                @endif

                @if ($isAdmin || auth()->user()->hasRole('student'))
                    @if ($isAdmin) <div class="menu-heading">Student</div> @endif
                    @if (Route::has('portal.subjects'))
                        <a href="{{ route('portal.subjects') }}" @class(['active' => request()->routeIs('portal.subjects')])>My subjects</a>
                    @endif
                    @if (Route::has('portal.marks'))
                        <a href="{{ route('portal.marks') }}" @class(['active' => request()->routeIs('portal.marks')])>My marks</a>
                    @endif
                    @if (Route::has('portal.attendance'))
                        <a href="{{ route('portal.attendance') }}" @class(['active' => request()->routeIs('portal.attendance')])>My attendance</a>
                    @endif
                @endif

                @if ($isAdmin || auth()->user()->hasRole('principal'))
                    @if ($isAdmin) <div class="menu-heading">Principal</div> @endif
                    @if (Route::has('principal.students'))
                        <a href="{{ route('principal.students') }}" @class(['active' => request()->routeIs('principal.students')])>{{ $isAdmin ? 'All students' : 'Students' }}</a>
                    @endif
                    @if (Route::has('principal.teachers'))
                        <a href="{{ route('principal.teachers') }}" @class(['active' => request()->routeIs('principal.teachers')])>Teachers</a>
                    @endif
                @endif

                {{-- Students and teachers apply for leave. Admin and principal do not. --}}
                @if ((auth()->user()->hasRole('student') || auth()->user()->hasRole('teacher')) && Route::has('leave.index'))
                    <a href="{{ route('leave.index') }}" @class(['active' => request()->routeIs('leave.*')])>My leave</a>
                @endif
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
