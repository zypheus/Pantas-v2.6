<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\RoomReservation;

class ReservationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(RoomReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Room Reservation Has Been Approved')
                    ->view('emails.reservations.approved'); // ✅ simple Blade view
    }
}
