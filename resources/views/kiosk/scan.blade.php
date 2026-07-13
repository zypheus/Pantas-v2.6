<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library kiosk - student lookup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
    @include('components.branding-overrides')

    <style>
        :root {
            --kiosk-ink: var(--brand-text-dark, #22333b);
            --kiosk-primary: var(--brand-button-bg, #22333b);
            --kiosk-accent: var(--brand-button-hover-bg, #ffb845);
            --kiosk-panel: #ffffff;
            --kiosk-border: #e5e7eb;
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(160deg, rgba(255, 255, 255, 0.92) 0%, rgba(245, 247, 250, 0.9) 100%),
                linear-gradient(160deg, var(--brand-kiosk-gradient-from, #f8f9fa) 0%, var(--brand-kiosk-gradient-to, #e9ecef) 100%);
            color: var(--kiosk-ink);
        }

        .kiosk-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kiosk-header {
            border-bottom: 1px solid rgba(34, 51, 59, 0.12);
            margin-bottom: 2rem;
            padding-bottom: 1.25rem;
        }

        .kiosk-logo {
            max-height: 58px;
        }

        .kiosk-layout {
            display: grid;
            grid-template-columns: minmax(260px, 0.9fr) minmax(360px, 1.1fr);
            gap: 1.25rem;
            align-items: stretch;
            max-width: 1040px;
            margin: 0 auto;
        }

        .kiosk-info-panel,
        .kiosk-card {
            border: 1px solid var(--kiosk-border);
            border-radius: 1rem;
            background: var(--kiosk-panel);
            box-shadow: 0 1rem 2.5rem rgba(15, 23, 42, 0.08);
        }

        .kiosk-info-panel {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
        }

        .kiosk-info-panel::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: var(--kiosk-accent);
        }

        .kiosk-eyebrow {
            color: #5f6b76;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .kiosk-card {
            min-height: 100%;
        }

        .kiosk-card .card-body {
            padding: 2rem;
        }

        .kiosk-input {
            border: 2px solid #d8dee5;
            border-radius: 0.85rem;
            font-size: clamp(1.35rem, 3vw, 2rem);
            font-weight: 700;
            letter-spacing: 0.04em;
            min-height: 74px;
        }

        .kiosk-input:focus {
            border-color: var(--kiosk-primary);
            box-shadow: 0 0 0 0.25rem rgba(34, 51, 59, 0.14);
        }

        .kiosk-primary-btn {
            background: var(--kiosk-primary);
            border-color: var(--kiosk-primary);
            border-radius: 0.75rem;
            color: #ffffff;
            font-weight: 800;
            min-height: 56px;
        }

        .kiosk-primary-btn:hover,
        .kiosk-primary-btn:focus {
            background: var(--kiosk-accent);
            border-color: var(--kiosk-accent);
            color: var(--kiosk-primary);
        }

        .kiosk-status {
            min-height: 1.5rem;
        }

        .kiosk-nav {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .kiosk-nav .btn {
            border-radius: 0.7rem;
            font-weight: 700;
            min-width: 110px;
            padding: 0.65rem 1rem;
        }

        @media (max-width: 900px) {
            .kiosk-shell {
                justify-content: flex-start;
            }

            .kiosk-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container kiosk-shell py-4 py-md-5">
    <div class="kiosk-header text-center">
        <img src="{{ $brandingSidebarLogoUrl }}" alt="Library" class="kiosk-logo mb-2">
        <h1 class="h3 fw-bold mb-1">Student Account Lookup</h1>
        <p class="text-muted mb-0">Scan your QR code or enter your Student ID to view your loans and fines.</p>
    </div>

    <div class="kiosk-layout">
        <section class="kiosk-info-panel">
            <div>
                <p class="kiosk-eyebrow mb-2">Library kiosk</p>
                <h2 class="h4 fw-bold mb-3">Quickly open your library account.</h2>
                <p class="text-muted mb-0">
                    Check your borrowed books, fines, and account status using your Student ID or QR code.
                </p>
            </div>
            <div class="mt-4 pt-4 border-top">
                <p class="small text-muted mb-1">For the fastest lookup</p>
                <p class="fw-semibold mb-0">Tap the field, scan your QR code, or type your Student ID.</p>
            </div>
        </section>

        <section class="card kiosk-card">
            <div class="card-body">
                <form id="lookupForm" novalidate>
                    <label for="manualInput" class="form-label fw-bold">Student ID or QR code</label>
                    <input type="text"
                           id="manualInput"
                           class="form-control kiosk-input text-center mb-3"
                           placeholder="Student ID or QR code"
                           autocomplete="off"
                           autofocus>
                    <button type="submit" id="lookupButton" class="btn kiosk-primary-btn w-100">
                        Open Account
                    </button>
                    <p id="lookupStatus" class="kiosk-status small text-center text-muted mb-0 mt-3">
                        Press Enter or tap Open Account to continue.
                    </p>
                </form>
            </div>
        </section>
    </div>

    <div class="kiosk-nav">
        <a href="{{ route('landing') }}" class="btn btn-outline-secondary">OPAC</a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Home</a>
    </div>
</div>

<script>
    (function () {
        var profileBase = @json(url('/student/qr'));
        var form = document.getElementById('lookupForm');
        var input = document.getElementById('manualInput');
        var button = document.getElementById('lookupButton');
        var status = document.getElementById('lookupStatus');

        function setStatus(message, className) {
            status.textContent = message;
            status.className = 'kiosk-status small text-center mb-0 mt-3 ' + className;
        }

        function goToProfile(code) {
            var value = String(code || '').trim();

            if (!value) {
                setStatus('Please enter your Student ID or scan your QR code.', 'text-danger');
                input.focus();
                return;
            }

            button.disabled = true;
            button.textContent = 'Opening...';
            setStatus('Opening your account...', 'text-muted');
            window.location.href = profileBase + '/' + encodeURIComponent(value);
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            goToProfile(input.value);
        });

        window.addEventListener('pageshow', function () {
            button.disabled = false;
            button.textContent = 'Open Account';
            setStatus('Press Enter or tap Open Account to continue.', 'text-muted');
            input.focus();
            input.select();
        });
    })();
</script>

</body>
</html>
