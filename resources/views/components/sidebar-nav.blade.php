@php
    $moduleAccess = app(\App\Services\Auth\ModuleAccessService::class);
    $user = Auth::user();
    $availableModules = $user ? $moduleAccess->availableModules($user) : [];
    $activeModule = session('active_module');
    $canLibrary = $user && $moduleAccess->hasLibraryAccess($user);
    $canLibraryAdmin = $user && $moduleAccess->hasLibraryAdminAccess($user);
    $canAttendance = $user && $moduleAccess->hasAttendanceAccess($user);
    $canAttendanceAdmin = $user && $moduleAccess->hasAttendanceAdminAccess($user);
    $canSuperAdmin = $user && $moduleAccess->isSuperAdmin($user);
@endphp

<div class="sidebar-header">
    <a href="{{ route('dashboard') }}" class="sidebar-logo-wrap">
        <img src="{{ asset('images/pantasLogo.png') }}" alt="Pantas Logo" class="sidebar-logo-img">
        <span class="sidebar-app-name">Pantas</span>
    </a>
    <div class="sidebar-user-info">
        <span class="sidebar-user-name">{{ $user->name ?? 'User' }}</span>
        <span class="sidebar-role-badge">{{ str_replace('_', ' ', ucfirst($user->role ?? 'staff')) }}</span>
    </div>

    @if (count($availableModules) > 1)
        <div class="mt-3 d-grid gap-2">
            @foreach ($availableModules as $availableModule)
                <form method="POST" action="{{ route('module.switch') }}" class="m-0">
                    @csrf
                    <input type="hidden" name="module" value="{{ $availableModule }}">
                    <button type="submit"
                            class="btn btn-sm w-100 {{ $activeModule === $availableModule ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ str_replace('-', ' ', ucfirst($availableModule)) }}
                    </button>
                </form>
            @endforeach
        </div>
    @endif
</div>

