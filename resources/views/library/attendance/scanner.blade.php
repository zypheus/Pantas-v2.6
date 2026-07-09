<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Attendance Scanner</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="mx-auto" style="max-width: 720px;">
            <h1 class="h3 mb-2">Library Attendance Scanner</h1>
            <p class="text-muted mb-4">Scan a registered library patron QR code or ID number.</p>

            <div class="card">
                <div class="card-body">
                    <form id="scan-form" class="d-flex gap-2">
                        @csrf
                        <input class="form-control form-control-lg" name="qrcode" autocomplete="off" autofocus placeholder="Scan code">
                        <button class="btn btn-primary" type="submit">Scan</button>
                    </form>
                    <div id="scan-result" class="alert mt-3 d-none" role="status"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('scan-form').addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = event.currentTarget;
            const result = document.getElementById('scan-result');
            const data = new FormData(form);

            const response = await fetch(@json(route('library.attendance.scan')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': data.get('_token'),
                },
                body: data,
            });
            const payload = await response.json();
            result.className = 'alert mt-3 ' + (response.ok ? 'alert-success' : 'alert-danger');
            result.textContent = response.ok
                ? `${payload.patron.firstname} ${payload.patron.lastname}: ${payload.status}`
                : (payload.message || 'Scan failed.');
            form.reset();
            form.qrcode.focus();
        });
    </script>
</body>
</html>
