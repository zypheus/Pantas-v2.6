@php
    $date = $reservation->date ? \Carbon\Carbon::parse($reservation->date) : null;
    $start = $reservation->start_time ? \Carbon\Carbon::parse($reservation->start_time) : null;
    $end = $reservation->end_time ? \Carbon\Carbon::parse($reservation->end_time) : null;
    $approvedAt = $reservation->approved_at ? \Carbon\Carbon::parse($reservation->approved_at) : null;
    $approverName = $reservation->approver
        ? trim($reservation->approver->fname . ' ' . $reservation->approver->lname)
        : null;
    $duration = ($start && $end) ? $start->diffInHours($end) : null;
    $appName = config('app.name', 'PANTAS');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reservation Approved</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#333333;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f6f8;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#1f4ea7 0%,#2e7d32 100%);padding:28px 32px;">
                            <p style="margin:0 0 6px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.85);">{{ $appName }} Library</p>
                            <h1 style="margin:0;font-size:24px;line-height:1.3;color:#ffffff;">Your room reservation is approved</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 32px 8px;">
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
                                Hello,
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;">
                                Your request to reserve a study room has been <strong style="color:#2e7d32;">approved</strong>.
                                Please review the details below and arrive on time for your booking.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 4px;font-size:12px;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Reservation</p>
                                        <p style="margin:0 0 12px;font-size:20px;font-weight:bold;color:#1f4ea7;">
                                            {{ $reservation->room->name ?? 'Room' }}
                                        </p>
                                        @if($date)
                                            <p style="margin:0;font-size:16px;line-height:1.5;">
                                                <strong>{{ $date->format('l, F j, Y') }}</strong>
                                            </p>
                                        @endif
                                        @if($start && $end)
                                            <p style="margin:6px 0 0;font-size:16px;line-height:1.5;">
                                                {{ $start->format('g:i A') }} – {{ $end->format('g:i A') }}
                                                @if($duration)
                                                    <span style="color:#64748b;">({{ $duration }} {{ $duration === 1 ? 'hour' : 'hours' }})</span>
                                                @endif
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:0 0 12px;font-size:16px;color:#1f4ea7;">Booking details</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;width:38%;font-size:14px;color:#64748b;">Confirmation #</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;font-weight:bold;">{{ $reservation->id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Room</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $reservation->room->name ?? '—' }}</td>
                                </tr>
                                @if($reservation->room?->description)
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Room info</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $reservation->room->description }}</td>
                                </tr>
                                @endif
                                @if($reservation->room?->capacity)
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Room capacity</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $reservation->room->capacity }} people</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Date</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $date ? $date->format('l, F j, Y') : '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Time</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">
                                        @if($start && $end)
                                            {{ $start->format('g:i A') }} – {{ $end->format('g:i A') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Contact email</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $reservation->patron_email ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Group size</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $reservation->number_of_students ?? '—' }} {{ ($reservation->number_of_students ?? 0) == 1 ? 'student' : 'students' }}</td>
                                </tr>
                                @if($approvedAt)
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Approved on</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $approvedAt->format('F j, Y g:i A') }}</td>
                                </tr>
                                @endif
                                @if($approverName)
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Approved by</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $approverName }}</td>
                                </tr>
                                @endif
                                @if($reservation->notes)
                                <tr>
                                    <td style="padding:10px 0;font-size:14px;color:#64748b;vertical-align:top;">Notes</td>
                                    <td style="padding:10px 0;font-size:14px;">{{ $reservation->notes }}</td>
                                </tr>
                                @endif
                            </table>

                            @if($reservation->students->isNotEmpty())
                            <h2 style="margin:0 0 12px;font-size:16px;color:#1f4ea7;">Attendees</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-bottom:24px;">
                                @foreach($reservation->students as $index => $student)
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;width:28px;color:#64748b;">{{ $index + 1 }}.</td>
                                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $student->name }}</td>
                                </tr>
                                @endforeach
                            </table>
                            @endif

                            <h2 style="margin:0 0 12px;font-size:16px;color:#1f4ea7;">Before you arrive</h2>
                            <ul style="margin:0 0 24px;padding-left:20px;font-size:14px;line-height:1.7;">
                                <li>Arrive at the library before your scheduled start time.</li>
                                <li>Check in with library staff at the circulation desk.</li>
                                <li>Keep noise levels appropriate for a shared study space.</li>
                                <li>Vacate the room promptly at the end of your time slot.</li>
                                <li>Contact the library if your plans change or you need to cancel.</li>
                            </ul>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin-bottom:8px;">
                                <tr>
                                    <td style="padding-right:10px;">
                                        <a href="{{ route('rooms.show', $reservation->id) }}" style="display:inline-block;padding:12px 18px;background-color:#1f4ea7;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:bold;">View reservation</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('rooms.schedule') }}" style="display:inline-block;padding:12px 18px;background-color:#ffffff;color:#1f4ea7;text-decoration:none;border-radius:6px;font-size:14px;font-weight:bold;border:1px solid #1f4ea7;">Room schedule</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 28px;border-top:1px solid #e2e8f0;background-color:#f8fafc;">
                            <p style="margin:0 0 6px;font-size:13px;color:#64748b;">
                                This message was sent to {{ $reservation->patron_email }} because a room reservation was approved for that address.
                            </p>
                            <p style="margin:0;font-size:13px;color:#64748b;">
                                Thanks,<br>
                                <strong style="color:#1f4ea7;">{{ $appName }} Library</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
