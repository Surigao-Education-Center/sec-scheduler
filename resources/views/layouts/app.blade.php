<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Room Scheduling' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/scheduling.css') }}">
</head>
<body>
    <div class="app-shell">
        <header class="topbar">
            <div class="brand-wrap">
                <a class="brand" href="{{ route('roomscheduling.timetable') }}">
                    <span class="brand-mark">RS</span>
                    <span><strong>Room</strong><small>Scheduling</small></span>
                </a>
            </div>

            <nav class="nav-list" aria-label="Main navigation">
                <a class="nav-link {{ request()->routeIs('roomscheduling.timetable') ? 'active' : '' }}" href="{{ route('roomscheduling.timetable') }}"><span>▦</span> Timetable</a>
                <a class="nav-link {{ request()->routeIs('roomscheduling.reports.*') ? 'active' : '' }}" href="{{ route('roomscheduling.reports.index') }}"><span>◫</span> Reports</a>
                <a class="nav-link {{ request()->routeIs('roomscheduling.schedules.*') ? 'active' : '' }}" href="{{ route('roomscheduling.schedules.index') }}"><span>◷</span> Schedule slots</a>
                <a class="nav-link {{ request()->routeIs('roomscheduling.rooms.*') ? 'active' : '' }}" href="{{ route('roomscheduling.rooms.index') }}"><span>⌂</span> Rooms</a>
                <a class="nav-link {{ request()->routeIs('roomscheduling.instructors.*') ? 'active' : '' }}" href="{{ route('roomscheduling.instructors.index') }}"><span>◎</span> Instructors</a>
                <a class="nav-link {{ request()->routeIs('roomscheduling.sections.*') ? 'active' : '' }}" href="{{ route('roomscheduling.sections.index') }}"><span>≡</span> Sections</a>
                <a class="nav-link {{ request()->routeIs('roomscheduling.subjects.*') ? 'active' : '' }}" href="{{ route('roomscheduling.subjects.index') }}"><span>◇</span> Subjects</a>
            </nav>

            <div class="topbar-actions">
                <div class="topbar-context">Academic operations <span>/</span> {{ now()->format('l, M j') }}</div>
                <div class="user-chip"><span>{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span> {{ auth()->user()->name ?? 'Administrator' }}</div>
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>