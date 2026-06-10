{{--
    DEPRECATED: This layout is no longer used by any dashboard pages.
    All pages have been migrated to layouts/sidebar.blade.php (2026-06-09).
    Kept here for reference only. Safe to delete once the sidebar layout is verified.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Pantas Library')</title>

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-responsive.css') }}">
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    @stack('styles')
</head>

<body>

    {{-- NAVBAR --}}
    <div class="d-flex align-items-center px-4 py-2 flex-wrap staff-top-bar" style="background-color: white; position: relative;">
        <img src="{{ asset('images/pantasLogo.png') }}" alt="New Logo" class="header-logo-img" />
        <h1 class="school-name mb-0 ms-2"></h1>

        <button type="button" id="customMenuToggle" class="d-lg-none toggle-btn" aria-label="Open menu">&#9776;</button>

        <div id="routeWrapper" class="d-flex gap-2 flex-wrap ms-lg-auto responsive-nav">
            <button type="button" id="customMenuClose" class="d-lg-none close-btn" aria-label="Close menu">&times;</button>

            <a href="{{ route('book.index') }}" class="btn0 btn-sm">Home</a>

            <div class="attendance_dropdown">
                <button class="attendance_dropdown-button">Attendance</button>
                <div class="attendance_dropdown-content">
                    <a href="{{ route('attendance.scan') }}">Attendance</a>
                    <a href="{{ route('attendance_logs.index') }}">Attendance-logs</a>
                    <a href="{{ route('attendance.changeVideo') }}">Change Video</a>
                </div>
            </div>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Data</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('students.index') }}">Student Data</a>
                    <a href="{{ route('employees.index') }}">Faculty &amp; Staff Data</a>
                </div>
            </div>

            <a href="{{ route('landing') }}" class="btn2 btn-sm">OPAC</a>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Admin</button>
                <div class="logs_dropdown-content">
                    @can('isAdmin')
                    <a href="{{ route('users.index') }}">View Pantas Users</a>
                    @endcan
                </div>
            </div>

            <a href="{{ route('prospectus.index') }}" class="btn3 btn-sm">Prospectus Manager</a>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Circulation</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('logs.index') }}">Circulation</a>
                    <a href="{{ route('book.report.download') }}">Download Book Report</a>
                    <a href="{{ route('students.report') }}">Student Report</a>
                </div>
            </div>

            <a href="{{ route('files.index') }}" class="btn4 btn-sm">Repository</a>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Room Reservations</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('rooms.index') }}">Manage Rooms</a>
                    <a href="{{ route('rooms.book') }}">Book a Room</a>
                    <a href="{{ route('rooms.schedule') }}">View Schedule</a>
                    <a href="{{ route('rooms.pending') }}">Pending Reservations</a>
                    <a href="{{ route('rooms.logs') }}">Reservation Logs</a>
                    <a href="{{ route('admin.attendance.feedbacks') }}">Attendance feedback</a>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn5">Logout</button>
            </form>

        </div>
    </div>

    {{-- BANNER --}}
    <div class="head">
        <img src="{{ asset('images/Bannernew.jpg') }}" alt="Banner" class="banner-img">
    </div>

    {{-- MAIN CONTENT --}}
    <div class="container py-3">
        @yield('content')
    </div>


    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/site-nav.js') }}"></script>

    @stack('scripts')
</body>
</html>
