<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryRoomReservation extends Model
{
    protected $guarded = [];

    public function room(): BelongsTo
    {
        return $this->belongsTo(LibraryRoom::class, 'room_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(LibraryStudent::class, 'student_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(LibraryEmployee::class, 'employee_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(LibraryReservationStudent::class, 'room_reservation_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LibraryReservationLog::class, 'room_reservation_id');
    }
}