<nav class="sidebar-nav" aria-label="Sidebar navigation">
    @if ($canSuperAdmin)
        <button class="sidebar-group-label" data-group="system" aria-expanded="false" aria-controls="sidebar-group-system">
            <span><i class="bi bi-shield-lock sidebar-group-icon"></i>System</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </button>
        <ul class="sidebar-group-items" id="sidebar-group-system" role="list">
            <li>
                <a href="{{ route('dashboard.super-admin') }}" class="sidebar-link {{ request()->routeIs('dashboard.super-admin') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Super Admin Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Staff Accounts
                </a>
            </li>
        </ul>
    @endif

    @if ($canLibrary)
        <button class="sidebar-group-label" data-group="library" aria-expanded="false" aria-controls="sidebar-group-library">
            <span><i class="bi bi-book sidebar-group-icon"></i>Library</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </button>
        <ul class="sidebar-group-items" id="sidebar-group-library" role="list">
            <li>
                <a href="{{ route($canLibraryAdmin ? 'dashboard.library-admin' : 'dashboard.library-staff') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard.library-*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Library Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('book.index') }}" class="sidebar-link {{ request()->routeIs('book.*', 'books.*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i> Books
                </a>
            </li>
            <li>
                <a href="{{ route('ebooks.index') }}" class="sidebar-link {{ request()->routeIs('ebooks.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> eBooks
                </a>
            </li>
            <li>
                <a href="{{ route('catalog.copy.openlibrary.form') }}" class="sidebar-link {{ request()->routeIs('catalog.copy.*') ? 'active' : '' }}">
                    <i class="bi bi-copy"></i> Copy Cataloging
                </a>
            </li>
            <li>
                <a href="{{ route('feedback.index') }}" class="sidebar-link {{ request()->routeIs('feedback.index') ? 'active' : '' }}">
                    <i class="bi bi-star"></i> Library Feedback
                </a>
            </li>
        </ul>

        @if ($canLibraryAdmin)
            <button class="sidebar-group-label" data-group="library-admin" aria-expanded="false" aria-controls="sidebar-group-library-admin">
                <span><i class="bi bi-sliders sidebar-group-icon"></i>Library Admin</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <ul class="sidebar-group-items" id="sidebar-group-library-admin" role="list">
                <li>
                    <a href="{{ route('logs.index') }}" class="sidebar-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Circulation Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Library Students
                    </a>
                </li>
                <li>
                    <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="bi bi-person-workspace"></i> Library Employees
                    </a>
                </li>
                <li>
                    <a href="{{ route('pending.index', ['tab' => 'students']) }}" class="sidebar-link {{ request()->routeIs('pending.*') ? 'active' : '' }}">
                        <i class="bi bi-person-plus"></i> Pending Patrons
                    </a>
                </li>
                <li>
                    <a href="{{ route('rooms.index') }}" class="sidebar-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> Rooms
                    </a>
                </li>
                <li>
                    <a href="{{ route('rooms.pending') }}" class="sidebar-link {{ request()->routeIs('rooms.pending') ? 'active' : '' }}">
                        <i class="bi bi-hourglass-split"></i> Pending Room Reservations
                    </a>
                </li>
                <li>
                    <a href="{{ route('files.index') }}" class="sidebar-link {{ request()->routeIs('files.*') ? 'active' : '' }}">
                        <i class="bi bi-folder2-open"></i> Repository
                    </a>
                </li>
                <li>
                    <a href="{{ route('prospectus.index') }}" class="sidebar-link {{ request()->routeIs('prospectus.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-bookmark"></i> Prospectus
                    </a>
                </li>
                <li>
                    <a href="{{ route('fines.outstanding') }}" class="sidebar-link {{ request()->routeIs('fines.*') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-circle"></i> Fines
                    </a>
                </li>
                <li>
                    <a href="{{ route('sms.page') }}" class="sidebar-link {{ request()->routeIs('sms.*', 'sms.page') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i> SMS
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.catalog_frameworks.index') }}" class="sidebar-link {{ request()->routeIs('admin.catalog_frameworks.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i> MARC Frameworks
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.catalog_select_options.index') }}" class="sidebar-link {{ request()->routeIs('admin.catalog_select_options.*') ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i> Catalog Options
                    </a>
                </li>
            </ul>
        @endif
    @endif

    @if ($canAttendance)
        <button class="sidebar-group-label" data-group="attendance" aria-expanded="false" aria-controls="sidebar-group-attendance">
            <span><i class="bi bi-clock-history sidebar-group-icon"></i>Attendance</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </button>
        <ul class="sidebar-group-items" id="sidebar-group-attendance" role="list">
            <li>
                <a href="{{ route($canAttendanceAdmin ? 'dashboard.attendance-admin' : 'dashboard.attendance-staff') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard.attendance-*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Attendance Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.scan') }}" class="sidebar-link {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                    <i class="bi bi-upc-scan"></i> Scanner
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.changeVideo') }}" class="sidebar-link {{ request()->routeIs('attendance.changeVideo') ? 'active' : '' }}">
                    <i class="bi bi-camera-video"></i> Change Video
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.feedback.settings') }}" class="sidebar-link {{ request()->routeIs('attendance.feedback.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Logout Feedback
                </a>
            </li>
            @if ($canAttendanceAdmin)
                <li>
                    <a href="{{ route('attendance_logs.index') }}" class="sidebar-link {{ request()->routeIs('attendance_logs.index') ? 'active' : '' }}">
                        <i class="bi bi-list-check"></i> Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('attendance_logs.reports.hub') }}" class="sidebar-link {{ request()->routeIs('attendance_logs.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attendance.feedbacks') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.feedbacks') ? 'active' : '' }}">
                        <i class="bi bi-chat-square-text"></i> Feedback Responses
                    </a>
                </li>
            @endif
        </ul>
    @endif

    <hr class="sidebar-divider">

    @if ($canLibrary)
        <a href="{{ route('landing') }}" class="sidebar-direct-link {{ request()->routeIs('landing') ? 'active' : '' }}">
            <i class="bi bi-search"></i> OPAC
        </a>
    @endif
</nav>

<div class="sidebar-footer-actions">
    <form method="POST" action="{{ route('logout') }}" class="flex-1 m-0">
        @csrf
        <button type="submit" class="btn-logout w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>
