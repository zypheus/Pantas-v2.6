<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function reservation()
    {
        return $this->belongsTo(RoomReservation::class, 'reservation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
