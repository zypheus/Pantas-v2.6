<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class RoomReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'status',
        'date',
        'start_time',
        'end_time',
        'patron_email',
        'number_of_students',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $dates = ['date', 'approved_at'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function students()
    {
        return $this->hasMany(ReservationStudent::class, 'reservation_id');
    }

    public function logs()
    {
        return $this->hasMany(ReservationLog::class, 'reservation_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
