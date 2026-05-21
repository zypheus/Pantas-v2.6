<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>📚 Book Kiosk</title>

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">

    @stack('styles')
    @yield('styles')

    <style>
        html, body { height: 100%; }
        body { display: flex; flex-direction: column; }
        main { flex: 1; }
    </style>
</head>
<body>
    <div class="d-flex align-items-center px-4 py-2 flex-wrap" style="background-color: white; position: relative;">
        <h1 class="school-name mb-0 ms-2"></h1>
    </div>
    <main>
        <div class="container py-3">
            @yield('content')
        </div>
    </main>
    @yield('footer')
    @stack('scripts')
</body>
</html>
