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
    <title>📚 Book Kiosk</title>

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-responsive.css') }}">
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    @stack('styles')
    @yield('styles')

    <style>
        /* Ensures footer sticks to bottom when page content is short */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
                .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .header-logo-img {
            height: 25px; /* adjust based on your navbar */
            width: auto;
            display: block;
        }
    </style>
</head>

<body>

    <!-- HEADER + BANNER -->
    <div class="d-flex align-items-center px-4 py-2 flex-wrap staff-top-bar" style="background-color: white; position: relative;">
        <a href="{{ route('book.index') }}">
            <img src="{{ asset('images/pantasLogo.png') }}" alt="New Logo" class="header-logo-img" />
        </a>
        <h1 class="school-name mb-0 ms-2"></h1>

        <button type="button" id="customMenuToggle" class="d-lg-none toggle-btn" aria-label="Open menu">&#9776;</button>

        <div id="routeWrapper" class="d-flex gap-2 flex-wrap ms-lg-auto responsive-nav">
            <button type="button" id="customMenuClose" class="d-lg-none close-btn" aria-label="Close menu">&times;</button>

            <a href="{{ route('book.index') }}" id="home" class="btn0 btn-sm">Home</a>

            <div class="attendance_dropdown">
                <button class="attendance_dropdown-button">Attendance</button>
                <div class="attendance_dropdown-content">
                    <a href="{{ route('attendance.scan') }}">Attendance</a>
                    <a href="{{ route('attendance_logs.index') }}">Attendance Logs</a>
                    <a href="{{ route('attendance.changeVideo') }}">Change Video</a>
                    <a href="{{ route('attendance.feedback.settings') }}">Logout Feedback</a>
                    <a href="{{ route('admin.attendance.feedbacks') }}">View Feedback Responses</a>
                </div>
            </div>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Data</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('students.index') }}">Student Data</a>
                    <a href="{{ route('employees.index') }}">Faculty &amp; Staff Data</a>
                </div>
            </div>

            <a href="{{ route('landing') }}" class="btn2 btn-sm {{ request()->routeIs('books.landing') ? 'active-btn' : '' }}">OPAC</a>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Circulation</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('logs.index') }}">Circulation</a>
                    @can('isAdmin')
                    <a href="{{ route('fines.outstanding') }}">Outstanding Fines</a>
                    @endcan
                    <a href="{{ route('catalog.copy.openlibrary.form') }}">Copy Cataloging</a>
                    <a href="{{ route('rfid.scanner') }}" hidden>RFID Scanner</a>
                    <a href="{{ route('book.report.download') }}">Download Book Report</a>
                    @can('isAdmin')
                    <a href="{{ route('fines.edit') }}">Fines and Due Dates</a>
                    @endcan
                </div>
            </div>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Admin</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('feedback.index') }}">Student Feedback</a>
                    <a href="{{ route('files.index') }}">Repository</a>
                    <a href="{{ route('prospectus.index') }}">Prospectus Manager</a>
                    @can('isAdmin')
                    <a href="{{ route('users.index') }}">View Pantas Users</a>
                    <a href="{{ route('admin.catalog_frameworks.index') }}">MARC catalog frameworks</a>
                    <a href="{{ route('admin.catalog_select_options.index') }}">Catalog dropdown options</a>
                    @endcan
                </div>
            </div>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Room Reservations</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('rooms.index') }}">Manage Rooms</a>
                    <a href="{{ route('rooms.book') }}">Book a Room</a>
                    <a href="{{ route('rooms.schedule') }}">View Schedule</a>
                    <a href="{{ route('rooms.pending') }}">Pending Reservations</a>
                    <a href="{{ route('rooms.logs') }}">Reservation Logs</a>
                    @can('isAdmin')
                    <a href="{{ route('sms.page') }}">SMS Blast</a>
                    @endcan
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" id="logout" class="btn5">Logout</button>
            </form>
        </div>
    </div>

    <div class="head">
        <img src="{{ asset('images/Bannernew.jpg') }}" alt="Banner" class="banner-img">
    </div>

    <!-- PAGE CONTENT -->
    <main>
        <div class="container py-3">
            @yield('content')
        </div>
    </main>

    <!-- PAGE-SPECIFIC FOOTER -->
    @yield('footer')

    <script src="{{ asset('js/site-nav.js') }}"></script>
    @stack('scripts')

</body>
</html>
