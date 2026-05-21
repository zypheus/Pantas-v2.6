<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Room Reservation Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 600px;
            background: white;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        h2 { color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Room Reservation Approved ✅</h2>
        <p>Hello,</p>
        <p>Your room reservation has been <strong>approved</strong>.</p>

        <p><strong>Reservation Details:</strong></p>
        <ul>
            <li><b>Room:</b> {{ $reservation->room->name }}</li>
            <li><b>Date:</b> {{ \Carbon\Carbon::parse($reservation->date)->format('F d, Y') }}</li>
            <li><b>Time Slot:</b> {{ $reservation->time_slot }}</li>
            <li><b>Number of Students:</b> {{ $reservation->number_of_students }}</li>
        </ul>

        <p>You can view the schedule for more information.</p>

        <a href="{{ route('rooms.schedule') }}" class="btn">View Schedule</a>

        <p style="margin-top:30px;">Thanks,<br>PANTAS</p>
    </div>
</body>
</html>
