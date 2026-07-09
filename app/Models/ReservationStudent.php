<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationStudent extends Model
{
    use HasFactory;

    protected $table = 'library_reservation_students';

    protected $fillable = [
        'reservation_id',
        'name',
    ];

    public function reservation()
    {
        return $this->belongsTo(RoomReservation::class, 'reservation_id');
    }
}
