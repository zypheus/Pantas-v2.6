<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Pantas Library')</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    {{-- Branding tokens --}}
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">

    {{-- Vite: Tailwind v4 + Flowbite compiled CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Sidebar custom styles --}}
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    @stack('styles')
    @yield('styles')
</head>

<body class="sidebar-layout sidebar-hydrating">

    {{-- ========================================================
         MOBILE TOP BAR (visible only on small screens)
    ========================================================= --}}
    <header class="sidebar-topbar d-flex align-items-center d-md-none px-3">
        <button id="sidebarToggle" class="sidebar-hamburger me-3" aria-label="Open menu" aria-expanded="false" aria-controls="sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>
        <img src="{{ asset('images/pantasLogo.png') }}" alt="Pantas Logo" class="sidebar-topbar-logo me-2">
        <span class="sidebar-topbar-title fw-semibold">@yield('title', 'Pantas Library')</span>
    </header>

    {{-- Mobile overlay backdrop --}}
    <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

    {{-- ========================================================
         SIDEBAR WRAPPER
    ========================================================= --}}
    <div class="sidebar-wrapper">

        {{-- LEFT SIDEBAR --}}
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Dashboard navigation">
            @include('components.sidebar-nav')
        </aside>

        <script>
            (function () {
                const groupsKey = 'pantas_sidebar_open_groups';
                const scrollKey = 'pantas_sidebar_scroll_top';

                function readJson(key, fallback) {
                    try {
                        return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
                    } catch (_) {
                        return fallback;
                    }
                }

                function readNumber(key, fallback) {
                    try {
                        const value = Number(localStorage.getItem(key) || fallback);
                        return Number.isFinite(value) ? value : fallback;
                    } catch (_) {
                        return fallback;
                    }
                }

                const savedOpen = readJson(groupsKey, []);

                document.querySelectorAll('.sidebar-group-label').forEach(function (label) {
                    const groupId = label.dataset.group;
                    const items = document.getElementById('sidebar-group-' + groupId);
                    if (!items) return;

                    const hasActive = items.querySelector('.sidebar-link.active') !== null;
                    if (hasActive || savedOpen.includes(groupId)) {
                        label.classList.add('open');
                        items.classList.add('open');
                        label.setAttribute('aria-expanded', 'true');
                    }
                });

                requestAnimationFrame(function () {
                    const scroller = document.querySelector('#sidebar .sidebar-nav') || document.getElementById('sidebar');
                    if (scroller) {
                        scroller.scrollTop = readNumber(scrollKey, 0);
                    }
                    document.body.classList.remove('sidebar-hydrating');
                });
            }());
        </script>

        {{-- MAIN CONTENT AREA --}}
        <main class="sidebar-content">
            {{-- Page-level header / breadcrumb (optional) --}}
            @hasSection('header')
                <div class="sidebar-page-header px-4 pt-3 pb-2 border-bottom">
                    @yield('header')
                </div>
            @endif

            <div class="sidebar-page-body px-4 py-3">
                @yield('content')
            </div>

            {{-- Footer — sidebar layout provides default; pages can override via @section('footer') --}}
            @hasSection('footer')
                @yield('footer')
            @endif
        </main>

    </div>{{-- /.sidebar-wrapper --}}

    {{-- Bootstrap 5 JS (needed for any Bootstrap components on content pages) --}}
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- jQuery (available globally for existing page scripts) --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all forms that submit to logout
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure you want to log out?',
                        text: "You will be returned to the login screen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1f4ea7', // matching brand-navy color
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, logout',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    {{-- Sidebar behaviour: collapse toggle, localStorage state, mobile overlay --}}
    <script src="{{ asset('js/sidebar.js') }}"></script>

    @stack('scripts')
    @yield('scripts')

</body>
</html>
