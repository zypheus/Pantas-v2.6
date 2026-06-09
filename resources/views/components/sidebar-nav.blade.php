{{--
    components/sidebar-nav.blade.php
    Full sidebar navigation for Admin + Staff dashboard.
    Groups use data-group="<id>" matched by sidebar.js.
    Active link detection uses request()->routeIs().
--}}

{{-- =========================================================================
     HEADER: Logo + user info
========================================================================== --}}
<div class="sidebar-header">
    <a href="{{ route('book.index') }}" class="sidebar-logo-wrap">
        <img src="{{ asset('images/pantasLogo.png') }}" alt="Pantas Logo" class="sidebar-logo-img">
        <span class="sidebar-app-name">Pantas Library</span>
    </a>
    <div class="sidebar-user-info">
        <span class="sidebar-user-name">{{ Auth::user()->name ?? 'User' }}</span>
        <span class="sidebar-role-badge">{{ ucfirst(Auth::user()->role ?? 'staff') }}</span>
    </div>
</div>

{{-- =========================================================================
     NAV GROUPS
========================================================================== --}}
<nav class="sidebar-nav" aria-label="Sidebar navigation">

    {{-- -----------------------------------------------------------------------
         BOOKS
    ----------------------------------------------------------------------- --}}
    <button class="sidebar-group-label" data-group="books" aria-expanded="false" aria-controls="sidebar-group-books">
        <span><i class="bi bi-book sidebar-group-icon"></i>Books</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-books" role="list">
        <li>
            <a href="{{ route('book.index') }}"
               class="sidebar-link {{ request()->routeIs('book.index') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> All Books
            </a>
        </li>
        <li>
            <a href="{{ route('books.archived') }}"
               class="sidebar-link {{ request()->routeIs('books.archived') ? 'active' : '' }}">
                <i class="bi bi-archive"></i> Archived Books
            </a>
        </li>
        <li>
            <a href="{{ route('books.trash') }}"
               class="sidebar-link {{ request()->routeIs('books.trash') ? 'active' : '' }}">
                <i class="bi bi-trash"></i> Trash
            </a>
        </li>
        <li>
            <a href="{{ route('ebooks.index') }}"
               class="sidebar-link {{ request()->routeIs('ebooks.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> eBooks
            </a>
        </li>
    </ul>

    {{-- -----------------------------------------------------------------------
         CIRCULATION
    ----------------------------------------------------------------------- --}}
    <button class="sidebar-group-label" data-group="circulation" aria-expanded="false" aria-controls="sidebar-group-circulation">
        <span><i class="bi bi-arrow-left-right sidebar-group-icon"></i>Circulation</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-circulation" role="list">
        @can('isAdmin')
        <li>
            <a href="{{ route('logs.index') }}"
               class="sidebar-link {{ request()->routeIs('logs.index') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Circulation Logs
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('fines.outstanding') }}"
               class="sidebar-link {{ request()->routeIs('fines.outstanding') ? 'active' : '' }}">
                <i class="bi bi-exclamation-circle"></i> Outstanding Fines
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('catalog.copy.openlibrary.form') }}"
               class="sidebar-link {{ request()->routeIs('catalog.copy.*') ? 'active' : '' }}">
                <i class="bi bi-copy"></i> Copy Cataloging
            </a>
        </li>
        <li>
            <a href="{{ route('book.report.download') }}"
               class="sidebar-link">
                <i class="bi bi-download"></i> Download Book Report
            </a>
        </li>
        @can('isAdmin')
        <li>
            <a href="{{ route('fines.edit') }}"
               class="sidebar-link {{ request()->routeIs('fines.edit') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i> Fines &amp; Due Dates
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
    </ul>

    {{-- -----------------------------------------------------------------------
         PEOPLE (Admin only group)
    ----------------------------------------------------------------------- --}}
    @can('isAdmin')
    <button class="sidebar-group-label" data-group="people" aria-expanded="false" aria-controls="sidebar-group-people">
        <span><i class="bi bi-people sidebar-group-icon"></i>People</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-people" role="list">
        <li>
            <a href="{{ route('students.index') }}"
               class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Student Data
            </a>
        </li>
        <li>
            <a href="{{ route('employees.index') }}"
               class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i> Faculty &amp; Staff Data
            </a>
        </li>
        <li>
            <a href="{{ route('students.pending') }}"
               class="sidebar-link {{ request()->routeIs('students.pending') ? 'active' : '' }}">
                <i class="bi bi-person-plus"></i> Pending Students
            </a>
        </li>
        <li>
            <a href="{{ route('pending.employees') }}"
               class="sidebar-link {{ request()->routeIs('pending.employees') ? 'active' : '' }}">
                <i class="bi bi-person-plus"></i> Pending Employees
            </a>
        </li>
    </ul>
    @endcan

    {{-- -----------------------------------------------------------------------
         ATTENDANCE
    ----------------------------------------------------------------------- --}}
    <button class="sidebar-group-label" data-group="attendance" aria-expanded="false" aria-controls="sidebar-group-attendance">
        <span><i class="bi bi-clock-history sidebar-group-icon"></i>Attendance</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-attendance" role="list">
        <li>
            <a href="{{ route('attendance.scan') }}"
               class="sidebar-link {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                <i class="bi bi-upc-scan"></i> Attendance Scanner
            </a>
        </li>
        @can('isAdmin')
        <li>
            <a href="{{ route('attendance_logs.index') }}"
               class="sidebar-link {{ request()->routeIs('attendance_logs.index') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> Attendance Logs
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('attendance_logs.reports.hub') }}"
               class="sidebar-link {{ request()->routeIs('attendance_logs.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> Reports
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('attendance.changeVideo') }}"
               class="sidebar-link {{ request()->routeIs('attendance.changeVideo') ? 'active' : '' }}">
                <i class="bi bi-camera-video"></i> Change Video
            </a>
        </li>
        <li>
            <a href="{{ route('attendance.feedback.settings') }}"
               class="sidebar-link {{ request()->routeIs('attendance.feedback.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Logout Feedback
            </a>
        </li>
        @can('isAdmin')
        <li>
            <a href="{{ route('admin.attendance.feedbacks') }}"
               class="sidebar-link {{ request()->routeIs('admin.attendance.feedbacks') ? 'active' : '' }}">
                <i class="bi bi-chat-square-text"></i> Feedback Responses
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
    </ul>

    {{-- -----------------------------------------------------------------------
         CONTENT
    ----------------------------------------------------------------------- --}}
    <button class="sidebar-group-label" data-group="content" aria-expanded="false" aria-controls="sidebar-group-content">
        <span><i class="bi bi-folder sidebar-group-icon"></i>Content</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-content" role="list">
        <li>
            <a href="{{ route('feedback.index') }}"
               class="sidebar-link {{ request()->routeIs('feedback.index') ? 'active' : '' }}">
                <i class="bi bi-star"></i> Student Feedback
            </a>
        </li>
        @can('isAdmin')
        <li>
            <a href="{{ route('files.index') }}"
               class="sidebar-link {{ request()->routeIs('files.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i> File Repository
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('prospectus.index') }}"
               class="sidebar-link {{ request()->routeIs('prospectus.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i> Prospectus Manager
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
    </ul>

    {{-- -----------------------------------------------------------------------
         ROOM RESERVATIONS
    ----------------------------------------------------------------------- --}}
    <button class="sidebar-group-label" data-group="rooms" aria-expanded="false" aria-controls="sidebar-group-rooms">
        <span><i class="bi bi-door-open sidebar-group-icon"></i>Room Reservations</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-rooms" role="list">
        @can('isAdmin')
        <li>
            <a href="{{ route('rooms.index') }}"
               class="sidebar-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Manage Rooms
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('rooms.book') }}"
               class="sidebar-link {{ request()->routeIs('rooms.book') ? 'active' : '' }}">
                <i class="bi bi-calendar-plus"></i> Book a Room
            </a>
        </li>
        <li>
            <a href="{{ route('rooms.schedule') }}"
               class="sidebar-link {{ request()->routeIs('rooms.schedule') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> View Schedule
            </a>
        </li>
        @can('isAdmin')
        <li>
            <a href="{{ route('rooms.pending') }}"
               class="sidebar-link {{ request()->routeIs('rooms.pending') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i> Pending Reservations
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('rooms.logs') }}"
               class="sidebar-link {{ request()->routeIs('rooms.logs') ? 'active' : '' }}">
                <i class="bi bi-clock"></i> Reservation Logs
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('sms.page') }}"
               class="sidebar-link {{ request()->routeIs('sms.page') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> SMS Blast
                <span class="sidebar-admin-badge">Admin</span>
            </a>
        </li>
        @endcan
    </ul>

    {{-- -----------------------------------------------------------------------
         SYSTEM SETTINGS (Admin only group)
    ----------------------------------------------------------------------- --}}
    @can('isAdmin')
    <button class="sidebar-group-label" data-group="settings" aria-expanded="false" aria-controls="sidebar-group-settings">
        <span><i class="bi bi-gear sidebar-group-icon"></i>System Settings</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </button>
    <ul class="sidebar-group-items" id="sidebar-group-settings" role="list">
        <li>
            <a href="{{ route('users.index') }}"
               class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Pantas Users
            </a>
        </li>
        <li>
            <a href="{{ route('admin.catalog_frameworks.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.catalog_frameworks.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i> MARC Frameworks
            </a>
        </li>
        <li>
            <a href="{{ route('admin.catalog_select_options.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.catalog_select_options.*') ? 'active' : '' }}">
                <i class="bi bi-list-ul"></i> Catalog Options
            </a>
        </li>
    </ul>
    @endcan

    <hr class="sidebar-divider">

    {{-- -----------------------------------------------------------------------
         OPAC — direct link
    ----------------------------------------------------------------------- --}}
    <a href="{{ route('landing') }}" class="sidebar-direct-link {{ request()->routeIs('landing') ? 'active' : '' }}">
        <i class="bi bi-search"></i> OPAC
    </a>

</nav>

{{-- =========================================================================
     FOOTER: Profile + Logout
========================================================================== --}}
<div class="sidebar-footer-actions">
    <span class="btn-profile">
        <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? '' }}
    </span>
    <form method="POST" action="{{ route('logout') }}" class="flex-1 m-0">
        @csrf
        <button type="submit" class="btn-logout w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>
