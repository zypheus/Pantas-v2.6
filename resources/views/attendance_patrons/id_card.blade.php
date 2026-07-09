<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance {{ $type }} ID</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: Arial, sans-serif; background: #eef2f7; color: #172033; }
        .card { width: 340px; min-height: 520px; border-radius: 22px; overflow: hidden; background: #fff; box-shadow: 0 20px 60px rgba(15, 23, 42, .22); }
        .head { padding: 22px; color: #fff; background: linear-gradient(135deg, #d97706, #f59e0b); text-align: center; }
        .head h1 { margin: 0; font-size: 22px; }
        .body { padding: 24px; text-align: center; }
        .photo { width: 118px; height: 118px; margin: 0 auto 16px; border-radius: 18px; object-fit: cover; background: #e5e7eb; display: grid; place-items: center; font-weight: 700; color: #64748b; }
        .photo img { width: 100%; height: 100%; border-radius: 18px; object-fit: cover; }
        .name { margin: 0 0 6px; font-size: 22px; font-weight: 800; }
        .meta { margin: 6px 0; color: #475569; }
        .qr { margin-top: 18px; padding: 14px; border: 2px dashed #cbd5e1; border-radius: 14px; font-size: 24px; font-weight: 900; letter-spacing: .08em; }
        .foot { padding: 14px 22px; background: #f8fafc; font-size: 12px; color: #64748b; text-align: center; }
        @media print { body { background: #fff; } .card { box-shadow: none; border: 1px solid #cbd5e1; } }
    </style>
</head>
<body>
    <main class="card">
        <div class="head">
            <h1>PANTAS Attendance</h1>
            <div>{{ $type }} ID Card</div>
        </div>
        <div class="body">
            <div class="photo">
                @if (! empty($person->{$photo}))
                    <img src="{{ asset($person->{$photo}) }}" alt="{{ $type }} photo">
                @else
                    PHOTO
                @endif
            </div>
            <h2 class="name">{{ $person->firstname }} {{ $person->middle_initial }} {{ $person->lastname }}</h2>
            <p class="meta">{{ $person->{$identifier} }}</p>
            <p class="meta">{{ $person->course ?? $person->department }} {{ $person->year ?? $person->position }}</p>
            <div class="qr">{{ $person->qrcode }}</div>
        </div>
        <div class="foot">Present this card when using the attendance scanner.</div>
    </main>
</body>
</html>
