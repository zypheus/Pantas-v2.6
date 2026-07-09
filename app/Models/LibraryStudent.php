<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryStudent extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookLogs(): HasMany
    {
        return $this->hasMany(LibraryBookLog::class, 'student_id');
    }

    public function roomReservations(): HasMany
    {
        return $this->hasMany(LibraryRoomReservation::class, 'student_id');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(LibraryAttendanceLog::class, 'student_id');
    }
}
