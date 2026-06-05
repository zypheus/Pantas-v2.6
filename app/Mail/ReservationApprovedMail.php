<?php

namespace App\Mail;

use App\Models\RoomReservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public RoomReservation $reservation;

    public function __construct(RoomReservation $reservation)
    {
        $this->reservation = $reservation->loadMissing(['room', 'students', 'approver']);
    }

    public function build()
    {
        $room = $this->reservation->room?->name ?? 'Room';
        $date = Carbon::parse($this->reservation->date)->format('M j, Y');

        return $this->subject("Room reservation approved — {$room}, {$date}")
            ->view('emails.reservations.approved');
    }
}
