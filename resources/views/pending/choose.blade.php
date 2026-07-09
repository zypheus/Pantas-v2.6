<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patron Registration</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="mx-auto" style="max-width: 720px;">
            <div class="mb-4">
                <h1 class="h3 mb-2">Choose Registration Type</h1>
                <p class="text-muted mb-0">Select the module where your patron record should be reviewed and approved.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <a class="card h-100 text-decoration-none text-dark" href="{{ route('library.register') }}">
                        <div class="card-body">
                            <h2 class="h5">Library Registration</h2>
                            <p class="text-muted mb-0">For borrowing, room reservations, OPAC services, and library visit logs.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="card h-100 text-decoration-none text-dark" href="{{ route('attendance.register') }}">
                        <div class="card-body">
                            <h2 class="h5">Attendance Registration</h2>
                            <p class="text-muted mb-0">For campus attendance scans, attendance reports, and attendance feedback.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
