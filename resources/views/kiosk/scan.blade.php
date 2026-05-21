<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library kiosk — student lookup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(
                160deg,
                var(--brand-kiosk-gradient-from, #f8f9fa) 0%,
                var(--brand-kiosk-gradient-to, #e9ecef) 100%
            );
        }
        .kiosk-card { max-width: 440px; border-radius: 1rem; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.08); }
        .kiosk-logo { max-height: 48px; }
    </style>
</head>

<body>

<div class="container py-4 py-md-5">
    <div class="text-center mb-4">
        <img src="{{ asset('images/pantasLogo.png') }}" alt="Library" class="kiosk-logo mb-2">
        <h1 class="h4 mb-1">Student library account</h1>
        <p class="text-muted small mb-0">Type your <strong>Student ID</strong> (same as id number) or QR value, then press Enter.</p>
    </div>

    <div class="card kiosk-card mx-auto">
        <div class="card-body p-4">
            <label for="manualInput" class="form-label fw-semibold">Student ID or QR code</label>
            <input type="text"
                   id="manualInput"
                   class="form-control form-control-lg text-center mb-2"
                   placeholder="Student ID or QR code"
                   autocomplete="off"
                   autofocus>
            <small class="text-muted d-block text-center mb-0">Press <kbd>Enter</kbd> to open your loans and fines.</small>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('landing') }}" class="btn btn-outline-secondary btn-sm">← OPAC</a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Home</a>
    </div>
</div>

<script>
    (function () {
        var profileBase = @json(url('/student/qr'));
        var input = document.getElementById('manualInput');

        function goToProfile(code) {
            var v = String(code || '').trim();
            if (!v) return;
            window.location.href = profileBase + '/' + encodeURIComponent(v);
        }

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                goToProfile(input.value);
            }
        });

        // After browser "Back", bfcache can restore this page with old JS state; always allow a new lookup.
        window.addEventListener('pageshow', function () {
            input.focus();
            input.select();
        });
    })();
</script>

</body>
</html>
