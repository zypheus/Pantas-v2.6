<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryReservationStudent extends Model
{
    protected $guarded = [];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(LibraryRoomReservation::class, 'room_reservation_id');
    }
}
